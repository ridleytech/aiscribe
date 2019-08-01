<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "en-de.php" );
include( "includes/nav-query.php" );

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

if ( ( ( isset( $_GET[ "d" ] ) ) && ( $_GET[ "d" ] == 1 ) ) && ( isset( $_GET[ "mid" ] ) ) && ( isset( $_GET[ "cid" ] ) ) ) {

    //delete models IBM

    include( "IBM-delete-model.php" );

} else {
    $query_rsModelOptions = sprintf( "SELECT * FROM custommodels WHERE modelid = %s", GetSQLValueString( de( $_GET[ 'mid' ] ), "int" ) );
    $rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
    $row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
    $totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );
}

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-files.css">

    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="side-nav.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
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
                <p id="headerLbl">Edit Custom Model</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">
                <p>&nbsp;</p>
                <p>Delete model
                    <strong>
                        <?php echo $row_rsModelOptions['modelname']?>
                    </strong>?</p>
                <p>&nbsp;</p>
                <p><a href="delete-model-confirmation.php?d=1&mid=<?php echo urlencode(en($row_rsModelOptions['modelid']))?>&cid=<?php echo urlencode(en($row_rsModelOptions['customizationid']))?>">Yes</a> <a href="view-model.php?mid=<?php echo urlencode(en($row_rsModelOptions['modelid']))?>">Cancel</a>
                </p>
                <p>&nbsp;
                </p>
                <input type="hidden" name="MM_update" value="form1">
                <input type="hidden" name="MM_insert" value="form1">
            </form>
            <p id="docContentDiv">&nbsp;</p>
        </div>
    </div>
    <?php include("includes/side-nav.php");?>
</body>
</html>
<?php
mysql_free_result( $rsModelInfo );
?>