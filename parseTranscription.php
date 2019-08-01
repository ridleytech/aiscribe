<?php

$date = date( "Y-m-d H:i:s" );

$resultString;

//echo ". step 1";

foreach ( $results as $result ) {

    $confidence = $result->alternatives[ 0 ]->confidence;

    $confidenceValues[] = $confidence;

    $transcript = $result->alternatives[ 0 ]->transcript;

    $resultString .= $transcript;
}

//echo ". step 2";

//var_dump($confidenceValues);

$average = array_sum( $confidenceValues ) / count( $confidenceValues );

//$percent = round((float)$average * 100 ) . '%';
$percent = number_format( ( float )$average * 100, 2, '.', '' ) . '%';

//echo "<h3>Transcription Confidence: {$percent}</h3>";

//echo $resultString;

$output = "Transcription Confidence: {$percent}\n\n{$resultString}";

$resultString = str_replace( "%HESITATION", "", $resultString );

$updateSQL = sprintf( "UPDATE documents SET status = %s,output = %s,watsonresponse= %s,documentconfidence= %s,processtime= %s,timeprocessed = %s WHERE documentid = %s",
    GetSQLValueString( 1, "int" ),
    GetSQLValueString( mysql_real_escape_string( $resultString ), "text" ),
    GetSQLValueString( mysql_real_escape_string( $response2 ), "text" ),
    GetSQLValueString( mysql_real_escape_string( $percent ), "double" ),
    GetSQLValueString( mysql_real_escape_string( $timeAdded ), "text" ),
    GetSQLValueString( mysql_real_escape_string( $endtime ), "date" ),
    GetSQLValueString( $did, "int" ) );

//echo "insertSQL: " . $insertSQL;

mysql_select_db( $database_transcribe, $transcribe );
$Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );

try {

    unlink( "uploads/{$filename}" );
} catch ( Exception $e ) {

    echo "could not delete file. ";

    //echo $e->getMessage(); // will print Exception message defined above.
}

if ( isset( $_POST[ 'mobile' ] ) ) {
    $status .= "transcription completed";
} else {
    echo "transcription completed";
}

//update user credits

mysql_select_db( $database_transcribe, $transcribe );
$query_rsUserInfo = sprintf( "SELECT credits FROM users WHERE userid = %s", GetSQLValueString( $uid, "int" ) );
$rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
$row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );

$credits = $row_rsUserInfo[ 'credits' ];
$remainingCredits = floatval( $credits ) - floatval( $estimatedCost );


$updateSQL = sprintf( "UPDATE users SET credits=%s WHERE userid=%s",
    GetSQLValueString( number_format( $remainingCredits, 2 ), "text" ),
    GetSQLValueString( $uid, "int" ) );

mysql_select_db( $database_transcribe, $transcribe );
$Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );

if ( isset( $_POST[ 'mobile' ] ) ) {
    $status .= " user credits updated";

    $myObj = new stdClass;
    $myObj->status = $status;
    $myObj->did = blankNull( strval( $did ) );

    echo "{\"data\":";
    echo "{\"transcribeData\":";
    echo json_encode( $myObj );
    echo "}";
    echo "}";
} else {
    echo "\nuser credits updated";
}

?>