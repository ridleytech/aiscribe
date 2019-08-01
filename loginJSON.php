<?php require_once('Connections/transcribe.php');

include("functions.php");
include("en-de.php");

date_default_timezone_set('America/Detroit');

mysql_select_db($database_transcribe, $transcribe);

header('Content-Type: application/json');

$devStatus = "dev";

//$query_rsLoginInfo = "SELECT * FROM users WHERE email = '" . en($_POST['email']) . "' AND password = '" . en($_POST['password']) . "'";
//
//$_POST['username'] = "ridley1224";
//$_POST['password'] = "1111";


$query_rsLoginInfo = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "' AND password = '" . $_POST['password'] . "' AND accountconfirmed = 1";

$rsLoginInfo = mysql_query($query_rsLoginInfo, $transcribe) or die(mysql_error());
$row_rsLoginInfo = mysql_fetch_assoc($rsLoginInfo);
$totalRows_rsLoginInfo = mysql_num_rows($rsLoginInfo);

$object = new stdClass();

//$object->loginquery = $query_rsLoginInfo;
$object->devStatus = $devStatus;

if($totalRows_rsLoginInfo)
{	
	$object->email = blankNull($row_rsLoginInfo['email']);
    $object->username = blankNull($row_rsLoginInfo['username']);
    $object->lastname = blankNull($row_rsLoginInfo['firstname']);
    //$object->username = blankNull($row_rsLoginInfo['lastname']);
    $object->credits = blankNull($row_rsLoginInfo['credits']);
    $object->userimage = blankNull($row_rsLoginInfo['userimage']);
    $object->userid = blankNull($row_rsLoginInfo['userid']);
    $object->accountconfirmed = $row_rsLoginInfo['accountconfirmed'];
    $object->basicinfocompleted = $row_rsLoginInfo['basicinfocompleted'];
    $object->usertype = blankNull($row_rsLoginInfo['usertype']);
    $object->code = blankNull($row_rsLoginInfo['code']);
    $object->genuserid = "";
    $object->status = "Logged in";
    $object->code1 = "";
}
else
{
	$object->status = "Invalid login credentials";
}
	
echo "{\"data\":";
echo "{\"loginData\":";
echo json_encode( $object );
echo "}";
echo "}";
	
?>
