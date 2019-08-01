<?php

mysql_select_db( $database_transcribe, $transcribe );
$query_rsStatusInfo = sprintf( "SELECT * FROM appstatus order by datecreated DESC LIMIT 1" );
$rsStatusInfo = mysql_query( $query_rsStatusInfo, $transcribe )or die( mysql_error() );
$row_rsStatusInfo = mysql_fetch_assoc( $rsStatusInfo );
$totalRows_rsStatusInfo = mysql_num_rows( $rsStatusInfo );

if ( $row_rsStatusInfo[ 'status' ] == 0 ) {
    header( "Location: " . "maintenance.php" . "?m=1" );
    exit;
}

?>