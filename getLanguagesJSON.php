<?php
require_once( 'Connections/transcribe.php' );

include( "functions.php" );

$query_rsModelOptions = sprintf( "SELECT * FROM modeloptions WHERE active = %s", GetSQLValueString( 1, "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );

do {
                                    
    $object = new stdClass();
    $object->code = $row_rsModelOptions['code'];
    $object->modelname = $row_rsModelOptions['modelname'];

    $list[] = $object;

} while ($row_rsModelOptions = mysql_fetch_assoc($rsModelOptions)); 


echo "{\"data\":";
echo "{\"languageData\":";
echo json_encode( $list );
echo "}";
echo "}";

?>