<?php
require_once( 'Connections/transcribe.php' );
include( "auth.php" );

include( "includes/appstatus.php" );
include( "functions.php" );
include( "includes/nav-query.php" );

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
    $updateSQL = sprintf( "UPDATE users SET username=%s, email=%s, password=%s, firstname=%s, lastname=%s WHERE userid=%s",
        GetSQLValueString( $_POST[ 'username' ], "text" ),
        GetSQLValueString( $_POST[ 'email' ], "text" ),
        GetSQLValueString( $_POST[ 'password' ], "text" ),
        GetSQLValueString( $_POST[ 'firstname' ], "text" ),
        GetSQLValueString( $_POST[ 'lastname' ], "text" ),
        GetSQLValueString( $_POST[ 'uid' ], "int" ) );

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );
}

$colname_rsUserInfo = "-1";
if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsUserInfo = $_SESSION[ 'uid' ];
}
mysql_select_db( $database_transcribe, $transcribe );
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
    <script src="jquery/jquery-1.11.1.min.js"></script>
    <script src="side-nav.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <title>My Account - AIScribe</title>
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
                <p id="headerLbl">My Account</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">

                <p>&nbsp;</p>
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>

                        <tr>
                            <td width="8%"><strong>Username</strong>
                            </td>
                            <td width="92%"><input name="username" type="text" id="username" value="<?php echo $row_rsUserInfo['username']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Password</strong>
                            </td>
                            <td><input name="password" type="password" id="password" value="<?php echo $row_rsUserInfo['password']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Email</strong>
                            </td>
                            <td><input name="email" type="text" id="email" value="<?php echo $row_rsUserInfo['email']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>First Name</strong>
                            </td>
                            <td><input name="firstname" type="text" id="firstname" value="<?php echo $row_rsUserInfo['firstname']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Last Name</strong>
                            </td>
                            <td><input name="lastname" type="text" id="lastname" value="<?php echo $row_rsUserInfo['lastname']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Credits</strong>
                            </td>
                            <td>$
                                <?php echo number_format($row_rsUserInfo['credits'],2); ?><br>
                                <a href="buy-credits.php">buy credits</a>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" name="submit" id="submit" value="Submit">
                                <input type="hidden" name="uid" id="uid" value="<?php echo $_SESSION['uid']; ?>">
                            </td>
                        </tr>

                    </tbody>
                </table>

                <p>&nbsp;
                </p>
                <input type="hidden" name="MM_update" value="form1">
            </form>

            <p id="docContentDiv">&nbsp;</p>
        </div>
    </div>

    <?php include("includes/side-nav.php");?>

</body>
</html>
<?php
mysql_free_result( $rsUserInfo );
?>