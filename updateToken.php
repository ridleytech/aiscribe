<?php

require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

date_default_timezone_set( 'America/Detroit' );

mysql_select_db( $database_transcribe, $transcribe );

if ( isset( $_POST[ 'deviceid' ] ) ) {
    $date = date( "Y-m-d H:i:s" );

    $insertSQL = sprintf( "UPDATE users SET deviceid = %s, deviceidupdate = %s WHERE userid = %s",
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'deviceid' ] ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $date ), "date" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'uid' ] ), "int" ) );

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

    $object = new stdClass();
    $object->status = "token updated";
}


echo "{\"data\":";
echo "{\"tokenData\":";
echo json_encode( $object );
echo "}";
echo "}";

?>