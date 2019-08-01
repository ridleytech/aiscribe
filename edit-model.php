<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );
include( "includes/appstatus.php" );
include( "functions.php" );

mysql_select_db( $database_transcribe, $transcribe );
$query_rsModelInfo = sprintf( "SELECT * FROM custommodels WHERE userid = %s AND active = 1", GetSQLValueString( $colname_rsModelInfo, "int" ) );
$rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
$row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );
$totalRows_rsModelInfo = mysql_num_rows( $rsModelInfo );


$query_rsModelOptions = sprintf( "SELECT * FROM modeloptions WHERE active = %s ORDER BY sortorder", GetSQLValueString( 1, "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );


$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

if ( ( isset( $_POST[ "MM_insert" ] ) ) && ( $_POST[ "MM_insert" ] == "form1" ) ) {

    $date = date( "Y-m-d H:i:s" );


    //var_dump($_POST);

    $success = true;

    if ( !isset( $_POST[ 'modelname' ] ) || $_POST[ 'modelname' ] == "" ) {
        $status = "Please enter model name";
        $success = false;
    }

    if ( !isset( $_POST[ 'modelLanguage' ] ) || $_POST[ 'modelLanguage' ] == "Choose Model" ) {
        $status = "Please select base model";
        $success = false;
    }


    if ( $success == true ) {
        $insertSQL = sprintf( "UPDATE custommodels SET modelname=%s, modeldescription=%s WHERE modelid =%s",
            GetSQLValueString( $_POST[ 'modelname' ], "text" ),
            GetSQLValueString( $_POST[ 'modeldescription' ], "text" ),
            GetSQLValueString( $_POST[ 'modelid' ], "int" ) );

        mysql_select_db( $database_transcribe, $transcribe );
        $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

        $insertGoTo = "my-models.php";
        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
        }
        header( sprintf( "Location: %s", $insertGoTo ) );
    }

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
                            <td width="12%">Model Name</td>
                            <td width="88%"><input name="modelname" type="text" id="modelname" value="<?php echo $row_rsModelInfo['modelname'];?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Langauge</td>
                            <td>
                                <?php echo $row_rsModelOptions['modelname'];?>
                            </td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><textarea name="modeldescription" id="modeldescription"><?php echo $row_rsModelInfo['modeldescription'];?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" name="submit" id="submit" value="Submit">
                                <input type="hidden" name="modelid" id="modelid" value="<?php echo $_GET['modelid']?>">
                            </td>
                        </tr>

                    </tbody>
                </table>

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