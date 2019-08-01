
<?php

require_once('Connections/transcribe.php'); 
include("functions.php");

 if(!isset($_POST['mobile']))
{
    $_POST[ 'uid' ] = $_SESSION[ 'uid' ];
}

//https://cloud.ibm.com/apidocs/speech-to-text#add-a-corpus
//https://cloud.ibm.com/docs/services/speech-to-text?topic=speech-to-text-manageGrammars#listGrammars

//$apiKey = "x4TKd2zCz0S6gQ9Zmc_kWFP5iFTwDBOWJCc3872RY7Vb"; //ridleytech@gmail.com
//$apiKey = "ohpms51lPSC1tPSFB2bhX-l0-CSL3zDVg8yS7_gscqjK"; //ridleytech@gmail.com

//$_POST['mobile'] = true;

//$_POST['customizationid'] = "ff8b45ef-cd5d-4299-8483-c84761526ebd";
//$_POST['filename'] = "rom";
//$testfile = "uploads/rom transcription.txt";

$date = date("Y-m-d H:i:s");

mysql_select_db( $database_transcribe, $transcribe );
$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];

if(isset($_POST['filename']) && isset($apiKey))
{
    $a = explode(".",$_POST['filename']);
    $filename = $a[0];
    $formatted = urlencode($filename);
    
    //query customizationid    
    
    $query_rsIdInfo = sprintf( "SELECT customizationid FROM corpus WHERE corpusid = %s AND userid = %s", GetSQLValueString( $_POST['corpusid'], "text" ),  GetSQLValueString( $_POST['uid'], "int" ));
    $rsIdInfo = mysql_query( $query_rsIdInfo, $transcribe )or die( mysql_error() );
    $row_rsIdInfo = mysql_fetch_assoc( $rsIdInfo );
    
    $customizationid = $row_rsIdInfo['customizationid'];
    
    
    
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

        //$status = "cURL Error1 #:" . $err;
        $status = "cURL token Error #:" . $err;
        
        if(isset($_POST['mobile']))
        {
            $myObj = new stdClass;
            $myObj->status = $status;

            echo "{\"data\":";
            echo "{\"corpusData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";
        }
        else
        {
            echo $status;
        }

    } else {

        //echo "token response: $response";

        $decodedData = json_decode($response);

        //var_dump($decodedData);

        $token = $decodedData->access_token;
        
        
        //delete old corpus
                
        
        $curl1 = curl_init();

        curl_setopt_array($curl1, array(
          CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/corpora/{$formatted}",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "DELETE",
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer {$token}",
            "cache-control: no-cache"
          ),
        ));

        $response1 = curl_exec($curl1);
        $err1 = curl_error($curl1);

        curl_close($curl1);

        if ($err1) {
            
          $status = "cURL delete corpus Error #:" . $err1;
            
            if(isset($_POST['mobile']))
            {
                $myObj = new stdClass;
                $myObj->status = $status;

                echo "{\"data\":";
                echo "{\"corpusData\":";
                echo json_encode( $myObj );
                echo "}";
                echo "}";
            }
            else
            {
                echo $status; // . " file1: " . $_FILES['file']['tmp_name'];
            }
                        
        } else {
            
            //corpora delete successful
            
          //echo $response1;
            
            $data = $_POST['content'];

        //echo "<p>token: {$token}</p>";

            $convert = true;

            if($convert == true)
            {
                $curl2 = curl_init();

                //$url = "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/corpora/{$filename}&allow_overwrite=true";

                //echo "url: {$url}";

                $url = "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/corpora/{$formatted}?allow_overwrite=true";

                curl_setopt_array($curl2, array(
                  CURLOPT_URL => $url,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $data,
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    "cache-control: no-cache"                  
                  ),
                ));

                $response2 = curl_exec($curl2);
                $err2 = curl_error($curl2);

                curl_close($curl2);

                if ($err2) {

                  //$status = "cURL Error2 #:" . $err2;
                  $status = "cURL create corpus Error #:" . $err2;

                    if(isset($_POST['mobile']))
                    {
                        $myObj = new stdClass;
                        $myObj->status = $status;

                        echo "{\"data\":";
                        echo "{\"corpusData\":";
                        echo json_encode( $myObj );
                        echo "}";
                        echo "}";
                    }
                    else
                    {
                        echo $status; // . " file1: " . $_FILES['file']['tmp_name'];
                    }

                } else {

                    //echo "<p>convert response: {$response2}</p>";

                    //var_dump($response2);

                    $json = json_decode($response2);

                    //["code"]=> int(400)

                    $code = $json->code;

                    if(!isset($code))
                    {
                        $curl3 = curl_init();

                        curl_setopt_array($curl3, array(
                          CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/train",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "POST",
                          CURLOPT_HTTPHEADER => array(
                            "Accept: */*",
                            "Authorization: Bearer {$token}",
                            "Cache-Control: no-cache",
                            "Connection: keep-alive",
                            "Host: stream.watsonplatform.net",
                            "Postman-Token: 31a46112-5376-4dc0-8668-34b302fc0e0f,9dfc2df8-0074-4eaa-b075-c8945b85b400",
                            "User-Agent: PostmanRuntime/7.15.0",
                            "accept-encoding: gzip, deflate",
                            "cache-control: no-cache"
                          ),
                        ));

                        $response3 = curl_exec($curl);
                        $err3 = curl_error($curl3);

                        curl_close($curl);

                        if ($err3) {
                          $status =  "cURL train Error #:" . $err3;
                        } else {
                          //echo $response3;

                            $insertSQL = sprintf( "UPDATE corpus SET content = %S WHERE corpusid = %s AND userid = %s",
                            GetSQLValueString( $data, "text" ),
                            GetSQLValueString( $_POST[ 'corpusid' ], "int" ),
                            GetSQLValueString( $_POST[ 'uid' ], "int" ));

                            mysql_select_db( $database_transcribe, $transcribe );
                            $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

                            $status = "corpus updated successfully";
                            //$corpusid = mysql_insert_id();


                            $trainStatus = "model trained successfully";

                            if(isset($_POST['mobile']))
                            {
                                $myObj = new stdClass;
                                $myObj->status = $status;
                                $myObj->trainStatus = $trainStatus;
                                $myObj->corpusid = blankNull(strval($corpusid));

                                echo "{\"data\":";
                                echo "{\"corpusData\":";
                                echo json_encode( $myObj );
                                echo "}";
                                echo "}";
                            }
                            else
                            {
                                //echo $status . " file2: " . $_FILES['file']['tmp_name'];
                                echo $status;
                            }
                        }


//                        if(isset($_POST['mobile']))
//                        {
//                            $myObj = new stdClass;
//                            $myObj->status = $status;
//                            $myObj->corpusid = blankNull(strval($corpusid));
//
//                            echo "{\"data\":";
//                            echo "{\"corpusData\":";
//                            echo json_encode( $myObj );
//                            echo "}";
//                            echo "}";
//                        }
//                        else
//                        {
//                            //echo $status . " file2: " . $_FILES['file']['tmp_name'];
//                            echo $status;
//                        }
                    }
                    else
                    {
                        $status = $json->error. " code: " . $code;

                        if(isset($_POST['mobile']))
                        {
                            $myObj = new stdClass;
                            $myObj->status = $status;
                            $myObj->code = $json->code;

                            echo "{\"data\":";
                            echo "{\"corpusData\":";
                            echo json_encode( $myObj );
                            echo "}";
                            echo "}";
                        }
                        else
                        {
                            echo $status . " file3: {$filename} path: " . $_FILES['file']['tmp_name'];
                        }
                    }
                }
            }
        }
        
        
        
        
    }
}
else
{
    $status = "missing params";
    
    if(isset($_POST['mobile']))
    {
        $myObj = new stdClass;
        $myObj->status = $status;

        echo "{\"data\":";
        echo "{\"corpusData\":";
        echo json_encode( $myObj );
        echo "}";
        echo "}";
    }
    else
    {
        echo $status;
    }
}

?>      