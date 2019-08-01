<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );

//$_POST[ 'code' ] = "en-US_NarrowbandModel";
//$_POST[ 'uid' ] = "8";
//$_POST[ 'mobile' ] = true;

$colname_rsModelOptions = "-1";

if ( isset( $_POST[ 'uid' ] ) ) {
    $colname_rsModelOptions = $_POST[ 'uid' ];
}

if ( isset( $_POST[ 'code' ] ) && isset( $_POST[ 'uid' ] ) ) {

    $currentPage = $_SERVER[ "PHP_SELF" ];

    $maxRows_rsModelOptions = 20;
    $pageNum_rsModelOptions = 0;
    if ( isset( $_GET[ 'pageNum_rsModelOptions' ] ) ) {
        $pageNum_rsModelOptions = $_GET[ 'pageNum_rsModelOptions' ];
    }
    $startRow_rsModelOptions = $pageNum_rsModelOptions * $maxRows_rsModelOptions;

    mysql_select_db( $database_transcribe, $transcribe );
    $query_rsModelOptions = sprintf( "SELECT * FROM custommodels WHERE userid = {$colname_rsModelOptions} AND code = '{$_POST['code']}' AND active = 1" );
    
    //echo "{$query_rsModelOptions}<br>";

    $query_limit_rsModelOptions = sprintf( "%s LIMIT %d, %d", $query_rsModelOptions, $startRow_rsModelOptions, $maxRows_rsModelOptions );
    $rsModelOptions = mysql_query( $query_limit_rsModelOptions, $transcribe )or die( mysql_error() );
    $row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );

    if ( isset( $_GET[ 'totalRows_rsModelOptions' ] ) ) {
        $totalRows_rsModelOptions = $_GET[ 'totalRows_rsModelOptions' ];
    } else {
        $all_rsModelOptions = mysql_query( $query_rsModelOptions );
        $totalRows_rsModelOptions = mysql_num_rows( $all_rsModelOptions );
    }
    $totalPages_rsModelOptions = ceil( $totalRows_rsModelOptions / $maxRows_rsModelOptions ) - 1;

    $queryString_rsModelOptions = "";
    if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
        $newParams = array();
        foreach ( $params as $param ) {
            if ( stristr( $param, "pageNum_rsModelOptions" ) == false &&
                stristr( $param, "totalRows_rsModelOptions" ) == false ) {
                array_push( $newParams, $param );
            }
        }
        if ( count( $newParams ) != 0 ) {
            $queryString_rsModelOptions = "&" . htmlentities( implode( "&", $newParams ) );
        }
    }
    $queryString_rsModelOptions = sprintf( "&totalRows_rsModelOptions=%d%s", $totalRows_rsModelOptions, $queryString_rsModelOptions );

    if ( $totalRows_rsModelOptions > 0 ) {

        do {

            $object = new stdClass();

            $object->cid = blankNull( $row_rsModelOptions[ 'customizationid' ] );
            $object->modelname = blankNull( str_replace( "- Narrowband", "", $row_rsModelOptions[ 'modelname' ] ) );

            $list[] = $object;

        } while ( $row_rsFiles = mysql_fetch_assoc( $rsFiles ) );
    }
}

echo "{\"data\":";
echo "{\"modelsData\":";
echo json_encode( $list );
echo "}";
echo "}";

?>