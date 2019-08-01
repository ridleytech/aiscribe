<?php

require_once( 'Connections/transcribe.php' );
include("functions.php");
include("en-de.php");

if(isset($_POST['did']) && isset($_POST['uid']))
{    
     mysql_select_db( $database_transcribe, $transcribe );
    $query_rsDocInfo = sprintf( "SELECT * FROM documents WHERE documentid = %s AND userid = %s", GetSQLValueString( de($_POST['did']), "int" ), GetSQLValueString( de($_POST['uid']), "int" ) );
    $rsDocInfo = mysql_query( $query_rsDocInfo, $transcribe )or die( mysql_error() );
    $row_rsDocInfo = mysql_fetch_assoc( $rsDocInfo );
    $totalRows_rsDocInfo = mysql_num_rows( $rsDocInfo );
    
    //echo "{$query_rsDocInfo}<br>";    
    
    if(($totalRows_rsDocInfo) > 0)
    {            
        $filename = explode( ".", $row_rsDocInfo['filename'] );
        $filename1 = $filename[ 0 ].$row_rsDocInfo['documentid']."." . $_POST['ext'];
        
        try {

            unlink( "uploads/{$filename1}" );
            
            echo "{$filename1} deleted.";
        } catch ( Exception $e ) {

            echo "could not delete {$filename1}. ";
        }
    }
    else
    {
        echo "no file {$filename1}. ";
    }
        
}

?>