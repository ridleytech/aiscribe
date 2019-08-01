<?php

require_once('Connections/transcribe.php'); 

include("functions.php");

date_default_timezone_set('America/Detroit');
$date = date("Y-m-d H:i:s");

if ((isset($_POST["output"]))) {
        
    $insertSQL = sprintf("UPDATE documents SET output = %s,dateupdated = %s WHERE documentid = %s",
                        GetSQLValueString($_POST['output'], "text"),
                        GetSQLValueString($date, "date"),
                        GetSQLValueString($_POST['did'], "int"));
    
    //echo "insertSQL: " . $insertSQL;

    mysql_select_db($database_transcribe, $transcribe);
    $Result1 = mysql_query($insertSQL, $transcribe) or die(mysql_error());

    $recipeid = mysql_insert_id();
    
    echo "transcription updated successfully";
    
    //echo "\nrecipe saved successfully";
}
else
{
    echo "transcription not updated";
}



?>