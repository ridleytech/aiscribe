<?php
// *** Logout the current user.
$logoutGoTo = "index.php";
if (!isset($_SESSION)) {
  session_start();
}
$_SESSION['MM_Username'] = NULL;
$_SESSION['uid'] = NULL;
$_SESSION['MM_UserGroup'] = NULL;
$_SESSION['tempid'] = NULL;
unset($_SESSION['MM_Username']);
unset($_SESSION['uid']);
unset($_SESSION['MM_UserGroup']);
unset($_SESSION['tempid']);
if ($logoutGoTo != "") {header("Location: $logoutGoTo");
exit;
}
?>
