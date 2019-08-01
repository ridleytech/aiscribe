<?php require_once('Connections/transcribe.php'); 

include("functions.php");


//$_POST['did'] = "1";

$colname_rsFiles = "-1";

if (isset($_POST['did'])) {
  $colname_rsFiles = $_POST['did'];
}

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsFiles = 25;
$pageNum_rsFiles = 0;
if (isset($_POST['pageNum_rsFiles'])) {
  $pageNum_rsFiles = $_POST['pageNum_rsFiles'];
}
$startRow_rsFiles = $pageNum_rsFiles * $maxRows_rsFiles;

mysql_select_db($database_transcribe, $transcribe);
$query_rsFiles = sprintf("SELECT a.*,b.* FROM (SELECT * FROM translations WHERE documentid = {$colname_rsFiles} AND translation IS NOT NULL) as a INNER JOIN (select filename,documentid FROM documents) as b ON a.documentid = b.documentid ORDER by a.displayLang");

//echo $query_rsFiles;

$query_limit_rsFiles = sprintf("%s LIMIT %d, %d", $query_rsFiles, $startRow_rsFiles, $maxRows_rsFiles);
$rsFiles = mysql_query($query_limit_rsFiles, $transcribe) or die(mysql_error());
$row_rsFiles = mysql_fetch_assoc($rsFiles);

if (isset($_POST['totalRows_rsFiles'])) {
  $totalRows_rsFiles = $_POST['totalRows_rsFiles'];
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


if($totalRows_rsFiles)
{
    

 do { 

    $object = new stdClass();

    $object->displayLang = blankNull( $row_rsFiles[ 'displayLang' ] );
    $object->language = blankNull( $row_rsFiles[ 'language' ] );
    $object->translationid = blankNull( $row_rsFiles[ 'translationid' ] );
    $object->did = blankNull( $colname_rsFiles );

    $list[] = $object;

    } while ($row_rsFiles = mysql_fetch_assoc($rsFiles)); 
    
}

echo "{\"data\":";
echo "{\"translationData\":";
echo json_encode( $list );
echo "}";
echo "}";

?>