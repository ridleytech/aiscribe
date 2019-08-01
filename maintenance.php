<?php
require_once( 'Connections/transcribe.php' );
include( "functions.php" );

mysql_select_db( $database_transcribe, $transcribe );
$query_rsStatusInfo = sprintf( "SELECT * FROM appstatus order by datecreated DESC LIMIT 1" );
$rsStatusInfo = mysql_query( $query_rsStatusInfo, $transcribe )or die( mysql_error() );
$row_rsStatusInfo = mysql_fetch_assoc( $rsStatusInfo );

if ( $row_rsStatusInfo[ 'status' ] == 1 ) {
    header( "Location: " . "my-files.php" );
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="maintenance.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <title>Site Maintenance</title>
</head>

<body>
    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>

            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Maintenance</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <div id="renderContent"></div>
            <p id="docContentDiv">
                <?php echo $row_rsStatusInfo['displaymessage'];?>
            </p>
        </div>
    </div>
</body>
<div id="tipDiv">
    <div id="tipContent">
        <div id="closeTip" style="cursor: pointer;">X</div>
        <p id="tipTxt"></p>
    </div>
</div>
</html>