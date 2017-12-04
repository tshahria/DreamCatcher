
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
    use PhpFanatic\clarifAI\ImageClient;

    $projectId = 'Google Project Id';
    $path = 'path to image you want to crop';
    $serviceAccountPath = 'path to the credential json file';
    

    function detect_face($projectId, $serviceAccountPath, $path)
    {
        $config = [
            'keyFilePath' => $serviceAccountPath,
            'projectId' => $projectId,
        ];
        $vision = new VisionClient($config);
        $image = $vision->image(file_get_contents($path), ['FACE_DETECTION']);
        $result = $vision->annotate($image);
        # [END detect_face]
        //print("Faces:\n");
        //foreach ((array) $result->faces() as $face) {
           
        //}
        $arrR = (array)$result;
        reset($arrR);
        $key = array_keys($arrR)[0];
        $attributesArr = $arrR[$key]["faceAnnotations"];
        $bPolyArrays = array();
        foreach ($attributesArr as $tempA) {
            array_push($bPolyArrays, $tempA["boundingPoly"]);
        }
        //var_dump($keys);
        //var_dump($bPolyArrays);
        return $bPolyArrays;
    }
    

    function genPic($pathName, $xStart, $yStart, $xSize, $ySize){
        $filename = $pathName;
        $im = imagecreatefromjpeg($filename);
        $im2 = imagecrop($im, ['x' => $xStart, 'y' => $yStart, 'width' => $xSize, 'height' => $ySize]);
        if ($im2 !== FALSE) {
            ob_start(); // Let's start output buffering.
            imagejpeg($im2); //This will normally output the image, but because of ob_start(), it won't.
            $contents = ob_get_contents(); //Instead, output above is saved to $contents
            ob_end_clean(); //End the output buffer.
            
            $dataUri = "data:image/jpeg;base64," . base64_encode($contents);        
        }else{
            echo"failed";
        }
        //var_dump($dataUri);
        return $dataUri;
    }
    $bPA = detect_face($projectId, $serviceAccountPath, $path);
    //var_dump($bPA);
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

    //$client = new ImageClient(e4a6be777c5243a38e6dcb840ba98cc2);

    //$client->AddImage('https://us.123rf.com/450wm/bialasiewicz/bialasiewicz1401/bialasiewicz140100757/25324368-yong-man-sleeping-in-his-bed-on-white-pillow.jpg?ver=6');
    //$result = $client->Predict('my-first-application');
    //var_dump($result);
?>
<html>
<head>
</head>
<body>
    <img src="ppl.jpg">
    <script type="text/javascript">
        <?php
            $dA = json_encode($dataArr);
            echo "var dArr = ". $dA . ";\n";
        ?>
        for (var i = 0; i < dArr.length; i++) {
            console.log(dArr[i]);
        }
    </script>
    <img src="<?php echo $dataArr[0]; ?>">
    <img src="<?php echo $dataArr[1]; ?>">
    <img src="<?php echo $dataArr[2]; ?>"> 
    <img src="<?php echo $dataArr[3]; ?>"> 
    <img src="<?php echo $dataArr[4]; ?>"> 
    <img src="<?php echo $dataArr[5]; ?>"> 

</body>
</html>

