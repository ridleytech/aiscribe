<?php 

require_once( 'Connections/transcribe.php' );

include("functions.php"); 

$uid = 1;
$estimatedCost = "1.00";

mysql_select_db( $database_transcribe, $transcribe );
$query_rsUserInfo = sprintf( "SELECT credits FROM users WHERE userid = %s", GetSQLValueString( $uid, "int" ) );
$rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
$row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );

$credits = $row_rsUserInfo['credits'];
$remainingCredits = floatval($credits) - floatval($estimatedCost);

$formatted = number_format($remainingCredits,2);

echo "credits: {$credits}<br>";
echo "estimatedCost: {$estimatedCost}<br>";
echo "remainingCredits: {$remainingCredits}<br>";
echo "formatted: {$formatted}<br>";


?>