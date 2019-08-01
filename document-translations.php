<?php require_once('Connections/transcribe.php'); 

include("functions.php");
include("en-de.php");
include("includes/nav-query.php");

$colname_rsFiles = "-1";
if (isset($_GET['did'])) {
  $colname_rsFiles = de($_GET['did']);
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsFiles = 10;
$pageNum_rsFiles = 0;
if (isset($_GET['pageNum_rsFiles'])) {
  $pageNum_rsFiles = $_GET['pageNum_rsFiles'];
}
$startRow_rsFiles = $pageNum_rsFiles * $maxRows_rsFiles;

mysql_select_db($database_transcribe, $transcribe);
$query_rsFiles = sprintf("SELECT a.*,b.* FROM (SELECT * FROM translations WHERE documentid = {$colname_rsFiles} AND translation IS NOT NULL) as a INNER JOIN (select filename,documentid FROM documents) as b ON a.documentid = b.documentid ORDER by a.displayLang");

//echo $query_rsFiles;

$query_limit_rsFiles = sprintf("%s LIMIT %d, %d", $query_rsFiles, $startRow_rsFiles, $maxRows_rsFiles);
$rsFiles = mysql_query($query_limit_rsFiles, $transcribe) or die(mysql_error());
$row_rsFiles = mysql_fetch_assoc($rsFiles);

if (isset($_GET['totalRows_rsFiles'])) {
  $totalRows_rsFiles = $_GET['totalRows_rsFiles'];
} else {
  $all_rsFiles = mysql_query($query_rsFiles);
  $totalRows_rsFiles = mysql_num_rows($all_rsFiles);
}
$totalPages_rsFiles = ceil($totalRows_rsFiles/$maxRows_rsFiles)-1;

$queryString_rsFiles = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsFiles") == false && 
        stristr($param, "totalRows_rsFiles") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsFiles = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsFiles = sprintf("&totalRows_rsFiles=%d%s", $totalRows_rsFiles, $queryString_rsFiles);



?>
<!DOCTYPE html>
<html>

    <head>
	<link rel="stylesheet" href="boilerplate.css">
	<link rel="stylesheet" href="my-files.css">
	<meta charset="utf-8">
	<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">
        <script src="jquery/jquery-1.11.1.min.js"></script>
        <script src="side-nav.js"></script>
    </head>
    <body>

    <div id="primaryContainer" class="primaryContainer clearfix">
        <div id="headerBG" class="clearfix">
            <span style="font-size:30px;cursor:pointer"><img id="navIcon" name="navIcon" src="img/Hamburger_icon.png" class="image"/></span>
            <img id="logo" src="img/logo.png" class="image" />
        </div>
        <?php include("includes/nav.php");?>
        <div id="titleDiv" class="clearfix">
            <div id="headerTxtBG" class="clearfix">
                <p id="headerLbl">Document Translations: <?php echo $row_rsFiles['filename']; ?></p>
            </div>
        </div>
        <div id="contentBG" class="clearfix">
            <p>&nbsp;</p>
            <table width="100%" cellpadding="5" cellspacing="5">
              <tbody>
                <tr>
                  <td width="26%"><strong>Language</strong></td>
                  <td width="18%"><strong>Model</strong></td>
                  <td width="4%" align="center">&nbsp;</td>
                  <td width="52%">&nbsp;</td>
                </tr>
                  
                   <?php do { ?> 
                <tr>
                  <td><?php echo $row_rsFiles['displayLang']; ?></td>
                  <td><?php echo $row_rsFiles['language']; ?></td>
                  <td align="center"><a href="edit-translation.php?tid=<?php echo urlencode(en($row_rsFiles['translationid'])); ?>&l=<?php echo urlencode(en($row_rsFiles['language'])); ?>&did=<?php echo urlencode($_GET['did']); ?>">edit</a></td>
                    
                  <td>&nbsp;</td>
                  </tr>
                
                  
                  <?php } while ($row_rsFiles = mysql_fetch_assoc($rsFiles)); ?>
                  
                  
              </tbody>
            </table>
            
            <div style="margin-bottom: 20px; margin-top: 20px;">
    <?php if ($pageNum_rsFiles > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsFiles=%d%s", $currentPage, 0, $queryString_rsFiles); ?>">First</a> 
      <?php } // Show if not first page ?>
    <?php if ($pageNum_rsFiles > 0) { // Show if not first page ?>
      <a href="<?php printf("%s?pageNum_rsFiles=%d%s", $currentPage, max(0, $pageNum_rsFiles - 1), $queryString_rsFiles); ?>">Previous</a> 
      <?php } // Show if not first page ?>
    <?php if ($pageNum_rsFiles < $totalPages_rsFiles) { // Show if not last page ?>
  <a href="<?php printf("%s?pageNum_rsFiles=%d%s", $currentPage, min($totalPages_rsFiles, $pageNum_rsFiles + 1), $queryString_rsFiles); ?>">Next</a> 
  <?php } // Show if not last page ?>
    <?php if ($pageNum_rsFiles < $totalPages_rsFiles) { // Show if not last page ?>
      <a href="<?php printf("%s?pageNum_rsFiles=%d%s", $currentPage, $totalPages_rsFiles, $queryString_rsFiles); ?>">Last</a> 
      <?php } // Show if not last page ?>
</div>
            <a href="my-files.php">Back</a>
            <p id="docContentDiv"></p>
        </div>
    </div>
        <?php include("includes/side-nav.php");?>
    </body>
</html>
<?php
mysql_free_result($rsFiles);
?>
  