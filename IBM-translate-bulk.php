<?php
require_once( 'Connections/transcribe.php' );

//STT token api x4TKd2zCz0S6gQ9Zmc_kWFP5iFTwDBOWJCc3872RY7Vb ridleytech@gmail.com
//Translation token api psQT-I3WUvcqe469H0QpQyGKqxuAzLS1Lv-VsyBPeTjG ridleytech@gmail.com

include( "functions.php" );

//$_POST['lang'] = "en-es";

//$_POST['uid'] = "1";
//$_POST['did'] = "14";
//$_POST['lang'] = "en-de";
//$_POST['input'] = "Lando was also absent from The Last Jedi (2017).During the early development of the film, director Rian Johnson briefly considered bringing back Lando as the codebreaker that Finn and Rose Tico seek in the coastal city of Canto Bight, but Lando was finally written out of the film's script, with the codebreaker role ultimately going to Benicio del Toro's character DJ.";

$uid = $_POST[ 'uid' ];
$lang = $_POST[ 'lang' ];
$displayLang = $_POST[ 'langDisplay' ];

if (isset($_POST[ 'file' ])) {
    
    //get file contents
    
    $fileName = $_POST[ 'fileName' ];
    $fileType = $_POST[ 'fileType' ];

    $input = file_get_contents($_POST[ 'file' ]);
    
} else {
    
    $did = $_POST[ 'did' ];
    $input = $_POST[ 'input' ];
}

//$apiKey = "psQT-I3WUvcqe469H0QpQyGKqxuAzLS1Lv-VsyBPeTjG"; //ridleytech@gmail.com


mysql_select_db( $database_transcribe, $transcribe );
$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "translate", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];

if ( isset( $uid ) && isset( $input ) && isset($apiKey)) {
    
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
        CURLOPT_POSTFIELDS => "grant_type=urn%3Aibm%3Aparams%3Aoauth%3Agrant-type%3Aapikey&apikey={$apikey}",
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
        
        if(isset($_POST['languages']))
        {
            $translations = explode(",",$_POST['languages']);
        }
        else
        {
            $translations = explode(",",$_SESSION['languages']);
        }
        
        $processcount = 0;
        
        foreach($translations as $trans)
        {
            $convert = true;

            if ( $convert == true ) {

                $curl2 = curl_init();

                $data = "{\"text\": [\"{$input}\"], \"model_id\":\"{$trans}\"}";

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

                    echo "<p>cURL Error2 #:" . $err2 . "</p>";

                } else {

                    //echo "<p>convert response: {$response2}</p>";

                    //echo "conversion successful";

                    $endtime = date( "Y-m-d H:i:s" );

                    $timeAdded = blankNull( humanTiming( strtotime( $starttime ) ) ) . " ago";

                    //echo ". before parse";
                    //parse result and save in db

                    include( "parseTranslation.php" );

                    //send email

                    //echo ". after parse";
                    
                    $processcount++;
                    $count = count($translations);

                    
                    if($processcount == $count)
                    {
                        //$message = "Document translated successfully. Total Time: {$timeAdded}";

                        $message = "Translation for {$count} languages completed successfully";
                        
                        mysql_select_db( $database_transcribe, $transcribe );
                        $query_rsUserInfo = sprintf( "SELECT * FROM users WHERE userid = %s", GetSQLValueString( $uid, "int" ) );
                        $rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
                        $row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );
                        
                        $to = $row_rsUserInfo['email'];
                        $subject = "Bulk document translation completed successfully";
                        $html = $message;
                        $text = $message;
                        $from = "noreply@myaiscribe.com";

                        //include("../send-email.php");    
                    }                               
                }
            }
        }        
    }
}

?>