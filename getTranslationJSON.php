<?php

//header("Content-Type: application/json; charset=UTF-8");

require_once('Connections/transcribe.php'); 

include("functions.php");

date_default_timezone_set('America/Detroit');
$date = date("Y-m-d H:i:s");

//$_POST["translationid"] = "1";
//$_POST["lang"] = "en-es";

//$_POST["did"] = "15";

if (isset($_POST["translationid"]) || isset($_POST["did"])) {
    
    if (isset($_POST["lang"])) {
     
        mysql_select_db($database_transcribe, $transcribe);
        mysql_query("SET NAMES UTF8");
        $query_rsDocInfo = sprintf("SELECT a.*,b.* FROM (SELECT * FROM translations WHERE translationid = %s AND language = %s) as a INNER JOIN (select filename,documentid from documents) as b on a.documentid = b.documentid", GetSQLValueString($_POST['translationid'], "int"),
        GetSQLValueString($_POST['lang'], "text"));
        $rsDocInfo = mysql_query($query_rsDocInfo, $transcribe) or die(mysql_error());
        $row_rsDocInfo = mysql_fetch_assoc($rsDocInfo);
        $totalRows_rsDocInfo = mysql_num_rows($rsDocInfo);

        //echo "query: {$query_rsDocInfo}<br>";

        
        //$translation = utf8_encode($row_rsDocInfo['translation']);
        $translation = $row_rsDocInfo['translation'];
        //$translation = $str_replace("\\'","'",$row_rsDocInfo['translation']);
        
        //$translation = utf8_encode($row_rsDocInfo['translation']);
        $display = $row_rsDocInfo['displayLang'];
        $filename= $row_rsDocInfo['filename'];

        //echo "trans: {$trans}<br>";
        //echo "display: {$display}<br>";

        $myObj1 = new stdClass;

        $myObj1->translation = $translation;
        $myObj1->displayLanguage = $display;
        $myObj1->filename= $filename;

        //var_dump($myObj1);

        $myJSON = json_encode($myObj1);

        //$error = json_last_error();

        //var_dump($myJSON, $error === JSON_ERROR_UTF8);

        //var_dump($myJSON);

        //echo $myJSON;

        //echo $row_rsDocInfo['translation'];
    }
    
    //echo "\nrecipe saved successfully";
}
else
{
    //echo "transcription not updated";
    
    //echo "none";
}

echo "{\"data\":";
echo "{\"translationData\":";
echo $myJSON;
echo "}";
echo "}";

?>                                                                                                                                                              