<?php 

//require_once('/Connections/haulyeah.php');  
include ("functions.php");

mysql_select_db($database_haulyeah, $haulyeah);

//include("resizePic.php");

$file = basename($_FILES['userfile']['name']);

$name = basename($file);
$extension = strtolower(strrchr($file, '.'));
$e = explode(".", $file );
$pre = $e[0];

$pre1 = $pre;


$filename = generateRandomString() . $extension;

/*if ($_FILES['userfile']['size']> 300000) {
	exit("Your file is too large."); 
}*/

$date = date("Y-m-d H:i:s");

if (move_uploaded_file($_FILES['userfile']['tmp_name'], "uploads/".$filename)) {
	
	//echo "file moved\r\n";	
		
	$source = "userimages/".$pre."".$extension;
	
	
	
	//list($width, $height) = getimagesize($source);
	
	$destination6 = "userimages/".$pre."_6".$extension;
	
	$object = new stdClass();
	$object->status = "media upload";
	$object->filename = $filename;
	
	//echo "image upload";
		
//	$thumb4 = 320;
//	$thumb5 = 320/3;
//	$thumb6 = 375/3;
	
	//$a = smart_resize_image($source,null,$thumb4,$thumb4,true,$destination4,false,false,100);
	//$a = smart_resize_image($source,null,$thumb5,$thumb5,true,$destination5,false,false,100);
	//$b = smart_resize_image($source,null,$thumb6,$thumb6,true,$destination6,false,false,100);	
	//$c = smart_resize_image($source,null,$width,$height,true,$destination7,false,false,100);	
}

echo "{\"data\":";
echo "{\"uploadData\":";
echo json_encode( $object );
echo "}";
echo "}";

?>