<?php
require_once( 'Connections/transcribe.php' );
include( "functions.php" );
include( "en-de.php" );

$uid = $_POST[ 'uid' ];
$language = $_POST[ 'language' ];
$displayLanguage = $_POST[ 'displayLanguage' ];
$estimatedCost = $_POST[ 'estimatedCost' ];

mysql_select_db( $database_transcribe, $transcribe );
$query_rsModelOptions = sprintf( "SELECT credits FROM users WHERE userid = %s", GetSQLValueString( $uid , "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );

$currentBalance = $row_rsModelOptions['credits'];


if($estimatedCost < $currentBalance)
{
    if (isset($_POST[ 'mobile' ])) {

        //echo "mobile<br>";

        //from mobile app

        $fileName = $_POST[ 'fileName' ];
        $fileType = $_POST[ 'fileType' ];

        $path = 'uploads/' . $fileName;
        $content = file_get_contents( $path );
        //$input = json_encode( $content );
        $input = $content;
    }
    else if (isset($_POST[ 'file' ])) {

        //get file contents

        $fileName = $_POST[ 'fileName' ];
        $fileType = $_POST[ 'fileType' ];

        $path = $_POST['file'];
        $input = file_get_contents($path);

        //echo "condition 2";
    }
    else if (isset($_FILES)) {

        //from jquery form

        //get file contents

        $fileName = $_POST[ 'fileName' ];
        $fileType = $_POST[ 'fileType' ];

        $path = $_FILES['file']['tmp_name'];
        $input = file_get_contents($path);

        //echo "condition 3";
    }

    else {

        //$did = $_POST[ 'did' ];
        $input = $_POST[ 'input' ];
    }

    $input = str_replace(array("\r", "\n"), '', $input);

    mysql_select_db( $database_transcribe, $transcribe );
    $query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "translate", "text" ) );
    $rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
    $row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

    $apiKey = $row_rsKeyInfo[ 'apikey' ];

    //echo "key: {$apiKey}<br>";

    if ( isset( $uid ) && isset( $input ) && isset($apiKey)) {

        //echo "get token<br>";

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

            //echo "<p>cURL Error1 #:" . $err . "</p>";

            $response = [ "error" => $err, "type"=>"token"];

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

        } else {

            //echo "<br>response: {$response}<br><br>";

            $decodedData = json_decode( $response );

            //var_dump($decodedData);

            $token = $decodedData->access_token;

            //echo "<p>token: {$token}</p>";

            $convert = true;

            if ( $convert == true ) {

                $curl2 = curl_init();

                //echo "language: {$language} input: {$input}<br>";

                //$data = "{\"text\": [\"{$input}\"], \"model_id\":\"en-de\"}";

                $data = "{\"text\": [\"{$input}\"], \"model_id\":\"{$language}\"}";

                //echo "<p>{$data}</p>";

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

                    //echo "<p>cURL Error2 #:" . $err2 . "</p>";

                    $response = [ "error" => $err2, "type"=>"translation"];

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
    }
}
else
{
    $status = "You do not have enough credits for this translation.\nPlease add more credits to your account.";
    
    $response = [ "status" => $status];
    
    echo "{\"data\":";
    echo "{\"translateData\":";
    echo json_encode( $response );
    echo "}";
    echo "}";
}

//$0.02 USD/MINUTE

?>