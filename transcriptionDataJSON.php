<?php

//header("Content-Type: application/json; charset=UTF-8");

require_once('Connections/transcribe.php'); 
include("functions.php");

date_default_timezone_set('America/Detroit');
$date = date("Y-m-d H:i:s");

//$_POST["lang"] = "en-es";

//$_POST["did"] = "15";

if (isset($_POST["did"])) {
    
    mysql_select_db($database_transcribe, $transcribe);
    $query_rsDocInfo = sprintf("SELECT * FROM documents WHERE documentid = %s", GetSQLValueString($_POST['did'], "int"));
    $rsDocInfo = mysql_query($query_rsDocInfo, $transcribe) or die(mysql_error());
    $row_rsDocInfo = mysql_fetch_assoc($rsDocInfo);
    $totalRows_rsDocInfo = mysql_num_rows($rsDocInfo);

    $myObj = new stdClass;

    $myObj->response = $row_rsDocInfo[ 'output' ];
    $myObj->filename = $row_rsDocInfo[ 'filename' ];
    $myObj->documentconfidence = blankNull($row_rsDocInfo[ 'documentconfidence' ]);

    $myJSON = json_encode( $myObj );
}

echo "{\"data\":";
echo "{\"transcriptionData\":";
echo json_encode( $myObj );
echo "}";
echo "}";
?>                                                                                                                                                              