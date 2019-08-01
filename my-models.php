<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "includes/nav-query.php" );

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-files.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="my-models.js"></script>
    <script src="side-nav.js"></script>
    <title>My Custom Models - AIScribe</title>
</head>

<body>
    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>

            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <?php include("includes/nav.php");?>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">My Custom Models</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <p>&nbsp;</p>
            <div id="renderContent"></div>
            <p id="docContentDiv"></p>
        </div>
    </div>

    <?php include("includes/side-nav.php");?>
</body>
<div id="tipDiv">
    <div id="tipContent">
        <div id="closeTip" style="cursor: pointer;">X</div>
        <p id="tipTxt"></p>
    </div>
</div>
</html>
<?php mysql_free_result( $rsFiles ); ?>