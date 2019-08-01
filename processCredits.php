<?php

require_once( 'Connections/transcribe.php' );

include( "functions.php" );

if(isset($_POST['remainingCredits']) && isset($_POST['uid']))
{
    $updateSQL = sprintf( "UPDATE users SET credits=%s WHERE userid=%s",
                GetSQLValueString( $_POST['remainingCredits'], "text" ),
                GetSQLValueString( $_POST['uid'], "int" ) );

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );
    
    echo "credits updated successfully";
}

?>