<?php

require_once( 'Connections/transcribe.php' );

include( "functions.php" );

$database_transcribe = "transcribe-backup";

$tables = "translations,transactions,documents,custommodels,corpus,feedback";

$tablesList = explode(",",$tables);

foreach($tablesList as $table)
{
    $insertSQL = sprintf( "truncate TABLE {$table}");

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );
}

echo "tables truncated<br>";

?>