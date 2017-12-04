<?php
require __DIR__ . '/vendor/autoload.php';
use PhpFanatic\clarifAI\ImageClient;

$myclient = new ImageClient('api key');

$myclient->AddImage('image URL');

$result = $myclient->Predict('Model Name');
$arrResult = (array)((array)(((array)json_decode($result))['outputs'])[0]);
$arr2 = (array)(((array)$arrResult['data'])['concepts']);
$sleepyScore = ((array)$arr2[0])['value'];
$engageScore = ((array)$arr2[1])['value'];
echo $sleepyScore . "    " . $engageScore;
//['data']['concept']
//var_dump($arr2);
//reset($arrResult);
//$key = array_keys($arrResult);
//var_dump($key);
//$trimR = substr($result, 1, strlen($result)-1);

?>