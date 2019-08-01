<?php

//header("Content-Type: application/json; charset=UTF-8");

require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

date_default_timezone_set( 'America/Detroit' );
$date = date( "Y-m-d H:i:s" );

//$_POST["translationid"] = "1";
//$_POST["lang"] = "en-es";

//$_POST["did"] = "26";

//$_POST["mobile"] = true;


if(isset($_POST["did"]) && isset($_POST["mobile"]))
{
   $did = $_POST["did"]; 
}
else if(isset($_POST["did"]))
{
   $did = de($_POST["did"]);  
}

if(isset($did))
{
    mysql_select_db( $database_transcribe, $transcribe );
    $query_rsTranslationInfo = "SELECT * FROM translations WHERE documentid = {$did}";
    $rsTranslationInfo = mysql_query( $query_rsTranslationInfo, $transcribe )or die( mysql_error() );
    $row_rsTranslationInfo = mysql_fetch_assoc( $rsTranslationInfo );
    $totalRows_rsTranslationInfo = mysql_num_rows( $rsTranslationInfo );
    
    do {
        
        $languages[] = $row_rsTranslationInfo['language'];

    } while ( $row_rsTranslationInfo = mysql_fetch_assoc( $rsTranslationInfo ) );
}


mysql_select_db( $database_transcribe, $transcribe );
$query_rsDocInfo = "SELECT * FROM languages WHERE active = 1";
$rsDocInfo = mysql_query( $query_rsDocInfo, $transcribe )or die( mysql_error() );
$row_rsDocInfo = mysql_fetch_assoc( $rsDocInfo );
$totalRows_rsDocInfo = mysql_num_rows( $rsDocInfo );

do {

    $myObj1 = new stdClass;

    $myObj1->displayname = $row_rsDocInfo[ 'displayname' ];
    $myObj1->code = $row_rsDocInfo[ 'code' ];
        
    if(in_array($row_rsDocInfo[ 'code' ],$languages))
    {
        $myObj1->translated = 1;
    }
    else
    {
        $myObj1->translated = 0;
    }

    $list[] = $myObj1;

} while ( $row_rsDocInfo = mysql_fetch_assoc( $rsDocInfo ) );


//var_dump($myObj1);

$myJSON = json_encode( $list );

//$error = json_last_error();

//var_dump($myJSON, $error === JSON_ERROR_UTF8);

//var_dump($myJSON);

if(isset($_POST['mobile']))
{
    echo "{\"data\":";
    echo "{\"languageData\":";
    echo $myJSON;
    echo "}";
    echo "}";
}
else
{
    echo $myJSON;

}

?>