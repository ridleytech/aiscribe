<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );
include( "includes/nav-query.php" );

$date = date( "Y-m-d H:i:s" );

$query_rsModelOptions = sprintf( "SELECT * FROM modeloptions WHERE active = %s", GetSQLValueString( 1, "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );

if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

    //var_dump($_POST);

    $success = true;

    if ( !isset( $_POST[ 'modelname' ] ) || $_POST[ 'modelname' ] == "" ) {
        $status = "Please enter a model name";
        $success = false;
    }

    if ( !isset( $_POST[ 'modelLanguage' ] ) || $_POST[ 'modelLanguage' ] == "Choose model..." ) {
        $status = "Please select language model";
        $success = false;
    }

    if ( $success == true ) {

        //var_dump($_POST);

        include( "IBM-create-model.php" );
    }

    //https://www.andiamo.co.uk/resources/iso-language-codes/
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-files.css">
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="side-nav.js"></script>
    <title>Create Custom Model - AIScribe</title>
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
                <p id="headerLbl">Create Custom Model</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">

                <p>&nbsp;</p>
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>
                        <?php if(isset($status)) { ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td style="color: red">
                                <?php echo $status; ?>
                            </td>
                        </tr>

                        <?php } ?>
                        <tr>
                            <td width="12%"><strong>Model Name</strong>
                            </td>
                            <td width="88%"><input name="modelname" type="text" id="modelname" value="<?php if(isset($_POST['modelname'])) { echo $_POST['modelname']; }?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Language Model</strong>
                            </td>
                            <td>
                                <select name="modelLanguage" id="modelLanguage">
                                    <option>Choose model...</option>
                                    <?php do { ?>

                                    <option <?php if(isset($_POST[ 'modelLanguage' ])){if($_POST[ 'modelLanguage' ] == $row_rsModelOptions[ 'code']){echo "selected";}}?> value="<?php echo $row_rsModelOptions['code'];?>">
                                        <?php echo str_replace("- Narrowband","",$row_rsModelOptions['modelname']);?>
                                    </option>

                                    <?php } while ($row_rsModelOptions = mysql_fetch_assoc($rsModelOptions)); ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Description</strong>
                            </td>
                            <td><textarea name="modeldescription" id="modeldescription"><?php if(isset($_POST['modeldescription'])) { echo $_POST['modeldescription']; }?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" name="submit" id="submit" value="Submit">
                                <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']?>">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="MM_insert" value="form1">
            </form>
        </div>
    </div>

    <?php include("includes/side-nav.php");?>

</body>
</html>