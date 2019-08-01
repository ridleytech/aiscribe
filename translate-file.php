<?php

require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "en-de.php" );
include( "includes/nav-query.php" );
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="translate-file.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="translate-file.js"></script>
    <script src="side-nav.js"></script>
<title>Translate File - AIScribe</title>
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
                <p id="headerLbl">Translate File</p>
                <p id="resultLbl">
                    95&#x25;
                </p>
            </div>
            <div id="exportBG" class="clearfix">
                <img id="pdf-icon" src="img/pdf-icon.png" class="image"/>
                <img id="word-icon" src="img/word-icon.png" class="image"/>
                <img id="txt-icon" src="img/txt-icon.png" class="image"/>
            </div>
        </div>
        <div id="contentDiv" class="clearfix">
            <div id="contentBG" class="clearfix">
                <p id="sourceLbl">Select output</p>
                <select name="sourceLanguage" id="sourceLanguage">
                    <option>Choose Language</option>
                    <option value="en-ar">Arabic</option>
                    <option value="en-cs">Czech</option>
                    <option value="en-da">Danish</option>
                    <option value="en-de">German</option>
                    <option value="en-es">Spanish</option>
                    <option value="en-fi">Finnish</option>
                    <option value="en-fr">French</option>
                    <option value="en-hi">Hindi</option>
                    <option value="en-it">Italian</option>
                    <option value="en-ja">Japanese</option>
                    <option value="en-ko">Korean</option>
                    <option value="en-nb">Norwegian Bokmal</option>
                    <option value="en-nl">Dutch</option>
                    <option value="en-pl">Polish</option>
                    <option value="en-pt">Portuguese</option>
                    <option value="en-ru">Russian</option>
                    <option value="en-sv">Swedish</option>
                    <option value="en-tr">Turkish</option>
                    <option value="en-zh">Simplified Chinese</option>
                    <option value="en-zh-TW">Traditional Chinese</option>
                </select>

                <p id="docContentDiv"></p>

                <textarea id="editTxt"></textarea>

                <img id="translate_button" src="img/translate%20button.png" class="image"/>
                <img id="edit_button" src="img/edit%20button.png" class="image"/>
                <img id="save_button" src="img/save%20button.png" class="image"/>
                <img id="cancel_button" src="img/cancel%20button.png" class="image"/>
            </div>
        </div>
        <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
        <input type="hidden" name="did" id="did" value="<?php echo $_GET['did']; ?>">
    </div>

    <?php include("includes/side-nav.php");?>

</body>
</html>