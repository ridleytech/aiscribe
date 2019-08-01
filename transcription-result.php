<?php

require_once( 'Connections/transcribe.php' );
include("functions.php");
include( "includes/appstatus.php" );
include( "en-de.php" );
include("includes/nav-query.php");

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="transcription-result.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="transcription-result.js"></script>
    <script src="side-nav.js"></script>
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
                <p id="headerLbl">Transcription Result</p>
            </div>
            <div id="exportBG" class="clearfix">
                <img id="pdf-icon" src="img/pdf-icon.png" class="image"/>
                <img id="word-icon" src="img/word-icon.png" class="image"/>
                <img id="txt-icon" src="img/txt-icon.png" class="image"/>
            </div>
        </div>
        <div id="contentDiv" class="clearfix">
            <div id="contentBG" class="clearfix">
                <div id="confidenceDiv" style="width:80%;float:left;margin-left: 35px;margin-top: 10px;"></div>
                <p id="docContentDiv"></p>
                <textarea id="editTxt"></textarea>

                <img id="edit_button" src="img/edit%20button.png" class="image"/>
                <img id="save_button" src="img/save%20button.png" class="image"/>
                <img id="cancel_button" src="img/cancel%20button.png" class="image"/>

            </div>
            <div id="translationDiv" class="clearfix">
                <div id="green_header" class="clearfix">
                </div>
                <p id="_10_off_document_translation">Document translation</p>
                <p id="Translate_this_document_from_your_choice_of_23_languages">
                    <span id="textspan">Translate this document from your choice of </span><span id="textspan1">20 languages</span>
                </p>
                <div id="listBox" class="clearfix"></div>
                <img id="checkoutBtn" src="img/checkout%20button.png" class="image"/>

            </div>
        </div>
        <input type="hidden" name="uid" id="uid" value="<?php echo en($_SESSION['uid']); ?>">
        <input type="hidden" name="did" id="did" value="<?php echo $_GET['did']; ?>">
    </div>
    <?php include("includes/side-nav.php");?>
</body>
</html>