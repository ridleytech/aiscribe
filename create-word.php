<?php

require_once 'phpword/vendor/autoload.php';
include( "en-de.php" );

if ( isset( $_POST[ 'content' ] ) ) {
    $did = de( $_POST[ 'did' ] );

    $phpWord = new\ PhpOffice\ PhpWord\ PhpWord();
    
    

    $filename = explode( ".", $_POST[ 'filename' ] );

    $filename1 = "{$filename[0]}{$did}.docx";

    $section = $phpWord->addSection();
    $myTextElement1 = $section->addText( $filename1 . "." . $filename[1] .  " Transcription" ); //test this
    $fontStyle1 = new\ PhpOffice\ PhpWord\ Style\ Font();
    $fontStyle1->setBold( true );
    $fontStyle1->setName( 'Arial' );
    $fontStyle1->setSize( 16 );

    $myTextElement1->setFontStyle( $fontStyle1 );

    $myTextElement2 = $section->addText( "" );

    $fontStyle = new\ PhpOffice\ PhpWord\ Style\ Font();
    $fontStyle->setBold( false );
    $fontStyle->setName( 'Arial' );
    $fontStyle->setSize( 16 );


    $myTextElement = $section->addText( $_POST[ 'content' ] );
    $myTextElement->setFontStyle( $fontStyle );

    $objWriter = \PhpOffice\ PhpWord\ IOFactory::createWriter( $phpWord, 'Word2007' );
    $objWriter->save( "uploads/" . $filename1 );


    $myObj = new stdClass;
    $myObj->output = "{$filename1} created successfully";
    $myObj->filename = $filename1;

    $myJSON = json_encode( $myObj );

    echo $myJSON;
}

//https://github.com/PHPOffice/PHPWord

?>