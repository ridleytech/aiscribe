<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

date_default_timezone_set( 'America/Detroit' );
$date = date( "Y-m-d H:i:s" );

if ( isset( $_GET[ 'uid' ] ) ) {

    mysql_select_db( $database_transcribe, $transcribe );
    $query_rsUsernameInfo = sprintf( "SELECT userid,email FROM users WHERE uid = %s AND accountconfirmed = 1", GetSQLValueString( $_GET[ 'uid' ], "int" ) );
    $rsUsernameInfo = mysql_query( $query_rsUsernameInfo, $transcribe )or die( mysql_error() );
    $row_rsUsernameInfo = mysql_fetch_assoc( $rsUsernameInfo );
    $totalRows_rsUsernameInfo = mysql_num_rows( $rsUsernameInfo );

    if ( $totalRows_rsUsernameInfo < 1 ) {

        $insertSQL = sprintf( "UPDATE users SET accountconfirmed = %s, dateconfirmed = %s WHERE userid = %s",
            GetSQLValueString( mysql_real_escape_string( "1" ), "text" ),
            GetSQLValueString( mysql_real_escape_string( $date ), "date" ),
            GetSQLValueString( mysql_real_escape_string( de( $_GET[ 'uid' ] ) ), "int" ) );

        mysql_select_db( $database_transcribe, $transcribe );
        $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

        echo "You account has been confirmed";
        
        //send welcome email
            
        $message = "Thank you joining the AIScribe community. Get started now uploading audio files and get searchable, editable transcripts in minutes. And translate them to your choice of 22 languages. Like magic.";

        $to = $row_rsUsernameInfo['email'];
        $subject = "Welcome to AIScribe";
        $html = $message;
        $text = $message;
        $from = "noreply@myaiscribe.com";

        include("send-email.php");
    }
}

?>