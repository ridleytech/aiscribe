<?php
$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );

//test write

//$_POST[ 'uid' ] = "1";

$colname_rsUser = "-1";
if ( isset( $_POST[ 'uid' ] ) ) {
    $colname_rsUser = $_POST[ 'uid' ];
}


if ( !isset( $_POST[ 'mobile' ] ) ) {

    if ( isset( $_SESSION[ 'uid' ] ) ) {
        $colname_rsUser = $_SESSION[ 'uid' ];
    }
}

$currentPage = $_SERVER[ "PHP_SELF" ];

$maxRows_rsModels = 20;
$pageNum_rsModels = 0;
if ( isset( $_GET[ 'pageNum_rsModels' ] ) ) {
    $pageNum_rsModels = $_GET[ 'pageNum_rsModels' ];
}
$startRow_rsModels = $pageNum_rsModels * $maxRows_rsModels;

mysql_select_db( $database_transcribe, $transcribe );
//$query_rsModels = sprintf( "SELECT * FROM custommodels WHERE userid = {$colname_rsModels} AND active = 1 ORDER by datecreated DESC" );
$query_rsModels = sprintf( "SELECT a.*, b.modelname2 FROM (SELECT * FROM custommodels WHERE userid = {$colname_rsUser} AND active = 1 ORDER by datecreated DESC) as a INNER JOIN (SELECT modelname as 'modelname2',code from modeloptions) as b on a.code = b.code" );

$query_limit_rsModels = sprintf( "%s LIMIT %d, %d", $query_rsModels, $startRow_rsModels, $maxRows_rsModels );
$rsModels = mysql_query( $query_limit_rsModels, $transcribe )or die( mysql_error() );
$row_rsModels = mysql_fetch_assoc( $rsModels );

if ( isset( $_GET[ 'totalRows_rsModels' ] ) ) {
    $totalRows_rsModels = $_GET[ 'totalRows_rsModels' ];
} else {
    $all_rsModels = mysql_query( $query_rsModels );
    $totalRows_rsModels = mysql_num_rows( $all_rsModels );
}
$totalPages_rsModels = ceil( $totalRows_rsModels / $maxRows_rsModels ) - 1;

$queryString_rsModels = "";
if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
    $newParams = array();
    foreach ( $params as $param ) {
        if ( stristr( $param, "pageNum_rsModels" ) == false &&
            stristr( $param, "totalRows_rsModels" ) == false ) {
            array_push( $newParams, $param );
        }
    }
    if ( count( $newParams ) != 0 ) {
        $queryString_rsModels = "&" . htmlentities( implode( "&", $newParams ) );
    }
}
$queryString_rsModels = sprintf( "&totalRows_rsModels=%d%s", $totalRows_rsModels, $queryString_rsModels );

if ( $totalRows_rsModels > 0 ) {

    do {

        $object = new stdClass();

        $object->modelname = blankNull( $row_rsModels[ 'modelname' ] );
        $object->mid = blankNull( $row_rsModels[ 'modelid' ] );
        //$object->basename1 = blankNull( $row_rsModels['basename']);
        $object->basename1 = blankNull( str_replace( "- Narrowband", "", $row_rsModels[ 'modelname2' ] ) );
        $object->modeldescription = blankNull( $row_rsModels[ 'modeldescription' ] );
        $object->cid = blankNull( $row_rsModels[ 'customizationid' ] );
        
        if($row_rsModels['status'] != 3)
        {            
            $object->status = "View status";
        }
        else
        {
            $object->status = "Available";
        }        

        $list[] = $object;

    } while ( $row_rsModels = mysql_fetch_assoc( $rsModels ) );

}

echo "{\"data\":";
echo "{\"modelsData\":";
echo json_encode( $list );
echo "}";
echo "}";

?>