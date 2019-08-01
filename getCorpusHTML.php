<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );
include( "functions.php" );
include( "en-de.php" );

//test write

$colname_rsCorpora = "-1";
if ( isset( $_GET[ 'uid' ] ) ) {
    $colname_rsCorpora = $_GET[ 'uid' ];
}

if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsCorpora = $_SESSION[ 'uid' ];
}

$currentPage = $_SERVER[ "PHP_SELF" ];

$maxRows_rsCorpora = 20;
$pageNum_rsCorpora = 0;
if ( isset( $_GET[ 'pageNum_rsCorpora' ] ) ) {
    $pageNum_rsCorpora = $_GET[ 'pageNum_rsCorpora' ];
}
$startRow_rsCorpora = $pageNum_rsCorpora * $maxRows_rsCorpora;

mysql_select_db( $database_transcribe, $transcribe );
$query_rsCorpora = sprintf( "SELECT a.*, b.code, b.modelid, b.modelname, c.modelname as 'modelname2'  FROM (SELECT * FROM corpus WHERE userid = {$colname_rsCorpora} ORDER by datecreated DESC) as a INNER JOIN (SELECT customizationid,code,modelname,modelid FROM custommodels) as b INNER JOIN (select modelname, code FROM modeloptions) as c ON a.customizationid = b.customizationid AND b.code = c.code" );

$query_limit_rsCorpora = sprintf( "%s LIMIT %d, %d", $query_rsCorpora, $startRow_rsCorpora, $maxRows_rsCorpora );
$rsCorpora = mysql_query( $query_limit_rsCorpora, $transcribe )or die( mysql_error() );
$row_rsCorpora = mysql_fetch_assoc( $rsCorpora );

if ( isset( $_GET[ 'totalRows_rsCorpora' ] ) ) {
    $totalRows_rsCorpora = $_GET[ 'totalRows_rsCorpora' ];
} else {
    $all_rsCorpora = mysql_query( $query_rsCorpora );
    $totalRows_rsCorpora = mysql_num_rows( $all_rsCorpora );
}
$totalPages_rsCorpora = ceil( $totalRows_rsCorpora / $maxRows_rsCorpora ) - 1;

$queryString_rsCorpora = "";
if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
    $newParams = array();
    foreach ( $params as $param ) {
        if ( stristr( $param, "pageNum_rsCorpora" ) == false &&
            stristr( $param, "totalRows_rsCorpora" ) == false ) {
            array_push( $newParams, $param );
        }
    }
    if ( count( $newParams ) != 0 ) {
        $queryString_rsCorpora = "&" . htmlentities( implode( "&", $newParams ) );
    }
}
$queryString_rsCorpora = sprintf( "&totalRows_rsCorpora=%d%s", $totalRows_rsCorpora, $queryString_rsCorpora );

?>

<p style="margin-bottom: 15px">Creating a corpus allows you to expand the vocabulary of your transcription service for domain-specific terminology. For industries such as medicine, government, and sports.</p>
<p>For more information on corpora, see <a href="corpora-details.php">Corpora details.</a>
</p>
<br>
<p><a href="add-corpus.php">Add corpus</a>
</p>
<br>
<?php

if ( $totalRows_rsCorpora > 0 ) {
    ?>

<table width="100%" cellpadding="5" cellspacing="5">
    <tbody>
        <tr>
            <td width="19%"><strong>Filename</strong>
            </td>
            <td width="42%"><strong>Content</strong>
            </td>
            <td width="22%"><strong>Model</strong>
            </td>
            <td width="17%"><strong>Status</strong>
            </td>
        </tr>

        <?php do { 
        
            if($row_rsCorpora['status'] != 2)
            {
                $cpid = urlencode(en($row_rsCorpora['corpusid']));
                $cid = urlencode(en($row_rsCorpora['customizationid']));
                $status = "<a href='edit-corpus.php?cpid={$cpid}&cid={$cid}'>View Status</a>";
            }
            else
            {
                $status = "Analyzed";
            }
        ?>
        <tr>
            <td>
                <a href="edit-corpus.php?cpid=<?php echo urlencode(en($row_rsCorpora['corpusid'])); ?>&cid=<?php echo urlencode(en($row_rsCorpora['customizationid'])); ?>">
                    <?php echo $row_rsCorpora['filename']; ?>.txt</a>
            </td>
            <td>
                <?php echo substr($row_rsCorpora['content'],0,30)."..."?>
            </td>
            <?php /*?>
            <td>
                <?php echo blankNull( str_replace(" - Narrowband","",$row_rsCorpora['modelname2'])); ?>
            </td>
            <?php */?>
            <td>
                <a href="view-model.php?mid=<?php echo urlencode(en($row_rsCorpora['modelid'])); ?>&cid=<?php echo urlencode(en($row_rsCorpora['customizationid'])); ?>">
                    <?php echo $row_rsCorpora['modelname']; ?>
                </a>
            </td>
            <td>Analyzed</td>
        </tr>

        <?php } while ($row_rsCorpora = mysql_fetch_assoc($rsCorpora)); ?>
    </tbody>
</table>

<p>&nbsp;
    <?php if ($pageNum_rsCorpora > 0) { // Show if not first page ?>
    <a href="<?php printf(" %s?pageNum_rsCorpora=%d%s ", $currentPage, 0, $queryString_rsCorpora); ?>">First</a>
    <?php } // Show if not first page ?>
    <?php if ($pageNum_rsCorpora > 0) { // Show if not first page ?>
    <a href="<?php printf(" %s?pageNum_rsCorpora=%d%s ", $currentPage, max(0, $pageNum_rsCorpora - 1), $queryString_rsCorpora); ?>">Previous</a>
    <?php } // Show if not first page ?>
    <?php if ($pageNum_rsCorpora < $totalPages_rsCorpora) { // Show if not last page ?>
    <a href="<?php printf(" %s?pageNum_rsCorpora=%d%s ", $currentPage, min($totalPages_rsCorpora, $pageNum_rsCorpora + 1), $queryString_rsCorpora); ?>">Next</a>
    <?php } // Show if not last page ?>
    <?php if ($pageNum_rsCorpora < $totalPages_rsCorpora) { // Show if not last page ?>
    <a href="<?php printf(" %s?pageNum_rsCorpora=%d%s ", $currentPage, $totalPages_rsCorpora, $queryString_rsCorpora); ?>">Last</a>
    <?php } // Show if not last page ?>
</p>

<?php } else { echo "You currently have no saved corpora."; }?>