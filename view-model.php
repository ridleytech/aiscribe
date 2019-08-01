<?php

require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "en-de.php" );
include( "includes/nav-query.php" );

//$_POST['customizationid'] = de($_GET['cid']);
//    
//    echo "cid: ".$_POST['customizationid']."<br>";

if ( isset( $_GET[ 't' ] ) && isset( $_GET[ 'cid' ] ) ) {
    //$_POST[ 'cid' ] = de( $_GET[ 'cid' ] );

    //echo "cid: ".$_POST['customizationid']."<br>";

    include( "IBM-train-model.php" );
}

mysql_select_db( $database_transcribe, $transcribe );
$query_rsModelInfo = sprintf( "SELECT * FROM custommodels WHERE userid = %s AND active = 1", GetSQLValueString( $colname_rsModelInfo, "int" ) );
$rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
$row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );
$totalRows_rsModelInfo = mysql_num_rows( $rsModelInfo );

$query_rsModelOptions = sprintf( "SELECT a.*, b.modelname2 FROM (SELECT * FROM custommodels WHERE modelid = %s) as a INNER JOIN (SELECT modelname as 'modelname2',code from modeloptions) as b on a.code = b.code", GetSQLValueString( de( $_GET[ 'mid' ] ), "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );

$query_rsCorpusInfo = sprintf( "SELECT * FROM corpus WHERE customizationid = %s", GetSQLValueString( de( $_GET[ 'cid' ] ), "text" ) );
$rsCorpusInfo = mysql_query( $query_rsCorpusInfo, $transcribe )or die( mysql_error() );
$row_rsCorpusInfo = mysql_fetch_assoc( $rsCorpusInfo );
$totalRows_rsCorpusInfo = mysql_num_rows( $rsCorpusInfo );

$query_rsFilesInfo = sprintf( "SELECT * FROM documents WHERE customizationid = %s", GetSQLValueString( de( $_GET[ 'cid' ] ), "text" ) );
$rsFilesInfo = mysql_query( $query_rsFilesInfo, $transcribe )or die( mysql_error() );
$row_rsFilesInfo = mysql_fetch_assoc( $rsFilesInfo );
$totalRows_rsFilesInfo = mysql_num_rows( $rsFilesInfo );

//echo $query_rsFilesInfo;

//if(isset($_GET['mid']) && (isset($_GET['t']) && $_GET['t'] == 1))
//{
//    //IBM Train service
//    //start timer
//    //move to JS
//}

//"SELECT a.*, b.modelname2 FROM (SELECT * FROM custommodels WHERE userid = {$colname_rsFiles} AND active = 1 ORDER by datecreated DESC) as a INNER JOIN (SELECT modelname as 'modelname2',code from modeloptions) as b on a.code = b.code"

?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-files.css">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="functions.js"></script>
    <script src="side-nav.js"></script>
    <script src="train-model.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">

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
<title>Model Info - AIScribe</title>
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
                <p id="headerLbl">Model Info</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">

                <p>&nbsp;</p>
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>
                        <?php /*?>
                        <?php if(isset($status)) { ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td style="color: red">
                                <?php echo $status; ?>
                            </td>
                        </tr>

                        <?php } ?>
                        <?php */?>
                        <tr>
                            <td width="16%"><strong>Model Name</strong>
                            </td>
                            <td width="84%">
                                <?php echo $row_rsModelOptions['modelname'];?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Language</strong>
                            </td>
                            <td>
                                <?php echo str_replace("- Narrowband","",$row_rsModelOptions['modelname2']);?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Description</strong>
                            </td>
                            <td>
                                <?php echo $row_rsModelOptions['modeldescription'];?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status</strong>
                            </td>
                            <td id="statusCell">Getting status...
                                <?php /*?>
                                <?php echo $row_rsModelOptions['status'];?>
                                <?php */?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Corpus File</strong>
                            </td>
                            <td>
                                <?php if($totalRows_rsCorpusInfo){echo $row_rsCorpusInfo['filename']. ".txt";} else {echo "None";}?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Transcriptions</strong>
                            </td>
                            <td>
                                <?php echo $totalRows_rsFilesInfo ;?>
                            </td>
                        </tr>
                        <tr>
                            <td><a href="delete-model-confirmation.php?mid=<?php echo urlencode(en($row_rsModelOptions['modelid']))?>">Delete</a>
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><a href="my-models.php">Back</a>
                            </td>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>

            <p id="docContentDiv">&nbsp;</p>
        </div>
        <input type="hidden" name="code" id="code" value="<?php echo en($row_rsModelOptions['code']); ?>">
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
<?php
mysql_free_result( $rsModelInfo );
?>