<?php

//header('Content-type: application/json');

require_once( 'Connections/transcribe.php' );
include( "functions.php" );
include( "en-de.php" );

mysql_select_db( $database_transcribe, $transcribe );

//$response = ["URLOfTheSecondWebsite" => $request['websites'][1]['URL']];

$date = date( "Y-m-d H:i:s" );

mysql_select_db( $database_transcribe, $transcribe );
$query_rsUsers = "SELECT userid from users WHERE username = '" . $_POST[ 'username' ] . "'";

$rsUsers = mysql_query( $query_rsUsers, $transcribe )or die( mysql_error() );
$row_rsUsers = mysql_fetch_assoc( $rsUsers );
$totalRows_rsUsers = mysql_num_rows( $rsUsers );

mysql_select_db( $database_transcribe, $transcribe );
$query_rsEmailInfo = sprintf( "SELECT * FROM users WHERE email = %s", GetSQLValueString( $_POST[ 'email' ], "text" ) );
$rsEmailInfo = mysql_query( $query_rsEmailInfo, $transcribe )or die( mysql_error() );
$row_rsEmailInfo = mysql_fetch_assoc( $rsEmailInfo );
$totalRows_rsEmailInfo = mysql_num_rows( $rsEmailInfo );

$object = new stdClass();
$object->status = "data not saved";

if ( isset( $_POST[ 'uid' ] ) ) {
    if ( isset( $_POST[ 'basicprofile' ] ) ) {
        $insertSQL = sprintf( "UPDATE users SET firstname = %s, lastname = %s, password = %s, email = %s , basicinfocompleted = %s WHERE userid = %s",
            GetSQLValueString( mysql_real_escape_string( $_POST[ 'firstname' ] ), "text" ),
            GetSQLValueString( mysql_real_escape_string( $_POST[ 'lastname' ] ), "text" ),
            GetSQLValueString( mysql_real_escape_string( $_POST[ 'password' ] ), "text" ),
            GetSQLValueString( mysql_real_escape_string( $_POST[ 'email' ] ), "text" ),
            GetSQLValueString( mysql_real_escape_string( 1 ), "int" ),
            GetSQLValueString( mysql_real_escape_string( $_POST[ 'uid' ] ), "text" ) );

        mysql_select_db( $database_transcribe, $transcribe );
        $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

        $object->status = "basic info saved";
    } else if ( isset( $_POST[ 'updating' ] ) ) {

        if ( $totalRows_rsEmailInfo != 0 && ($_POST[ 'email' ] != $row_rsEmailInfo['email'])) {
            $object->status = "Account with email " . $_POST[ 'email' ] . " aleady exists";
        } else {
            $updateSQL = sprintf( "UPDATE users SET firstname = %s, lastname = %s, password = %s, email = %s WHERE userid = %s",
                GetSQLValueString( mysql_real_escape_string( $_POST[ 'firstname' ] ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $_POST[ 'lastname' ] ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $_POST[ 'password' ] ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $_POST[ 'email' ] ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $_POST[ 'uid' ] ), "text" ) );

            mysql_select_db( $database_transcribe, $transcribe );
            $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );

            $object->status = "profile updated";
        }
    }
} else if ( $totalRows_rsUsers != 0 ) {
    $object->status = "Account with username " . $_POST[ 'username' ] . " aleady exists";
} else if ( $totalRows_rsEmailInfo != 0 ) {
    $object->status = "Account with email " . $_POST[ 'email' ] . " aleady exists";
} else if ( isset( $_POST[ 'signup' ] ) ) {
    $insertSQL = sprintf( "INSERT INTO users (username, fullname, email, password, deviceid, fbID, usertype, code, credits, datecreated) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'username' ] ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'fullname' ] ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'email' ] ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'password' ] ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'deviceid' ] ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'fbID' ] ), "text" ),
        GetSQLValueString( 1, "int" ),
        GetSQLValueString( mysql_real_escape_string( $_POST[ 'code' ] ), "text" ),
        GetSQLValueString( "0.00", "text" ),
        GetSQLValueString( $date, "date" ) );

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

    $last_id = mysql_insert_id();

    $object->status = "user saved";
    $object->userid = $last_id;

    //to do randall
    //add ec2 url for account confirmation

    $uid = urlencode( en( $last_id ) );

    $to = $_POST[ "email" ];
    $subject = "AIScribe Account Registration Request";
    $html = "We’ve emailed a verification link to {$_POST["
    email "]}. Click <a href='https://www.myaiscribe.com/confirmation.php?uid={$uid}'>here</a> to finish setting up your account.";
    $text = "We’ve emailed a verification link to {$_POST["
    email "]}. Click here to finish setting up your account.";
    $from = "noreply@aiscribe.com";

    include( "send-email.php" );
}

echo "{\"data\":";
echo "{\"userData\":";
echo json_encode( $object );
echo "}";
echo "}";

?>