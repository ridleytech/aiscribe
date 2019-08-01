<?php

include("getID3/getid3/getid3.php");

$pathName = $_FILES['file']['tmp_name'];



$getID3 = new getID3;
$ThisFileInfo = $getID3->analyze($pathName);
$len= @$ThisFileInfo['playtime_seconds']; //

echo $len;

?>