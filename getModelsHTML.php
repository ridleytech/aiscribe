<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

//test write

$colname_rsModels = "-1";
if ( isset( $_GET[ 'uid' ] ) ) {
    $colname_rsModels = $_GET[ 'uid' ];
}

if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsModels = $_SESSION[ 'uid' ];
}

$currentPage = $_SERVER[ "PHP_SELF" ];

$maxRows_rsModels = 20;
$pageNum_rsModels = 0;
if ( isset( $_GET[ 'pageNum_rsModels' ] ) ) {
    $pageNum_rsModels = $_GET[ 'pageNum_rsModels' ];
}
$startRow_rsModels = $pageNum_rsModels * $maxRows_rsModels;

mysql_select_db( $database_transcribe, $transcribe );
$query_rsModels = sprintf( "SELECT a.*, b.modelname2 FROM (SELECT * FROM custommodels WHERE userid = {$colname_rsModels} AND active = 1 ORDER by datecreated DESC) as a INNER JOIN (SELECT modelname as 'modelname2',code from modeloptions) as b on a.code = b.code" );

$query_limit_rsModels = sprintf( "%s LIMIT %d, %d", $query_rsModels, $startRow_rsModels, $maxRows_rsModels );
$rsModels = mysql_query( $query_limit_rsModels, $transcribe )or die( mysql_error() );
$row_rsModels = mysql_fetch_assoc( $rsModels );

if ( isset( $_GET[ 'totalRows_rsModels' ] ) ) {
    $totalRows_rsModels = $_GET[ 'totalRows_rsModels' ];
} else {
    $all_rsModels = mysql_query( $query_rsModels );
    $totalRows_rsModels = mysql_num_rows( $all_rsModels );
}
$totalPages_rsModels = ceil( $totalRows_rsModels / $maxRows_rsModels ) - 1;

$queryString_rsModels = "";
if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
    $newParams = array();
    foreach ( $params as $param ) {
        if ( stristr( $param, "pageNum_rsModels" ) == false &&
            stristr( $param, "totalRows_rsModels" ) == false ) {
            array_push( $newParams, $param );
        }
    }
    if ( count( $newParams ) != 0 ) {
        $queryString_rsModels = "&" . htmlentities( implode( "&", $newParams ) );
    }
}
$queryString_rsModels = sprintf( "&totalRows_rsModels=%d%s", $totalRows_rsModels, $queryString_rsModels );

?>
<style type="text/css">
    
#tipDiv {
    position: absolute;
    top: 0px;
    left: 0px;
    width: 100%;
    height: 100%;
    z-index: 100;
    background-color: rgba(0,0,0,0.5);
    display: none;
}
    .train {
        color: green;
    }
#closeTip {
    float: right;
    width: 100%;
    text-align: right;
    margin-bottom: 10px;
}
#tipContent p {
    margin-bottom: 20px;
}
#tipContent {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 400px;
    height: auto;
    margin-top: -150px;
    margin-left: -200px;
    background-color: white;
    padding: 20px;
    color: rgb(55, 55, 55);
}
</style>

<p>Custom language models allow you to expand the vocabulary of your transcription service for domain-specific terminology to improve transcription accuracy. To create custom corpora for your specific transcription needs, you must first create a model.<br><br>
<p><a href="create-model.php">Create new model</a></p><br>
<?php
if($totalRows_rsModels  > 0) {
?>

<table width="100%" cellpadding="5" cellspacing="5">
    <tbody>
        <tr>
            <td width="19%"><strong>Model Name</strong></td>
            <td width="22%"><strong>Language</strong></td>
            <td width="42%"><strong>Description</strong></td>
            <td width="17%"><strong>Status</strong></td>
        </tr>

        <?php do { 
    
            //$status = "Pending";
    
            $description = $row_rsModels['modeldescription'];
            $language = str_replace("- Narrowband","",$row_rsModels['modelname2']);
            $cid = urlencode(en($row_rsModels['customizationid']));
            //$cid = urlencode($row_rsModels['customizationid']);
            $mid = urlencode(en($row_rsModels['modelid']));
            $code = urlencode(en($row_rsModels['code']));
    
            if($row_rsModels['status'] != 3)
            {
                $status = "<a href='view-model.php?cid={$cid}&mid={$mid}'>View status</a>";
            }
            else
            {
                $status = "<a href='transcribe.php?cid={$cid}&l={$code}'>available</a><img src='img/tip.png' style='height: 20px;
            width: 20px;cursor: pointer;' id='tipIcon3' class='tipIcon3'>";
            }
        
        
        ?>
        <tr>
            <td class="hr"><a href="view-model.php?mid=<?php echo $mid; ?>&cid=<?php echo $cid; ?>"><?php echo $row_rsModels['modelname']; ?></a></td>
            <td><?php echo $language;?></td>
            <td><?php echo $description; ?></td>
            <td><?php echo $status; ?></td>
        </tr>

        <?php } while ($row_rsModels = mysql_fetch_assoc($rsModels)); ?>
    </tbody>
</table>



<p>
    <?php if ($pageNum_rsModels > 0) { // Show if not first page ?>
    <a href="<?php printf(" %s?pageNum_rsModels=%d%s ", $currentPage, 0, $queryString_rsModels); ?>">First</a>
    <?php } // Show if not first page ?>
    <?php if ($pageNum_rsModels > 0) { // Show if not first page ?>
    <a href="<?php printf(" %s?pageNum_rsModels=%d%s ", $currentPage, max(0, $pageNum_rsModels - 1), $queryString_rsModels); ?>">Previous</a>
    <?php } // Show if not first page ?>
    <?php if ($pageNum_rsModels < $totalPages_rsModels) { // Show if not last page ?>
    <a href="<?php printf(" %s?pageNum_rsModels=%d%s ", $currentPage, min($totalPages_rsModels, $pageNum_rsModels + 1), $queryString_rsModels); ?>">Next</a>
    <?php } // Show if not last page ?>
    <?php if ($pageNum_rsModels < $totalPages_rsModels) { // Show if not last page ?>
    <a href="<?php printf(" %s?pageNum_rsModels=%d%s ", $currentPage, $totalPages_rsModels, $queryString_rsModels); ?>">Last</a>
    <?php } // Show if not last page ?>
</p>

<?php } else { echo "You currently have no custom models."; }?>