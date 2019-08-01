<?php
require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

if ( isset( $_GET[ 't' ] ) && isset( $_GET[ 'cid' ] ) ) {
    
    //$_POST[ 'customizationid' ] = de( $_GET[ 'cid' ] );

    //echo "cid: ".$_POST['customizationid']."<br>";

    include( "IBM-train-model.php" );
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
                <p><?php echo $status;?></p>
                <p>&nbsp;</p>
                
                </p>
            </form>
        </div>
    </div>

    <?php include("includes/side-nav.php");?>

</body>
</html>
<?php
mysql_free_result( $rsModelInfo );
?>