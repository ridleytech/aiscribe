<?php

//$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );


//$_POST[ 'uid' ] = "1";


$colname_rsFiles = "-1";
if ( isset( $_POST[ 'uid' ] ) ) {
    $colname_rsFiles = $_POST[ 'uid' ];
}

if(!isset( $_POST[ 'mobile' ] )){
 
    if ( isset( $_SESSION[ 'uid' ] ) ) {
        $colname_rsFiles = $_SESSION[ 'uid' ];
    }
}

$currentPage = $_SERVER[ "PHP_SELF" ];

$maxRows_rsFiles = 20;
$pageNum_rsFiles = 0;
if ( isset( $_POST[ 'pageNum_rsFiles' ] ) ) {
    $pageNum_rsFiles = $_POST[ 'pageNum_rsFiles' ];
}
$startRow_rsFiles = $pageNum_rsFiles * $maxRows_rsFiles;

mysql_select_db( $database_transcribe, $transcribe );
$query_rsFiles = sprintf( "SELECT * FROM documents WHERE userid = {$colname_rsFiles} AND active = 1 ORDER by datecreated DESC" );

$query_limit_rsFiles = sprintf( "%s LIMIT %d, %d", $query_rsFiles, $startRow_rsFiles, $maxRows_rsFiles );

//echo "query: " . $query_limit_rsFiles;

$rsFiles = mysql_query( $query_limit_rsFiles, $transcribe )or die( mysql_error() );
$row_rsFiles = mysql_fetch_assoc( $rsFiles );

if ( isset( $_POST[ 'totalRows_rsFiles' ] ) ) {
    $totalRows_rsFiles = $_POST[ 'totalRows_rsFiles' ];
} else {
    $all_rsFiles = mysql_query( $query_rsFiles );
    $totalRows_rsFiles = mysql_num_rows( $all_rsFiles );
}
$totalPages_rsFiles = ceil( $totalRows_rsFiles / $maxRows_rsFiles ) - 1;

$queryString_rsFiles = "";
if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
    $newParams = array();
    foreach ( $params as $param ) {
        if ( stristr( $param, "pageNum_rsFiles" ) == false &&
            stristr( $param, "totalRows_rsFiles" ) == false ) {
            array_push( $newParams, $param );
        }
    }
    if ( count( $newParams ) != 0 ) {
        $queryString_rsFiles = "&" . htmlentities( implode( "&", $newParams ) );
    }
}
$queryString_rsFiles = sprintf( "&totalRows_rsFiles=%d%s", $totalRows_rsFiles, $queryString_rsFiles );

if($totalRows_rsFiles > 0)
{
    
do {

    $object = new stdClass();

    $object->filename = blankNull( $row_rsFiles[ 'filename' ] );
    $object->transcription = "view";
    $object->did = blankNull( $row_rsFiles[ 'documentid' ] );
    
    $date = date_create($row_rsFiles[ 'datecreated' ]);
    
    $str = date_format($date,"m/d/Y") . " at " . date_format($date,"h:i a");
    
    $object->datecreated = blankNull( $str ); 
    
    
    if ( $row_rsFiles[ 'status' ] == 1 ) {
        $object->status = "Completed";
    } else {
        $object->status = "Pending";
    }
    if ( $row_rsFiles[ 'status' ] == 1 && $row_rsFiles[ 'status' ] == 1 ) {
        $object->translations = "view";
    } else if ( $row_rsFiles[ 'status' ] == 1 ) {
        $object->translations = "create";
    }
    
    $object->query = json_encode($query_rsFiles);

    $list[] = $object;

} while ( $row_rsFiles = mysql_fetch_assoc( $rsFiles ) );

}


echo "{\"data\":";
echo "{\"filesData\":";
echo json_encode( $list );
echo "}";
echo "}";

?>