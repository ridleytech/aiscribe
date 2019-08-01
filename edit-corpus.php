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
    <link rel="stylesheet" href="edit-translation.css">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style type="text/css">
        #tipDiv {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 100%;
            height: 100%;
            z-index: 100;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
        }
        
        .train {
            color: green;
        }
        
        #closeTip {
            float: right;
            width: 100%;
            text-align: right;
            margin-bottom: 10px;
        }
        
        #tipContent p {
            margin-bottom: 20px;
        }
        
        #tipContent {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 400px;
            height: auto;
            margin-top: -150px;
            margin-left: -200px;
            background-color: white;
            padding: 20px;
            color: rgb(55, 55, 55);
        }
    </style>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="edit-corpus.js"></script>
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
                <p id="headerLbl">Edit Corpus</p>
            </div>
            <div id="exportBG" class="clearfix">
                <img id="pdf-icon" src="img/pdf-icon.png" class="image"/>
                <img id="word-icon" src="img/word-icon.png" class="image"/>
                <img id="txt-icon" src="img/txt-icon.png" class="image"/>
            </div>
        </div>
        <div id="contentDiv" class="clearfix">
            <div id="contentBG" class="clearfix">
                <div id="statusDiv" style=" margin-left: 35px;"></div>
                <p id="docContentDiv"></p>

                <textarea id="editTxt"></textarea>

                <img id="edit_button" src="img/edit%20button.png" class="image"/>
                <img id="save_button" src="img/save%20button.png" class="image"/>
                <img id="cancel_button" src="img/cancel%20button.png" class="image"/>
            </div>
        </div>

        <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
        <input type="hidden" name="cpid" id="cpid" value="<?php echo $_GET['cpid']; ?>">
        <input type="hidden" name="cid" id="cid" value="<?php echo $_GET['cid']; ?>">
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