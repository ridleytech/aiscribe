<?php
require_once( 'Connections/transcribe.php' );
include( "functions.php" );
include( "includes/appstatus.php" );

$editFormAction = $_SERVER[ 'PHP_SELF' ];
if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $editFormAction .= "?" . htmlentities( $_SERVER[ 'QUERY_STRING' ] );
}

if ( ( isset( $_POST[ "MM_update" ] ) ) && ( $_POST[ "MM_update" ] == "form1" ) ) {
            
    if (  strlen($_POST[ 'username' ]) == 0 ) {
        $status = "Please enter Username.";
        $success = 0;
    }

    else if (strlen($_POST[ 'password' ]) == 0 ) {
        $status = "Please enter Password.";
        $success = 0;
    }
    else if ( strlen($_POST[ 'email' ] ) == 0 ) {
        $status = "Please enter Email.";
        $success = 0;
    }    
    
    else if (strlen($_POST[ 'firstname' ]) == 0) {
        $status = "Please enter First Name.";
        $success = 0;
    }
    
    else if (strlen($_POST[ 'lastname' ]) == 0) {
        $status = "Please enter Last Name.";
        $success = 0;
    }
    else if (!preg_match("/[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}/",$_POST[ 'email' ])) {
      $status = "Please enter a valid email address.";
        $success = 0;
    }
    else
    {    
        $success = 1;
    }    
    
    if($success == 1)
    {
        mysql_select_db( $database_transcribe, $transcribe );
        $query_rsUsernameInfo = sprintf( "SELECT * FROM users WHERE username = %s", GetSQLValueString( $_POST[ 'username' ], "text" ) );
        $rsUsernameInfo = mysql_query( $query_rsUsernameInfo, $transcribe )or die( mysql_error() );
        $row_rsUsernameInfo = mysql_fetch_assoc( $rsUsernameInfo );
        $totalRows_rsUsernameInfo = mysql_num_rows( $rsUsernameInfo );


        mysql_select_db( $database_transcribe, $transcribe );
        $query_rsEmailInfo = sprintf( "SELECT * FROM users WHERE email = %s", GetSQLValueString( $_POST[ 'email' ], "text" ) );
        $rsEmailInfo = mysql_query( $query_rsEmailInfo, $transcribe )or die( mysql_error() );
        $row_rsEmailInfo = mysql_fetch_assoc( $rsEmailInfo );
        $totalRows_rsEmailInfo = mysql_num_rows( $rsEmailInfo );

        if ( $totalRows_rsUsernameInfo ) {
            $status = "Account with username <strong>{$_POST['username']}</strong> already exists.";
        }
        else if ( $totalRows_rsEmailInfo ) {
            $status = "Account with email <strong>{$_POST['email']}</strong> already exists.";
        }
        else {
            $updateSQL = sprintf( "INSERT INTO users (username,email,password,firstname,lastname,credits,usertype) VALUES (%s,%s,%s,%s,%s,%s,%s)",
                GetSQLValueString( $_POST[ 'username' ], "text" ),
                GetSQLValueString( $_POST[ 'email' ], "text" ),
                GetSQLValueString( $_POST[ 'password' ], "text" ),
                GetSQLValueString( $_POST[ 'firstname' ], "text" ),
                GetSQLValueString( $_POST[ 'lastname' ], "text" ),
                GetSQLValueString( "0.00", "text" ),
                GetSQLValueString( 1, "int" ));

            mysql_select_db( $database_transcribe, $transcribe );
            $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );

            $last_id = mysql_insert_id();

            $_SESSION['MM_Username'] = $_POST[ 'username' ];
            $_SESSION['MM_UserGroup'] = "";
            $_SESSION[ 'uid' ] = $last_id;
            
            //send welcome email
            
            $message = "Thank you joining the AIScribe community. Get started now uploading audio files and get searchable, editable transcripts in minutes. And translate them to your choice of 22 languages. Like magic.";
            
            $to = $_POST['email'];
            $subject = "Welcome to AIScribe";
            $html = $message;
            $text = $message;
            $from = "noreply@myaiscribe.com";

            include("send-email.php");

            $updateGoTo = "my-account.php";

            if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
                $updateGoTo .= ( strpos( $updateGoTo, '?' ) ) ? "&" : "?";
                $updateGoTo .= $_SERVER[ 'QUERY_STRING' ];
            }
            header( sprintf( "Location: %s", $updateGoTo ) );
        }
    }    
}

?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="boilerplate.css">
    <link rel="stylesheet" href="my-account.css">
    <script src="jquery/jquery-1.11.1.min.js"></script>

    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
</head>

<body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Create Account</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $editFormAction; ?>" id="form1" name="form1" method="POST">

                <p>&nbsp;</p>
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>

                        <?php if(isset($status)) { ?>
                        <tr>
                            <td >&nbsp;</td>
                            <td style="color: red">
                                <?php echo $status?>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr>
                            <td width="12%">Username</td>
                            <td width="88%"><input name="username" type="text" id="username" value="<?php if(isset($_POST['username'])) {echo $_POST['username'];} ?>" autocomplete="off">
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td><input name="password" type="password" id="password" autocomplete="new-password">
                            </td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input name="email" type="text" id="email" value="<?php if(isset($_POST['email'])) {echo $_POST['email'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>First Name</td>
                            <td><input name="firstname" type="text" id="firstname" value="<?php if(isset($_POST['firstname'])) {echo $_POST['firstname'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Last Name</td>
                            <td><input name="lastname" type="text" id="lastname" value="<?php if(isset($_POST['lastname'])) {echo $_POST['lastname'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" name="submit" id="submit" value="Submit">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <input type="hidden" name="MM_update" value="form1">
            </form>
            <p id="docContentDiv">&nbsp;</p>
        </div>
    </div>
</body>
</html>