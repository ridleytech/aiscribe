<?php

require_once( 'Connections/transcribe.php' );

include( "functions.php" );

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "my-files.php";
  $MM_redirectLoginFailed = "login.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_transcribe, $transcribe);
  
  $LoginRS__query=sprintf("SELECT username, password, userid FROM users WHERE username=%s AND password=%s",
    GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $transcribe) or die(mysql_error());
    $row_rsUserInfo = mysql_fetch_assoc( $LoginRS );
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
     $loginStrGroup = "";
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
    $_SESSION['uid'] = $row_rsUserInfo['userid'];

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
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
<title>Account Login - AIScribe</title>
</head>

<body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <a href="index.php"><img id="logo" src="img/logo.png" class="image"/></a>
        </div>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Login</p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <form action="<?php echo $loginFormAction; ?>" id="form1" name="form1" method="POST">

                <p>&nbsp;</p>
                <table width="100%" cellpadding="5" cellspacing="5">
                    <tbody>
                        <tr>
                            <td width="12%">Username</td>
                            <td width="88%"><input name="username" type="text" id="username" value="<?php if(isset($_POST['username'])) {echo $_POST['username'];} ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td><input name="password" type="password" id="password" value="">
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