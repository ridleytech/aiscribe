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
    <link rel="stylesheet" href="translate.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="translate-new.js"></script>
    <script src="side-nav.js"></script>
    <title>Translate File - AIScribe</title>
</head>

<body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>
            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <?php include("includes/nav.php");?>

        <div id="btnDiv" class="clearfix">

            <select name="sourceLanguage" id="sourceLanguage">
                <option>Choose output language...</option>
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

            <div id="selectFileBtn" class="clearfix">
                <p id="fileBtnTxt">SELECT FILE</p>
            </div>

            <div>
                <p id="fileNameTxt"></p>
                <p id="languageTxt"></p>
                <p id="fileLbl">.txt, .doc, .docx, .pdf</p>
            </div>

            <div id="transcribeBtn" class="clearfix">
                <p id="TRANSCRIBE">TRANSLATE</p>
            </div>


            <input type="file" id="SELECT_FILE">
            <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
        </div>
    </div>

    <?php include("includes/side-nav.php");?>

</body>
</html>