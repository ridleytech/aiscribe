<?php

require_once( 'Connections/transcribe.php' );
include("functions.php");
include("en-de.php");
include("includes/nav-query.php");

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="edit-translation.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="edit-translation.js"></script>
    <script src="side-nav.js"></script>
</head>

<body>
    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerDiv" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>

            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <?php include("includes/nav.php");?>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Edit Translation</p>
            </div>
            <div id="exportBG" class="clearfix">
                <img id="pdf-icon" src="img/pdf-icon.png" class="image"/>
                <img id="word-icon" src="img/word-icon.png" class="image"/>
                <img id="txt-icon" src="img/txt-icon.png" class="image"/>
            </div>
        </div>
        <div id="contentDiv" class="clearfix">
            <div id="contentBG" class="clearfix">

                <p id="langLbl">Output Language:</p>

                <p id="docContentDiv"></p>

                <textarea id="editTxt"></textarea>

                <img id="edit_button" src="img/edit%20button.png" class="image"/>
                <img id="save_button" src="img/save%20button.png" class="image"/>
                <img id="cancel_button" src="img/cancel%20button.png" class="image"/>
            </div>
        </div>
        
        <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
        <input type="hidden" name="tid" id="tid" value="<?php echo $_GET['tid']; ?>">
        <input type="hidden" name="l" id="l" value="<?php echo $_GET['l']; ?>">
        <input type="hidden" name="did" id="did" value="<?php echo $_GET['did']; ?>">
    </div>

    <?php include("includes/side-nav.php");?>
</body>
</html>