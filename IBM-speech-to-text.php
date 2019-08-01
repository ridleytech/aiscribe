<?php

require_once('Connections/transcribe.php'); 
include("functions.php");
include("en-de.php");


mysql_select_db( $database_transcribe, $transcribe );
$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];

if(isset($_POST['filename']))
{
   $filename = $_POST['filename'];
}

if(isset($_POST['did']))
{
   $did = $_POST['did'];
}

if(isset($_POST['uid']))
{
   $uid = $_POST['uid'];
}

if(isset($filename) && isset($did) && isset($apiKey))
{
    $ext = trim(strtolower(strrchr($filename, '.')),".");;
    //$did = $_POST['did'];
    //$uid = $_POST['uid'];
    $starttime = date("Y-m-d H:i:s");
    
    mysql_select_db( $database_transcribe, $transcribe );
    $query_rsDocInfo = sprintf( "SELECT * FROM documents WHERE documentid = %s", GetSQLValueString( $did, "int" ) );
    $rsDocInfo = mysql_query( $query_rsDocInfo, $transcribe )or die( mysql_error() );
    $row_rsDocInfo = mysql_fetch_assoc( $rsDocInfo );
    $totalRows_rsDocInfo = mysql_num_rows( $rsDocInfo );
    
    //echo "{$query_rsDocInfo}<br>";
    
    if(($row_rsDocInfo['status']) == 0)
    {
        //echo "starting transcription";
        
        $estimatedCost = $row_rsDocInfo['estimatedCost'];
        $model = "?model={$row_rsDocInfo['languagemodel']}";
                
        if(isset($row_rsDocInfo['customizationid']))
        {
            $customizationid = "&language_customization_id={$row_rsDocInfo['customizationid']}";
        }
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
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
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

           echo "<p>cURL Error1 #:" . $err . "</p>";

        } else {

            //echo "token response: $response";

            $decodedData = json_decode($response);

            //var_dump($decodedData);

            $token = $decodedData->access_token;

            //echo "<p>token: {$token}</p>";

            $convert = true;

            if($convert == true)
            {
                //$filename =  "wire.mp3";

                //$data = file_get_contents('sin.mp3');

                $path = "uploads/{$filename}";

                //echo "path: {$path}";

                $data = file_get_contents($path);

                $curl2 = curl_init();
                                
                $sttURL = "https://stream.watsonplatform.net/speech-to-text/api/v1/recognize{$model}{$customizationid}";
                
                curl_setopt_array($curl2, array(
                  CURLOPT_URL => $sttURL,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_BINARYTRANSFER => true,
                  CURLOPT_TIMEOUT => 50000,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $data,
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    "Content-Type: audio/{$ext}",
                    "Postman-Token: 9bf378d7-8f8d-40d5-98d9-15a6092d10de",
                    "cache-control: no-cache"
                  ),
                ));

                $response2 = curl_exec($curl2);
                $err2 = curl_error($curl2);

                curl_close($curl2);

                if ($err2) {

                  //echo "<p>cURL Error2 #:" . $err2 . "</p>";

                } else {

                    //echo "<p>convert response: {$response2}</p>";

                    $json = json_decode($response2);

                    $results = $json->results;

                    if($results)
                    {
                        //echo "conversion successful";

                        $endtime = date("Y-m-d H:i:s");

                        $timeAdded = blankNull(humanTiming(strtotime($starttime))) . " ago";

                        //echo ". before parse";                        
                        
                        include("parseTranscription.php");
                        
                        $link = urlencode(en($did));
                        
                        $message = "Transcription for {$row_rsDocInfo['filename']} completed successfully. You can view it <a href='https://myaiscribe.com/transcription-result.php?did={$link}'>here</a>.";
                        
                        mysql_select_db( $database_transcribe, $transcribe );
                        $query_rsUserInfo = sprintf( "SELECT * FROM users WHERE userid = %s", GetSQLValueString( $uid, "int" ) );
                        $rsUserInfo = mysql_query( $query_rsUserInfo, $transcribe )or die( mysql_error() );
                        $row_rsUserInfo = mysql_fetch_assoc( $rsUserInfo );
                        
                        $to = $row_rsUserInfo['email'];
                        $subject = "AIScribe Transcription Completed";
                        $html = $message;
                        $text = $message;
                        $from = "noreply@myaiscribe.com";

                        include("send-email.php");
                    }
                    else
                    {
                        //echo "error: {$err2} url: {$sttURL} json: {$response2}";
                        
                        if(isset($_POST['mobile']))
{
                           $status = $err2;

                                    $myObj = new stdClass;
                                    $myObj->status = $status;

                            echo "{\"data\":";
                            echo "{\"transcribeData\":";
                            echo json_encode( $myObj );
                            echo "}";
                            echo "}";
                        }
                        else
                        {
                            echo "\n{$err2}";
                        }
                    }
                }
            }
        }
    }
    else
    {
        $status .= "already transcribed or unsuccessful";
        
        if(isset($_POST['mobile']))
        {
            $status .= "Transcription unsuccessful";
            
            $myObj = new stdClass;
            $myObj->status = $status;
            
            echo "{\"data\":";
            echo "{\"transcribeData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";
        }
        else 
        {
            echo $status;
        }
    }
}

?>      