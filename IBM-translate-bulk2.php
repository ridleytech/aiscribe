<?php
require_once( 'Connections/transcribe.php' );



include( "en-de.php" );
include( "functions.php" );


$uid = $_POST[ 'uid' ];
$language = $_POST[ 'language' ];
$displayLanguage = $_POST[ 'displayLanguage' ];

if(isset($_POST[ 'did' ]) && isset($_POST[ 'mobile' ]))
{
    $did = $_POST[ 'did' ];
}
else
{
    $did = de($_POST[ 'did' ]);
}

//echo "uid: {$uid} language: {$language} displayLanguage: {$displayLanguage} did: {$did}";

mysql_select_db( $database_transcribe, $transcribe );
$query_rsDocInfo = sprintf( "SELECT output FROM documents WHERE documentid = %s", GetSQLValueString( $did, "int" ) );
$rsDocInfo = mysql_query( $query_rsDocInfo, $transcribe )or die( mysql_error() );
$row_rsDocInfo = mysql_fetch_assoc( $rsDocInfo );

$input = json_encode( $row_rsDocInfo[ 'output' ] );

//echo "input: {$input}";

mysql_select_db( $database_transcribe, $transcribe );
$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "translate", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo[ 'apikey' ];

if ( isset( $did ) && isset( $input ) && isset( $apiKey ) ) {

    $starttime = date( "Y-m-d H:i:s" );
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

        echo "<p>cURL Error1 #:" . $err . "</p>";

    } else {

        //echo "<br>response: {$response}<br>";

        $decodedData = json_decode( $response );

        //var_dump($decodedData);

        $token = $decodedData->access_token;

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if ( $convert == true ) {

            $curl2 = curl_init();
            
            $language = trim($language);

            $data = "{\"text\": [{$input}], \"model_id\":\"{$language}\"}";

            //echo "<p>data: {$data}</p>";

            curl_setopt_array( $curl2, array(
                CURLOPT_URL => "https://gateway.watsonplatform.net/language-translator/api/v3/translate?version=2018-05-01",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    "Content-Type: application/json",
                    "Postman-Token: ca931d40-42a1-49ca-b711-70fe511252fd",
                    "cache-control: no-cache"
                ),
            ) );

            $response2 = curl_exec( $curl2 );
            $err2 = curl_error( $curl2 );

            curl_close( $curl2 );

            if ( $err2 ) {

                echo "<p>cURL Error2 #:" . $err2 . "</p>";

            } else {

                //echo "<p>convert response: {$response2}</p>";

                //echo "conversion successful";

                $endtime = date( "Y-m-d H:i:s" );

                $timeAdded = blankNull( humanTiming( strtotime( $starttime ) ) ) . " ago";

                //echo ". before parse";
                //parse result and save in db

                include( "parseTranslation.php" );           
            }
        }
    }
} else {
    echo "params not set";
}

//$0.02 USD/MINUTE

?>