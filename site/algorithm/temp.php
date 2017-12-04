<body>
    <canvas id="myCanvas" width="1500" height="1500">
    <img id="scream" src="<?php echo $path; ?>">

    <script type="text/javascript">
        <?php
            $js_array = json_encode($bPA);
            echo "var jArr = ". $js_array . ";\n";
        ?>
        var c=document.getElementById("myCanvas");
        var ctx=c.getContext("2d");
        var img = document.getElementById("scream");
        ctx.drawImage(img, 0, 0);
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
            console.log(ylen);
            ctx.rect(tempValx1,tempValy1,xlen,ylen);
            ctx.stroke();
        }
    </script>
</body>
<?
    $storage = new StorageClient([
    'projectId' => $projectId
    ]);
    $bucketName = 'dreamcatcher-storage';
    $bucket = $storage->bucket($bucketName);
?>
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

     