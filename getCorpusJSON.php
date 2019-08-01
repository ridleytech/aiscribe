<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );
include( "functions.php" );

$colname_rsCorpora = "-1";
if ( isset( $_POST[ 'uid' ] ) ) {
    $colname_rsCorpora = $_POST[ 'uid' ];
}

//if ( !isset( $_POST[ 'mobile' ] ) ) {
//
//    if ( isset( $_SESSION[ 'uid' ] ) ) {
//        $colname_rsCorpora = $_SESSION[ 'uid' ];
//    }
//}

$currentPage = $_SERVER[ "PHP_SELF" ];

$maxRows_rsCorpora = 20;
$pageNum_rsCorpora = 0;
if ( isset( $_POST[ 'pageNum_rsCorpora' ] ) ) {
    $pageNum_rsCorpora = $_POST[ 'pageNum_rsCorpora' ];
}
$startRow_rsCorpora = $pageNum_rsCorpora * $maxRows_rsCorpora;

mysql_select_db( $database_transcribe, $transcribe );
$query_rsCorpora = sprintf( "SELECT a.*, b.code, b.modelid, b.modelname, c.modelname as 'modelname2'  FROM (SELECT * FROM corpus WHERE userid = {$colname_rsCorpora} ORDER by datecreated DESC) as a INNER JOIN (SELECT customizationid,code,modelname,modelid FROM custommodels) as b INNER JOIN (select modelname, code FROM modeloptions) as c ON a.customizationid = b.customizationid AND b.code = c.code" );

$query_limit_rsCorpora = sprintf( "%s LIMIT %d, %d", $query_rsCorpora, $startRow_rsCorpora, $maxRows_rsCorpora );
$rsCorpora = mysql_query( $query_limit_rsCorpora, $transcribe )or die( mysql_error() );
$row_rsCorpora = mysql_fetch_assoc( $rsCorpora );

if ( isset( $_POST[ 'totalRows_rsCorpora' ] ) ) {
    $totalRows_rsCorpora = $_POST[ 'totalRows_rsCorpora' ];
} else {
    $all_rsCorpora = mysql_query( $query_rsCorpora );
    $totalRows_rsCorpora = mysql_num_rows( $all_rsCorpora );
}
$totalPages_rsCorpora = ceil( $totalRows_rsCorpora / $maxRows_rsCorpora ) - 1;

$queryString_rsCorpora = "";
if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
    $newParams = array();
    foreach ( $params as $param ) {
        if ( stristr( $param, "pageNum_rsCorpora" ) == false &&
            stristr( $param, "totalRows_rsCorpora" ) == false ) {
            array_push( $newParams, $param );
        }
    }
    if ( count( $newParams ) != 0 ) {
        $queryString_rsCorpora = "&" . htmlentities( implode( "&", $newParams ) );
    }
}
$queryString_rsCorpora = sprintf( "&totalRows_rsCorpora=%d%s", $totalRows_rsCorpora, $queryString_rsCorpora );

$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo[ 'apikey' ];

$curl = curl_init();

curl_setopt_array( $curl, array(
    CURLOPT_URL => "https://iam.bluemix.net/identity/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "grant_type=urn%3Aibm%3Aparams%3Aoauth%3Agrant-type%3Aapikey&apikey={$apiKey}",
    CURLOPT_HTTPHEADER => array(
        "Accept: application/json",
        "Content-Type: application/x-www-form-urlencoded",
        "Postman-Token: 1d378144-7f93-4d72-8b2d-3d775883d3f3",
        "cache-control: no-cache"
    ),
) );

$response = curl_exec( $curl );
$err = curl_error( $curl );

curl_close( $curl );

if ( $err ) {

    $status = "cURL Error1 #:" . $err;


} else {

    //echo "token response: {$response}<br>";

    $decodedData = json_decode( $response );

    //var_dump($decodedData);

    $token = $decodedData->access_token;

    //echo "<p>token: {$token}</p>";

    if ( $totalRows_rsCorpora > 0 ) {

        do {

            $object = new stdClass();

            $object->mid = blankNull( $row_rsCorpora[ 'modelid' ] );
            $object->code = blankNull( $row_rsCorpora[ 'code' ] );
            $object->cid = blankNull( $row_rsCorpora[ 'customizationid' ] );
            $object->cpid = blankNull( $row_rsCorpora[ 'corpusid' ] );
            $object->content = blankNull( $row_rsCorpora[ 'content' ] );
            $object->filename = blankNull( $row_rsCorpora[ 'filename' ] );
            $object->modelname = blankNull( str_replace( " - Narrowband", "", $row_rsCorpora[ 'modelname' ] ) );

            if ( $row_rsCorpora[ 'status' ] != 2 ) {

                $object->status = "View Status";

            } else {
                $object->status = "Analyzed";
            }

            //$object->status = $status;
            $list[] = $object;

        } while ( $row_rsCorpora = mysql_fetch_assoc( $rsCorpora ) );
    }
}

echo "{\"data\":";
echo "{\"corporaData\":";
echo json_encode( $list );
echo "}";
echo "}";
?>