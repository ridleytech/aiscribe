<?php

//header("Content-Type: application/json; charset=UTF-8");

require_once('Connections/transcribe.php'); 

include("functions.php");
include("en-de.php");

date_default_timezone_set('America/Detroit');
$date = date("Y-m-d H:i:s");

//$_POST["translationid"] = "1";
//$_POST["lang"] = "en-es";

//$_POST["did"] = "15";

if(isset($_POST["tid"]))
{
   $tid = de($_POST["tid"]);
}

if (isset($tid) || isset($_POST["did"])) {
    
    if (isset($_POST["lang"])) {
     
        mysql_select_db($database_transcribe, $transcribe);
        mysql_query("SET NAMES UTF8");
        $query_rsDocInfo = sprintf("SELECT a.*,b.* FROM (SELECT * FROM translations WHERE translationid = %s AND language = %s) as a INNER JOIN (select filename,documentid from documents) as b on a.documentid = b.documentid", GetSQLValueString($tid, "int"),
        GetSQLValueString(de($_POST['lang']), "text"));
        $rsDocInfo = mysql_query($query_rsDocInfo, $transcribe) or die(mysql_error());
        $row_rsDocInfo = mysql_fetch_assoc($rsDocInfo);
        $totalRows_rsDocInfo = mysql_num_rows($rsDocInfo);

        //echo "query: {$query_rsDocInfo}<br>";

        
        //$translation = utf8_encode($row_rsDocInfo['translation']);
        $translation = $row_rsDocInfo['translation'];
        //$translation = $str_replace("\'","'",$translation);
        //$translation = utf8_encode($row_rsDocInfo['translation']);
        $display = $row_rsDocInfo['displayLang'];
        $filename= $row_rsDocInfo['filename'];

        //echo "trans: {$trans}<br>";
        //echo "display: {$display}<br>";

        $myObj1 = new stdClass;

        $myObj1->translation = $translation;
        $myObj1->displayLang = $display;
        $myObj1->filename= $filename;

        //var_dump($myObj1);

        $myJSON = json_encode($myObj1);

        //$error = json_last_error();

        //var_dump($myJSON, $error === JSON_ERROR_UTF8);

        //var_dump($myJSON);

        echo $myJSON;

        //echo $row_rsDocInfo['translation'];
    }
    else
    {
        mysql_select_db($database_transcribe, $transcribe);
        $query_rsDocInfo = sprintf("SELECT * FROM documents WHERE documentid = %s", GetSQLValueString(de($_POST['did']), "int"));
        $rsDocInfo = mysql_query($query_rsDocInfo, $transcribe) or die(mysql_error());
        $row_rsDocInfo = mysql_fetch_assoc($rsDocInfo);
        $totalRows_rsDocInfo = mysql_num_rows($rsDocInfo);

        $myObj = new stdClass;

        $myObj->response = $row_rsDocInfo[ 'output' ];
        $myObj->filename = $row_rsDocInfo[ 'filename' ];
        $myObj->confidence = blankNull($row_rsDocInfo[ 'documentconfidence' ]);

        $myJSON = json_encode( $myObj );

        echo $myJSON;

        //echo $row_rsDocInfo['output'];
    }
    
    //echo "\nrecipe saved successfully";
}
else if(isset($_POST["cpid"]))
{
    mysql_select_db($database_transcribe, $transcribe);
    $query_rsDocInfo = sprintf("SELECT * FROM corpus WHERE corpusid = %s", GetSQLValueString(de($_POST['cpid']), "int"));
    $rsDocInfo = mysql_query($query_rsDocInfo, $transcribe) or die(mysql_error());
    $row_rsDocInfo = mysql_fetch_assoc($rsDocInfo);
    $totalRows_rsDocInfo = mysql_num_rows($rsDocInfo);

    $myObj = new stdClass;

    $myObj->content = $row_rsDocInfo[ 'content' ];
    $myObj->cid = $row_rsDocInfo[ 'cid' ];
    $myObj->filename = $row_rsDocInfo[ 'filename' ] . ".txt";

    $myJSON = json_encode( $myObj );

    echo $myJSON;

    //echo $row_rsDocInfo['output'];
}
else
{
    //echo "transcription not updated";
    
    //echo "none";
}

?>                                                                                                                                                              