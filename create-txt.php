<?php

include( "en-de.php" );

if ( isset( $_POST[ 'content' ] ) ) {
    $did = de( $_POST[ 'did' ] );

    $a = explode( ".", $_POST[ 'filename' ] );

    $filename = "{$a[0]}{$did}.txt";

    //$filename = "uploads/{$_POST['filename']}{$_POST['did']}.txt";
    $header = $_POST[ 'filename' ] . " Transcription\n\n";

    $myfile = fopen( "uploads/" . $filename, "w" )or die( "Unable to open file!" );
    fwrite( $myfile, $header . $_POST[ 'content' ] );
    fclose( $myfile );


    $myObj = new stdClass;
    $myObj->output = "{$filename} created successfully";
    $myObj->filename = $filename;

    $myJSON = json_encode( $myObj );

    echo $myJSON;
}

?>