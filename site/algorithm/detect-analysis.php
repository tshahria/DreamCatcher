
<?php
    /*
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     * http://www.apache.org/licenses/LICENSE-2.0
     */

    require __DIR__ . '/vendor/autoload.php';
    
    //Imports the Google Cloud client library
    use Google\Cloud\Vision\VisionClient;
    use Google\Cloud\Storage\StorageClient;
    use PhpFanatic\clarifAI\ImageClient;

    //Path to the image for analysis
    $path = $_GET['link'];

    //User ID
    if(isset($_GET['userID'])){
        $userID = $_GET['userID'];
    }else{
        $userID = "";s
    }

    //Google Vision API and Storage Bucket Info
    $projectId = 'Google Project Id';
    $bucket = 'name of the google sotrage bucket to store the pictures';
    $serviceAccountPath = 'path to your credential json file';

    //Clarfai API Info
    $clarfaiAPI = 'Clarifai API Key';
    $clarfaiModelName = 'Clarifai Model Name';


    //Functions that uses Google Vision API to get the vertices of the faces of an image
    function detect_face()
    {
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $vision = new VisionClient($config);
        $image = $vision->image(file_get_contents($path), ['FACE_DETECTION']);
        $result = $vision->annotate($image);
        $arrR = (array)$result;
        reset($arrR);
        $key = array_keys($arrR)[0];
        $attributesArr = $arrR[$key]["faceAnnotations"];
        $bPolyArrays = array();
        foreach ($attributesArr as $tempA) {
            array_push($bPolyArrays, $tempA["boundingPoly"]);
        }
        return $bPolyArrays;
    }
    

    //Functions to genetare picture in Base64 format and get the cropped faces in an image
    function generatePicB64($pathName, $xStart, $yStart, $xSize, $ySize){
        $filename = $pathName;
        $im = imagecreatefromjpeg($filename);
        $im2 = imagecrop($im, ['x' => $xStart, 'y' => $yStart, 'width' => $xSize, 'height' => $ySize]);
        if ($im2 !== FALSE) {
            ob_start(); 
            imagejpeg($im2); 
            $contents = ob_get_contents(); 
            ob_end_clean();             
            $dataUri = "data:image/jpeg;base64," . base64_encode($contents);        
        }else{
            echo"failed";
        }
        return $dataUri;
    }

    function getCroppedImage($bPA){
        $dataArr = array();
        for ($i=0; $i < sizeof($bPA); $i++) { 
            $vx1 = $bPA[$i]["vertices"][0]["x"];
            $vy1 = $bPA[$i]["vertices"][0]["y"];
            $vx2 = $bPA[$i]["vertices"][1]["x"];
            $vy2 = $bPA[$i]["vertices"][1]["y"];
            $vx3 = $bPA[$i]["vertices"][2]["x"];
            $vy3 = $bPA[$i]["vertices"][2]["y"];
            $vx4 = $bPA[$i]["vertices"][3]["x"];
            $vy4 = $bPA[$i]["vertices"][3]["y"];
            $xlen = abs($vx2-$vx1);
            $ylen = abs($vy3-$vy1);
            $dataTempURI = generatePicB64($path, $vx1, $vy1, $xlen, $ylen);
            array_push($dataArr, $dataTempURI);
        }
        return $dataArr;
    }


    //Functions for Uploading Objects to Google Storage Bucket
    function upload_object($objectName, $source)
    {
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $storage = new StorageClient($config);
        $file = fopen($source, 'r');
        $bucket = $storage->bucket($bucket);
        $object = $bucket->upload($file, [
            'name' => $objectName
        ]);
        $object2 = $bucket->object($objectName);
        $object2->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
    }

    function uploadBatchToGS($dataA){
        $arr = array();
        for ($i=0; $i <sizeof($dataA) ; $i++) { 
            $objName = "P -" . $userID. "-" .($i+1).".jpg";
            upload_object($objName, $dataA[$i]);
            array_push($arr, $objName);
        }
        return $arr;
    }
       
    //Functions for analyzing a batch of images of people (awake vs sleeping) 
    function batchCAnalysis($objList){
        $client = new ImageClient($clarfaiAPI);
        $totalScore = 0;
        $totalSleepScore = 0;
        $totalEngageScore = 0;
        $avgSleepScore = 0;
        $avgEngageScore = 0;
        $objLsize = sizeof($objList);
        for ($i=0; $i < $objLsize; $i++) {
            $imgLink = "https://storage.googleapis.com/". $bucket ."/" . $objList[$i];
            $client->AddImage($imgLink);
            $result = $client->Predict($clarfaiModelName);
            $arrResult = (array)((array)(((array)json_decode($result))['outputs'])[0]);
            $arr2 = (array)(((array)$arrResult['data'])['concepts']);
            if(strcmp(((array)$arr2[0])['id'],"sleeping")){
                $engageScore = ((array)$arr2[0])['value'];
                $sleepyScore = ((array)$arr2[1])['value'];
            }else{
                $sleepyScore = ((array)$arr2[0])['value'];
                $engageScore = ((array)$arr2[1])['value'];
            }
            //var_dump($arr2); 
            //echo "sleepyScore $i : $sleepyScore";
            //echo "engageScore $i: $engageScore";            
            $totalScore += ($sleepyScore+$engageScore);
            $totalSleepScore += $sleepyScore;
            $totalEngageScore += $engageScore;
        }
        $avgSleepScore = $totalSleepScore/$objLsize;
        $avgEngageScore = $totalEngageScore/$objLsize;
        $percentSleepScore = $totalSleepScore/$totalScore;
        $percentEngageScore = $totalEngageScore/$totalScore;
        $analysisArray = array( 'avgSleepScore' => $avgSleepScore*100,
                                'avgEngageScore' => $avgEngageScore*100,
                                'percentSleepScore' => $percentSleepScore,
                                'percentEngageScore' => $percentEngageScore);        
        return $analysisArray;
    }

    //function deteleBatch($nameArr)

    //function drawFaces($img, $vertices)
    //F: img -> img with face rectangles

    $vertexJson = detect_face();
    $finalAnalysis = batchCAnalysis(uploadBatchToGS(getCroppedImage($vertexJson)));
    //var_dump($finalAnalysis);
?>
<html>
<head>
    <style type="text/css">
        body{text-align: center;}
        .img{position:absolute;z-index:1;}
        #container{
            display:inline-block;
            width:1920px; 
            height:1080px;
           }
        #myCanvas{
            position:relative;
            z-index:20;
        }

    </style>
</head>
<body>
    <script type="text/javascript">
        <?php echo "var fAnalysis = " . json_encode($finalAnalysis) . ";";?>
        console.log(fAnalysis);
    </script>
        <div class="container">
            <img class="img" src="<?php echo"$path"?>">
            <canvas id="myCanvas" width="1920" height="1080">
            <script type="text/javascript">
                <?php
                    $js_array = json_encode($vertexJson);
                    echo "var jArr = ". $js_array . ";\n";
                ?>
                var ctx=document.getElementById("myCanvas").getContext("2d");
                for(var i=0; i<jArr.length; i++){
                    var tempValx1 = jArr[i]["vertices"][0]["x"];
                    var tempValy1 = jArr[i]["vertices"][0]["y"];
                    var tempValx2 = jArr[i]["vertices"][1]["x"];
                    var tempValy2 = jArr[i]["vertices"][1]["y"];
                    var tempValx3 = jArr[i]["vertices"][2]["x"];
                    var tempValy3 = jArr[i]["vertices"][2]["y"];
                    var tempValx4 = jArr[i]["vertices"][3]["x"];
                    var tempValy4 = jArr[i]["vertices"][3]["y"];
                    var xlen = Math.abs(tempValx2-tempValx1);
                    var ylen = Math.abs(tempValy3-tempValy1);
                    ctx.rect(tempValx1,tempValy1,xlen,ylen);
                    ctx.strokeStyle="#00FF00";
                    ctx.stroke();
                }
            </script>
        </div>
</body>
</html>

