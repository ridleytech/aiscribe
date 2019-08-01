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
    <link rel="stylesheet" href="translate.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="corpus-new.js"></script>
    <script src="side-nav.js"></script>
    <title>Add Corpus - AIScribe</title>
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
                <option>Choose custom model...</option>

                <?php 
                                
                do {     
                
                if(isset($_GET['cid']))
                {
                    if(de($_GET['cid']) == $row_rsModelInfo['customizationid'])
                    {
                        $selected = " selected";
                    }
                    else
                    {
                        $selected = "";
                    }
                }
                
                ?>

                <option value="<?php echo $row_rsModelInfo['customizationid'];?>" <?php echo $selected;?>>
                    <?php echo $row_rsModelInfo['modelname'];?>
                </option>

                <?php } while ($row_rsModelInfo = mysql_fetch_assoc($rsModelInfo)); ?>

            </select>


            <div id="selectFileBtn" class="clearfix">
                <p id="fileBtnTxt">SELECT FILE</p>
            </div>

            <div>
                <p id="fileNameTxt"></p>
                <p id="languageTxt"></p>
                <p id="fileLbl">.txt</p>
            </div>

            <div id="transcribeBtn" class="clearfix">
                <p id="TRANSCRIBE">UPLOAD</p>
            </div>

            <input type="file" id="SELECT_FILE">
            <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']?>">

        </div>
    </div>

    <?php include("includes/side-nav.php");?>

</body>
</html>