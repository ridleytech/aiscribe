<?php

require_once( 'Connections/transcribe.php' );
include( "functions.php" );
include( "en-de.php" );

if ( !isset( $_POST[ 'mobile' ] ) ) {
    $_POST[ 'uid' ] = $_SESSION[ 'uid' ];
}

if ( isset( $_POST[ 'mobile' ] ) ) {
    $customizationid = $_POST[ 'cid' ];
} else {
    $customizationid = de( $_POST[ 'cid' ] );
}

//ycbnu931pRfkbJuVEM9Xvw%3D%3D

//https://cloud.ibm.com/apidocs/speech-to-text#add-a-corpus
//https://cloud.ibm.com/docs/services/speech-to-text?topic=speech-to-text-manageGrammars#listGrammars

//$_POST['mobile'] = true;

//$_POST['customizationid'] = "ff8b45ef-cd5d-4299-8483-c84761526ebd";
//$_POST['customizationid'] = "665a8754-bd34-49d5-967f-196202aab43a";

$date = date( "Y-m-d H:i:s" );

mysql_select_db( $database_transcribe, $transcribe );


$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo[ 'apikey' ];

if ( isset( $customizationid ) && isset( $apiKey ) ) {
    //$customizationid = de($_POST['cid']);
    //$customizationid = $_POST['customizationid'];

    $curl = curl_init();

    curl_setopt_array( $curl, array(
        CURLOPT_URL => "https://iam.bluemix.net/identity/token",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "grant_type=urn%3Aibm%3Aparams%3Aoauth%3Agrant-type%3Aapikey&apikey={$apiKey}",
        CURLOPT_HTTPHEADER => array(
            "Accept: application/json",
            "Content-Type: application/x-www-form-urlencoded",
            "Postman-Token: 1d378144-7f93-4d72-8b2d-3d775883d3f3",
            "cache-control: no-cache"
        ),
    ) );

    $response = curl_exec( $curl );
    $err = curl_error( $curl );

    curl_close( $curl );

    if ( $err ) {

        //$status = "cURL Error1 #:" . $err;
        $status = $err;

        if ( isset( $_POST[ 'mobile' ] ) ) {
            $myObj = new stdClass;
            $myObj->status = $status;

            echo "{\"data\":";
            echo "{\"corpusData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";
        } else {
            echo $status;
        }

    } else {

        //echo "token response: $response";

        $decodedData = json_decode( $response );

        //var_dump($decodedData);

        $token = $decodedData->access_token;

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if ( $convert == true ) {
            $curl2 = curl_init();

            curl_setopt_array( $curl2, array(
                CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/corpora",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    "cache-control: no-cache"
                ),
            ) );

            $response2 = curl_exec( $curl2 );
            $err2 = curl_error( $curl2 );

            curl_close( $curl2 );

            if ( $err2 ) {
                echo "cURL Error #:" . $err2;
            } else {

                //echo $response;

                $decodedData = json_decode( $response2 );

                $oov = $decodedData->corpora[0]->out_of_vocabulary_words;
                $total_words = $decodedData->corpora[0]->total_words;
                $status = $decodedData->corpora[ 0 ]->status;

                if ( $status == "being_processed" ) {
                    $update = true;
                    $statusInd = 0;
                } else if ( $status == "undetermined" ) {
                    $update = true;
                    $statusInd = 1;
                } else {
                    $update = true;
                    $statusInd = 2;
                }

                $updateSQL = sprintf( "UPDATE corpus SET status = %s WHERE customizationid = %s",
                    GetSQLValueString( mysql_real_escape_string( $statusInd ), "text" ),
                    GetSQLValueString( mysql_real_escape_string( $customizationid ), "text" ) );

                mysql_select_db( $database_transcribe, $transcribe );
                $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );

                $response = [ "status" => $status ];

                if ( isset( $_POST[ 'mobile' ] ) ) {

                    $myObj = new stdClass;
                    $myObj->status = $status;
                    $myObj->timeSince = $timeSince;

                    echo "{\"data\":";
                    echo "{\"corpusData\":";
                    echo json_encode( $myObj );
                    echo "}";
                    echo "}";
                } else {
                    echo json_encode( $response );
                }
            }
        }
    }
} else {
    $status = "missing params";

    if ( isset( $_POST[ 'mobile' ] ) ) {
        $myObj = new stdClass;
        $myObj->status = $status;

        echo "{\"data\":";
        echo "{\"corpusData\":";
        echo json_encode( $myObj );
        echo "}";
        echo "}";
    } else {
        echo $status;
    }
}

?>