<?php

//debug vars

$date = date( "Y-m-d H:i:s" );

//echo ". step 0<br>";

$json = json_decode( $response2 );

//var_dump($json);

//"translations": [
//        {
//            "translation": "¡ Hola, mundo! "
//        },
//        {
//            "translation": "¿Cómo estás?"
//        }
//    ],
//    "word_count": 5,
//    "character_count": 26

$results = $json->translations;

if ( isset( $results ) ) {

    $resultString = "";

    //echo ". step 1<br>";

    foreach ( $results as $result ) {

        //$confidence = $result->alternatives[0]->confidence;

        //$confidenceValues[] = $confidence;

        $translation = $result->translation;

        $resultString .= $translation;
    }

    //echo ". step 2<br>";

    $wordcount = $json->word_count;
    $charactercount = $json->character_count;

    //echo "<h3>Transcription Confidence: {$percent}</h3>";

    //echo $resultString;

    //$output = "Transcription Confidence: {$percent}\n\n{$resultString}";

    //echo $resultString;


    if ( !isset( $did ) ) {
                
        mysql_select_db( $database_transcribe, $transcribe );
        $query_rsFileInfo = sprintf( "SELECT * FROM documents WHERE filename = %s AND userid = %s AND output=%s", GetSQLValueString( $fileName , "text" ), GetSQLValueString( $uid , "int" ), GetSQLValueString( $input , "text" ) );
        $rsFileInfo = mysql_query( $query_rsFileInfo, $transcribe )or die( mysql_error() );
        $row_rsFileInfo = mysql_fetch_assoc( $rsFileInfo );
        $totalRows_rsFileInfo = mysql_num_rows( $rsFileInfo );
        
        if($totalRows_rsFileInfo == 0)
        {
                $insertSQL = sprintf( "INSERT INTO documents (output, filetype,filename, userid, estimatedCost, status, datecreated) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString( mysql_real_escape_string( $input ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $fileType ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $fileName ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $uid ), "text" ),
                GetSQLValueString( mysql_real_escape_string( $estimatedCost ), "text" ),
                GetSQLValueString( mysql_real_escape_string( 1 ), "int" ),
                GetSQLValueString( mysql_real_escape_string( $date ), "date" ) );

            mysql_select_db( $database_transcribe, $transcribe );
            $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );
            $did = mysql_insert_id();
        }
        else
        {
            $did = $row_rsFileInfo['documentid'];
        } 
    }
    
    $resultString = str_replace("\'","'",$resultString);
    
    //echo "resultString: {$resultString}";

    $insertSQL = sprintf( "INSERT INTO translations (displayLang, language, userid, documentid, input, output, translation, wordcount, charactercount, processtime, timeprocessed,datecreated) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString( mysql_real_escape_string( $displayLanguage ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $language ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $uid ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $did ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $input ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $response2 ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $resultString ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $wordcount ), "int" ),
        GetSQLValueString( mysql_real_escape_string( $charactercount ), "int" ),
        GetSQLValueString( mysql_real_escape_string( $timeAdded ), "text" ),
        GetSQLValueString( mysql_real_escape_string( $endtime ), "date" ),
        GetSQLValueString( mysql_real_escape_string( $date ), "date" ) );

    mysql_select_db( $database_transcribe, $transcribe );
    $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

    $translationid = mysql_insert_id();
    
    if(isset($_POST['mobile']))
    {
        $response = [ "translation" => $resultString, "translationid" => strval($translationid), "did" => strval($did)];
                
        //delete file
        
        try {
    
            unlink( $path );
        }
        catch (Exception $e) {

            //echo "could not delete file. ";

            //echo $e->getMessage(); // will print Exception message defined above.
        }
        
        echo "{\"data\":";
        echo "{\"translateData\":";
        echo json_encode( $response );
        echo "}";
        echo "}";
    }
    else
    {
        $response = [ "translation" => $resultString, "translationid" => en($translationid), "did" => en($did)];

        echo $json = json_encode( $response );
    }    

    if ( isset( $did ) ) {

        $insertSQL = sprintf( "UPDATE documents SET hasTranslation = %s WHERE documentid = %s",
            GetSQLValueString( 1, "int" ),
            GetSQLValueString( $did, "int" ) );

        mysql_select_db( $database_transcribe, $transcribe );
        $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );
    }
    
} else {

    $response = [ "translation" => "translation error", "translationid" => "none", "error" =>  $json];

    //echo $json = json_encode( $response );
    
    if(isset($_POST['mobile']))
    {
        echo "{\"data\":";
        echo "{\"translateData\":";
        echo json_encode( $response );
        echo "}";
        echo "}";
    }
    else
    {
        echo $json = json_encode( $response );
    }
}

?>