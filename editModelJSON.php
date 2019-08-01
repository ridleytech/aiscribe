<?php

require_once( 'Connections/transcribe.php' );

include( "functions.php" );


$myObj = new stdClass;


if (isset( $_POST[ "modeldescription" ]) && isset( $_POST[ "modelid" ])) {

     $insertSQL = sprintf( "UPDATE custommodels SET modeldescription=%s WHERE modelid =%s",
            GetSQLValueString( $_POST[ 'modeldescription' ], "text" ),
            GetSQLValueString( $_POST[ 'modelid' ], "int" ) );

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

    $myObj->status = "model update successful";
}
else
{
    $myObj->status = "model not updated";
}


echo "{\"data\":";
echo "{\"modelData\":";
echo json_encode( $myObj );
echo "}";
echo "}";

?>