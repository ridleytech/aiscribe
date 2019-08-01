<?php

$devStatus = "dev";

require_once( 'Connections/transcribe.php' );

include( "functions.php" );
include( "en-de.php" );

//$_POST[ 'code' ] = "en-US_NarrowbandModel";
//$_POST[ 'uid' ] = "1";

$colname_rsModelOptions = "-1";
if ( isset( $_GET[ 'uid' ] ) ) {
    $colname_rsModelOptions = $_GET[ 'uid' ];
}

if ( isset( $_SESSION[ 'uid' ] ) ) {
    $colname_rsModelOptions = $_SESSION[ 'uid' ];
}

if ( isset( $_POST[ 'code' ] ) && isset( $_POST[ 'uid' ] ) ) {

    $currentPage = $_SERVER[ "PHP_SELF" ];

    $maxRows_rsModelOptions = 20;
    $pageNum_rsModelOptions = 0;
    if ( isset( $_GET[ 'pageNum_rsModelOptions' ] ) ) {
        $pageNum_rsModelOptions = $_GET[ 'pageNum_rsModelOptions' ];
    }
    $startRow_rsModelOptions = $pageNum_rsModelOptions * $maxRows_rsModelOptions;

    mysql_select_db( $database_transcribe, $transcribe );
    $query_rsModelOptions = sprintf( "SELECT * FROM custommodels WHERE userid = {$colname_rsModelOptions} AND code = '{$_POST['code']}' AND active = 1" );

    $query_limit_rsModelOptions = sprintf( "%s LIMIT %d, %d", $query_rsModelOptions, $startRow_rsModelOptions, $maxRows_rsModelOptions );
    $rsModelOptions = mysql_query( $query_limit_rsModelOptions, $transcribe )or die( mysql_error() );
    $row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );

    if ( isset( $_GET[ 'totalRows_rsModelOptions' ] ) ) {
        $totalRows_rsModelOptions = $_GET[ 'totalRows_rsModelOptions' ];
    } else {
        $all_rsModelOptions = mysql_query( $query_rsModelOptions );
        $totalRows_rsModelOptions = mysql_num_rows( $all_rsModelOptions );
    }
    $totalPages_rsModelOptions = ceil( $totalRows_rsModelOptions / $maxRows_rsModelOptions ) - 1;

    $queryString_rsModelOptions = "";
    if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) ) {
        $params = explode( "&", $_SERVER[ 'QUERY_STRING' ] );
        $newParams = array();
        foreach ( $params as $param ) {
            if ( stristr( $param, "pageNum_rsModelOptions" ) == false &&
                stristr( $param, "totalRows_rsModelOptions" ) == false ) {
                array_push( $newParams, $param );
            }
        }
        if ( count( $newParams ) != 0 ) {
            $queryString_rsModelOptions = "&" . htmlentities( implode( "&", $newParams ) );
        }
    }
    $queryString_rsModelOptions = sprintf( "&totalRows_rsModelOptions=%d%s", $totalRows_rsModelOptions, $queryString_rsModelOptions );

    ?>

    <?php if($totalRows_rsModelOptions  > 0) { ?>

    <select name="customModel" id="customModel">
        <option>Custom models...</option>
        <?php do { 
        
        
        if(isset($_POST['cid']))
        {
            if(de($_SESSION['tempid']) == $row_rsModelOptions['customizationid'])
            {
                $selected = " selected";
            }
            else
            {
                $selected = "";
            }
        }
        
        ?>

        <option value="<?php echo $row_rsModelOptions['customizationid'];?>" <?php echo $selected;?>><?php echo str_replace("- Narrowband","",$row_rsModelOptions['modelname']);?>
</option>

        <?php } while ($row_rsModelOptions = mysql_fetch_assoc($rsModelOptions)); ?>
    </select>


    <?php } else { 
        
        //echo "{$_POST['code']} currently has no custom models."; 
        echo "";     
    }
}
else
{
    echo "missing params";
}

?>