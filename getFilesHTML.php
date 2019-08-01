<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

//test write

$colname_rsFiles = "-1";
if ( isset( $_GET[ 'uid' ] ) ) {
    $colname_rsFiles = $_GET[ 'uid' ];
}

if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsFiles = $_SESSION[ 'uid' ];
}

$currentPage = $_SERVER[ "PHP_SELF" ];

$maxRows_rsFiles = 20;
$pageNum_rsFiles = 0;
if ( isset( $_GET[ 'pageNum_rsFiles' ] ) ) {
    $pageNum_rsFiles = $_GET[ 'pageNum_rsFiles' ];
}
$startRow_rsFiles = $pageNum_rsFiles * $maxRows_rsFiles;

mysql_select_db( $database_transcribe, $transcribe );
$query_rsFiles = sprintf( "SELECT * FROM documents WHERE userid = {$colname_rsFiles} AND active = 1 ORDER by datecreated DESC" );

$query_limit_rsFiles = sprintf( "%s LIMIT %d, %d", $query_rsFiles, $startRow_rsFiles, $maxRows_rsFiles );

//echo "query: " . $query_limit_rsFiles;

$rsFiles = mysql_query( $query_limit_rsFiles, $transcribe )or die( mysql_error() );
$row_rsFiles = mysql_fetch_assoc( $rsFiles );

if ( isset( $_GET[ 'totalRows_rsFiles' ] ) ) {
    $totalRows_rsFiles = $_GET[ 'totalRows_rsFiles' ];
} else {
    $all_rsFiles = mysql_query( $query_rsFiles );
    $totalRows_rsFiles = mysql_num_rows( $all_rsFiles );
}
$totalPages_rsFiles = ceil( $totalRows_rsFiles / $maxRows_rsFiles ) - 1;

$queryString_rsFiles = "";
if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
    $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
    $newParams = array();
    foreach ( $params as $param ) {
        if ( stristr( $param, "pageNum_rsFiles" ) == false &&
            stristr( $param, "totalRows_rsFiles" ) == false ) {
            array_push( $newParams, $param );
        }
    }
    if ( count( $newParams ) != 0 ) {
        $queryString_rsFiles = "&" . htmlentities( implode( "&", $newParams ) );
    }
}
$queryString_rsFiles = sprintf( "&totalRows_rsFiles=%d%s", $totalRows_rsFiles, $queryString_rsFiles );


if ($totalRows_rsFiles > 0) { ?>


<table width="100%" cellpadding="5" cellspacing="5">
    <tbody>
        <tr>
            <td width="31%"><strong>Filename</strong>
            </td>
            <td width="5%" align="center"><strong>Status</strong>
            </td>
            <td width="5%" align="center"><strong>Transcription</strong>
            </td>
            <td width="5%" align="center"><strong>Translations</strong>
            </td>
            <td width="59%">&nbsp;</td>
        </tr>

        <?php do { ?>
        <tr>
            <td class="hr"><?php echo $row_rsFiles['filename']; ?></td>
            <td align="center"><?php if($row_rsFiles['status'] == 1) { ?>Completed<?php } else { ?> Pending <?php } ?></td>
            <td align="center"><?php if($row_rsFiles['status'] == 1) { ?><a href="transcription-result.php?did=<?php echo urlencode(en($row_rsFiles['documentid'])); ?>">view</a><?php } ?></td>
            <td align="center"><?php if($row_rsFiles['hasTranslation'] == 1 && $row_rsFiles['status'] == 1) { ?><a href="document-translations.php?did=<?php echo urlencode(en($row_rsFiles['documentid'])); ?>">view</a>
            <?php } else if($row_rsFiles['status'] == 1){ ?><a href="translate-file.php?did=<?php echo urlencode(en($row_rsFiles['documentid'])); ?>">create</a><?php } ?>
            </td>
            <td>&nbsp;</td>
        </tr>

        <?php } while ($row_rsFiles = mysql_fetch_assoc($rsFiles));  ?>
    </tbody>
</table>


<p>&nbsp;
    <?php if ($pageNum_rsFiles > 0) { // Show if not first page ?>
    <a href="<?php printf(" %s?pageNum_rsFiles=%d%s ", $currentPage, 0, $queryString_rsFiles); ?>">First</a>
    <?php } // Show if not first page ?>
    <?php if ($pageNum_rsFiles > 0) { // Show if not first page ?>
    <a href="<?php printf(" %s?pageNum_rsFiles=%d%s ", $currentPage, max(0, $pageNum_rsFiles - 1), $queryString_rsFiles); ?>">Previous</a>
    <?php } // Show if not first page ?>
    <?php if ($pageNum_rsFiles < $totalPages_rsFiles) { // Show if not last page ?>
    <a href="<?php printf(" %s?pageNum_rsFiles=%d%s ", $currentPage, min($totalPages_rsFiles, $pageNum_rsFiles + 1), $queryString_rsFiles); ?>">Next</a>
    <?php } // Show if not last page ?>
    <?php if ($pageNum_rsFiles < $totalPages_rsFiles) { // Show if not last page ?>
    <a href="<?php printf(" %s?pageNum_rsFiles=%d%s ", $currentPage, $totalPages_rsFiles, $queryString_rsFiles); ?>">Last</a>
    <?php } // Show if not last page ?>
</p>

<?php } else { echo "You currently don't have any transcribed or translated files."; }?>