<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "en-de.php" );
include( "includes/nav-query.php" );

mysql_select_db( $database_transcribe, $transcribe );

$query_rsModelInfo = sprintf( "SELECT * FROM custommodels WHERE userid = %s AND active = 1", GetSQLValueString( $colname_rsModelInfo, "int" ) );
$rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
$row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );
$totalRows_rsModelInfo = mysql_num_rows( $rsModelInfo );

$query_rsModelOptions = sprintf( "SELECT * FROM modeloptions WHERE active = %s", GetSQLValueString( 1, "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );

if ( isset( $_GET[ 'cid' ] ) ) {
    $_SESSION[ 'tempid' ] = $_GET[ 'cid' ];
} else {
    $_SESSION[ 'tempid' ] = null;
    unset( $_SESSION[ 'tempid' ] );
}

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="transcribe.css">
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="transcribe.js"></script>
    <script src="side-nav.js"></script>
    <title>Transcribe File - AIScribe</title>
</head>

<body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>
            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
            <div id="uploadStatus"></div>
        </div>

        <?php include("includes/nav.php");?>

        <div id="btnDiv" class="clearfix">

            <select name="sourceLanguage" id="sourceLanguage">
                <option>Choose audio language...</option>
                <?php 
                
                do { 
                
                if(isset($_GET['l']))
                {
                    if(de($_GET['l']) == $row_rsModelOptions['code'])
                    {
                        $selected = " selected";
                    }
                    else
                    {
                        $selected = "";
                    }
                }
                
                ?>

                <option value="<?php echo $row_rsModelOptions['code'];?>" <?php echo $selected;?>>
                    <?php echo str_replace("- Narrowband","",$row_rsModelOptions['modelname']);?> </option>

                <?php } while ($row_rsModelOptions = mysql_fetch_assoc($rsModelOptions)); ?>
            </select>

            <div id="customDiv" style="margin-bottom: 10px;clear: left;"></div>

            <div id="selectFileBtn" class="clearfix">
                <p id="fileBtnTxt">SELECT FILE</p>
            </div>

            <div>
                <p id="fileNameTxt"></p>
                <p id="fileLbl">.wav, .mp3, .ogg, .flac</p>
            </div>

            <div id="transcribeBtn" class="clearfix">
                <p id="TRANSCRIBE">TRANSCRIBE</p>
            </div>

            <input type="file" id="SELECT_FILE">
        </div>
        <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
        <input type="hidden" name="tempid" id="tempid" value="<?php echo $_GET['cid']; ?>">
    </div>
    <div id="tipDiv">
        <div id="tipContent">
            <div id="closeTip">X</div>
            <p>Use broadband models for responsive, real-time applications, for example, for live-speech applications.</p>
            <p>Use narrowband models for offline decoding of telephone speech, which is the typical use and sampling rate.</p>
        </div>
    </div>

    <?php include("includes/side-nav.php");?>
</body>
</html>