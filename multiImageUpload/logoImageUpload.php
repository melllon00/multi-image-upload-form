<?php

include_once 'imageUpload.php';

function getTimestamp()
{
    return date("His") . substr((string)microtime(), 2, 8);
}

$today = date("Ymd");
$nowTime = getTimestamp();

multiImageCompressAndUpload("../image/logo/".$today, $nowTime, 'imageFile');

?>