<?php

mysql_select_db( $database_transcribe, $transcribe );
$query_rsModelInfo = sprintf( "SELECT * FROM custommodels WHERE userid = %s AND active = 1", GetSQLValueString( $colname_rsModelInfo, "int" ) );
$rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
$row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );
$totalRows_rsModelInfo = mysql_num_rows( $rsModelInfo );

?>