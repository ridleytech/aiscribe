<?php

require_once('Connections/transcribe.php');
include("functions.php");

$myObj = new stdClass;

if(isset($_POST['feedback']) && isset($_POST['uid']))
{
    $feedback = $_POST['feedback'];
    $uid = $_POST['uid'];
    $date = date("Y-m-d H:i:s");
    
    $insertSQL = sprintf("INSERT INTO feedback (feedback, userid, datecreated) VALUES (%s, %s, %s)",
    GetSQLValueString(mysql_real_escape_string($feedback), "text"),
    GetSQLValueString(mysql_real_escape_string($uid), "int"),
    GetSQLValueString(mysql_real_escape_string($date), "date"));

    mysql_select_db($database_transcribe, $transcribe);
    $Result1 = mysql_query($insertSQL, $transcribe) or die(mysql_error());	

    $feedbackid = mysql_insert_id();
    
    $status = "feedback saved";
    $myObj->feedbackid = blankNull(strval($feedbackid));
}
else
{
    $status = "feedback not saved";
}

 $myObj->status = $status;


echo "{\"data\":";
echo "{\"feedbackData\":";
echo json_encode( $myObj );
echo "}";
echo "}";




?>  