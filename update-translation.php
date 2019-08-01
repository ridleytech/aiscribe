<?php

require_once('Connections/transcribe.php'); 

include("functions.php");
include("en-de.php");

date_default_timezone_set('America/Detroit');
$date = date("Y-m-d H:i:s");



if ((isset($_POST["input"]))) {
        
    $insertSQL = sprintf("UPDATE translations SET translation = %s,dateupdated = %s WHERE translationid = %s",
                        GetSQLValueString($_POST['input'], "text"),
                        GetSQLValueString($date, "date"),
                        GetSQLValueString(de($_POST['tid']), "int"));
    
    //echo "insertSQL: " . $insertSQL;

    mysql_select_db($database_transcribe, $transcribe);
    $Result1 = mysql_query($insertSQL, $transcribe) or die(mysql_error());
    
    $response = [ "status" => "Translation updated successfully"];
    
    if(isset($_POST['mobile']))
    {
        echo "{\"data\":";
        echo "{\"translateData\":";
        echo json_encode( $response );
        echo "}";
        echo "}";
    }
    else
    {
        echo "translation updated successfully";
    }
}
else
{    
    $response = [ "status" => "Translation not updated"];
    
    if(isset($_POST['mobile']))
    {
        echo "{\"data\":";
        echo "{\"translateData\":";
        echo json_encode( $response );
        echo "}";
        echo "}";
    }
    else
    {
        echo "translation not updated";
    }
}

?>