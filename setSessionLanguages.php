<?php



if ( !isset( $_SESSION ) ) {
    session_start();
}


//include("functions.php");

$_SESSION['languages'] = $_POST['languages'];
$_SESSION['displayLanguages'] = $_POST['displayLanguages'];



$myObj = new stdClass;
$myObj->status = "languages set successfully";
$myObj->languages = $_SESSION['languages'];
$myObj->displayLanguages = $_SESSION['displayLanguages'];

$myJSON = json_encode( $myObj );

echo $myJSON;


//echo json_encode($_SESSION['languages']);

?>