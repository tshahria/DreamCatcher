
<?php
    /**
     * Copyright 2016 Google Inc.
     *
     * Licensed under the Apache License, Version 2.0 (the "License");
     * you may not use this file except in compliance with the License.
     * You may obtain a copy of the License at
     *
     *     http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing, software
     * distributed under the License is distributed on an "AS IS" BASIS,
     * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
     * See the License for the specific language governing permissions and
     * limitations under the License.
     */
    # [START vision_quickstart]
    # Includes the autoloader for libraries installed with composer

    require __DIR__ . '/vendor/autoload.php';
    # Imports the Google Cloud client library
    use Google\Cloud\Vision\VisionClient;
    use Google\Cloud\Storage\StorageClient;
    use PhpFanatic\clarifAI\ImageClient;

    $projectId = 'Google Project Id';
    $path = $_GET['link'];

    $serviceAccountPath = 'path to your credential json file';
    $bucket = 'name of the google sotrage bucket to store the pictures';

    function detect_face($projectId, $serviceAccountPath, $path)
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
    

    function genPic($pathName, $xStart, $yStart, $xSize, $ySize){
        $filename = $pathName;
        $im = imagecreatefromjpeg($filename);
        $im2 = imagecrop($im, ['x' => $xStart, 'y' => $yStart, 'width' => $xSize, 'height' => $ySize]);
        if ($im2 !== FALSE) {
            ob_start(); 
            $contents = ob_get_contents();
            ob_end_clean(); 
            
            $dataUri = "data:image/jpeg;base64," . base64_encode($contents);        
        }else{
            echo"failed";
        }
        return $dataUri;
    }
    
    function upload_object($pid, $sap, $bucketName, $objectName, $source)
    {
        $config = [
            'keyFilePath' => $sap,
            'projectId' => $pid,
        ];
        $storage = new StorageClient($config);
        $file = fopen($source, 'r');
        $bucket = $storage->bucket($bucketName);
        $object = $bucket->upload($file, [
            'name' => $objectName
        ]);
        $object2 = $bucket->object($objectName);
        $object2->update(['acl' => []], ['predefinedAcl' => 'PUBLICREAD']);
    }

    function uploadBatchToGS($dataA,$pid,$sap,$bk){
        $arr = array();
        for ($i=0; $i <sizeof($dataA) ; $i++) { 
            $objName = "testPic".($i+1).".jpg";
            upload_object($pid, $sap, $bk, $objName, $dataA[$i]);
            array_push($arr, $objName);
        }
        return $arr;
    }
       
    function batchCAnalysis($objList){
        $client = new ImageClient('Clarifai API Key');
        $totalScore = 0;
        $totalSleepScore = 0;
        $totalEngageScore = 0;
        $avgSleepScore = 0;
        $avgEngageScore = 0;
        $objLsize = sizeof($objList);
        for ($i=0; $i < $objLsize; $i++) {
            $imgLink = "https://storage.googleapis.com/[name of the storage bucketyou use]/" . $objList[$i];
            $client->AddImage($imgLink);
            $result = $client->Predict('Clarifai Model Name');
            $arrResult = (array)((array)(((array)json_decode($result))['outputs'])[0]);
            $arr2 = (array)(((array)$arrResult['data'])['concepts']);
            if(strcmp(((array)$arr2[0])['id'],"sleeping")){
                $engageScore = ((array)$arr2[0])['value'];
                $sleepyScore = ((array)$arr2[1])['value'];
            }else{
                $sleepyScore = ((array)$arr2[0])['value'];
                $engageScore = ((array)$arr2[1])['value'];
            }  
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

    $bPA = detect_face($projectId, $serviceAccountPath, $path);
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
        $dataTempURI = genPic($path, $vx1, $vy1, $xlen, $ylen);
        array_push($dataArr, $dataTempURI);
    }
    $resultLinkArr = uploadBatchToGS($dataArr,$projectId,$serviceAccountPath,$bucket);
    $batchArr = batchCAnalysis($resultLinkArr);
    //var_dump(%batchArr);
?>
<html>
<head>
</head>
<body>
    <script type="text/javascript">
        <?php echo "var barr = " . json_encode($batchArr) . ";";?>
        console.log(barr);
    </script>
</body>
</html>

