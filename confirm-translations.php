<?php

require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "en-de.php" );
include( "includes/nav-query.php" );

//$_SESSION['languages'] = "en,ar,fr";

$languages = explode( ",", $_SESSION[ 'languages' ] );
$displayLanguages = explode( ",", $_SESSION[ 'displayLanguages' ] );

$colname_rsDocInfo = "-1";
if ( isset( $_GET[ 'did' ] ) ) {
    $colname_rsDocInfo = $_GET[ 'did' ];
}

mysql_select_db( $database_transcribe, $transcribe );
$query_rsDocInfo = sprintf( "SELECT * FROM documents WHERE documentid = %s", GetSQLValueString( de( $colname_rsDocInfo ), "int" ) );
$rsDocInfo = mysql_query( $query_rsDocInfo, $transcribe )or die( mysql_error() );
$row_rsDocInfo = mysql_fetch_assoc( $rsDocInfo );
$totalRows_rsDocInfo = mysql_num_rows( $rsDocInfo );

$len1 = strlen( $row_rsDocInfo[ 'output' ] );

//$0.02 USD /THOUSAND CHAR
//$0.10 USD /THOUSAND CHAR (custom)

$chars = ceil( $len1 / 1000 ) * 1000;

$custom = false;

if ( $custom ) {
    $rate = 0.10;
} else {
    $rate = 0.02;
}

//$markup = .5;
$markup = 5;

$cost = ( $chars / 1000 ) * $rate;

//echo "cost: {$cost}<br>";

$total = ( $cost * $markup ) + $cost;

//echo "total : {$total }<br>";

if ( $total < 1 ) {
    $total = 1;
}

$total = count( $languages ) * $total;

$colname_rsUserInfo = "-1";
if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsUserInfo = $_SESSION[ 'uid' ];
}
$query_rsUserInfo = sprintf( "SELECT * FROM users WHERE userid = %s", GetSQLValueString( $colname_rsUserInfo, "int" ) );
$rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
$row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );
$totalRows_rsUserInfo = mysql_num_rows( $rsUserInfo );

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
    <script src="side-nav.js"></script>
    <script src="confirm-translations.js"></script>
    <title>My Custom Models</title>
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
                <p id="headerLbl">Translate Files:
                    <?php echo $row_rsDocInfo['filename'];?>
                </p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <p>&nbsp;</p>
            <div id="renderContent">
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>
                        <tr>
                            <td width="31%"><strong>Language</strong>
                            </td>
                            <td width="5%" align="center" id="statusCell"></td>
                            <td width="59%"></td>
                        </tr>

                        <?php $ind = 0; foreach($displayLanguages as $lang) { ?>
                        <tr>
                            <td>English->
                                <?php echo $lang; ?>
                            </td>
                            <td id="<?php echo $languages[$ind]; ?>" align="center"></td>
                            <td></td>
                        </tr>
                        <?php $ind++;} ?>
                    </tbody>
                </table>
            </div>
            <div id="resultDiv" style="margin-top:20px">
                <p>Remaining Credits: $
                    <?php echo number_format($row_rsUserInfo['credits'],2); ?><br>Subtotal: $
                    <?php echo number_format($total,2); ?>
                </p>
                <p>&nbsp;</p>
                <p><a href="transcription-result.php?did=<?php echo urlencode($_GET['did']);?>">Back</a>
                </p>
                <input name="submitBtn" type="button" class="image" id="submitBtn" value="Submit Order" style="margin-top: 20px;">
                <input type="hidden" id="languages" value="<?php echo implode(" , ",$languages);?>">
                <input type="hidden" id="displayLanguages" value="<?php echo implode(" , ",$displayLanguages);?>">
                <input type="hidden" id="uid" value="<?php echo $colname_rsUserInfo;?>">
                <input type="hidden" id="did" value="<?php echo $_GET['did'];?>">
                <input type="hidden" id="subtotal" value="<?php echo $total;?>">
                <input type="hidden" id="credits" value="<?php echo $row_rsUserInfo['credits'];?>">
            </div>
        </div>
    </div>
    <?php include("includes/side-nav.php");?>
</body>
</html>
<?php mysql_free_result( $rsFiles ); ?>