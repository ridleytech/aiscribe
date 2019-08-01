<?php 

//$_POST['mobile'] = true;

if(!isset($_POST[ "MM_insert" ]))
{
    require_once('Connections/transcribe.php'); 
    include("functions.php");
}

if(!isset($_POST[ "mobile" ]))
{
    $_POST[ 'uid' ] = $_SESSION[ 'uid' ];
}


//$_POST['filename'] = "hlm.wav";

//$apiKey = "x4TKd2zCz0S6gQ9Zmc_kWFP5iFTwDBOWJCc3872RY7Vb"; //ridleytech@gmail.com
//$apiKey = "ohpms51lPSC1tPSFB2bhX-l0-CSL3zDVg8yS7_gscqjK"; //ridleytech@gmail.com


//$_POST['modelname'] = "t1";
//$_POST['modelLanguage'] = "en-US";
//$_POST['modeldescription'] = "test again";

//{"name": "Randall example language model", "base_model_name": "en-US_BroadbandModel", "description": "First example custom language model"}

mysql_select_db( $database_transcribe, $transcribe );
$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];


if(isset($_POST['modelname']))
{
    $modelname =  $_POST['modelname'];
    //$ext = trim(strtolower(strrchr($filename, '.')),".");;
    //$did = $_POST['did'];
    $modellanguage =  $_POST['modelLanguage'];
    $modeldescription = $_POST['modeldescription'];
    $starttime = date("Y-m-d H:i:s");

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

        $status = "cURL Error1 #:" . $err;
        
        if(isset($_POST['mobile']))
        {
            $myObj = new stdClass;
            $myObj->status = $status;

            echo "{\"data\":";
            echo "{\"modelData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";
        }
        else
        {
            //echo "<p>{$status}</p>";
        }

    } else {

        //echo "token response: {$response}<br>";

        $decodedData = json_decode($response);

        //var_dump($decodedData);

        $token = $decodedData->access_token;

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if($convert == true)
        {
            $curl2 = curl_init();
            
            $data = "{\"name\": \"{$modelname}\", \"base_model_name\": \"{$modellanguage}\", \"description\": \"{$modeldescription}\"}";
            //$data = "{\"name\": \"{$modelname}\", \"base_model_name\": \"{$modellanguage}_NarrowbandModel\", \"description\": \"{$modeldescription}\"}";
            //$data2 = "{\"name\": \"t1\", \"base_model_name\": \"en-US_NarrowbandModel\", \"description\": \"t1d test\"}";
            
            //echo "data: {$data}<br>";
            //echo "data: {$data2}<br>";

            curl_setopt_array($curl2, array(
              CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              //CURLOPT_POSTFIELDS => "{\"name\": \"t1\", \"base_model_name\": \"en-US_NarrowbandModel\", \"description\": \"t1d\"}",
              CURLOPT_POSTFIELDS => $data,
              CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Authorization: Bearer {$token}",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Content-Type: application/json",
                "Host: stream.watsonplatform.net",
                "Postman-Token: f64d38b3-033f-4030-9db8-fb6111a7f5f4,f9aa703e-8a06-4979-a4c0-f634f8e9bbce",
                "User-Agent: PostmanRuntime/7.15.0",
                "accept-encoding: gzip, deflate",
                "cache-control: no-cache"
              ),
            ));

            $response2 = curl_exec($curl2);
            $err2 = curl_error($curl2);

            curl_close($curl2);

            if ($err2) {

              $status = "cURL Error2 #:" . $err2;
        
                if(isset($_POST['mobile']))
                {
                    $myObj = new stdClass;
                    $myObj->status = $status;

                    echo "{\"data\":";
                    echo "{\"modelData\":";
                    echo json_encode( $myObj );
                    echo "}";
                    echo "}";
                }
                else
                {
                    //echo "<p>{$status}</p>";
                }

            } else {

                //echo "<p>convert response: {$response2}</p>";
                
                $json = json_decode($response2);
                
                //var_dump($json);
                
                if($json->customization_id)
                {         
                    $customizationid = $json->customization_id;
                    
                    $insertSQL = sprintf( "INSERT INTO custommodels (modelname, code, customizationid, modeldescription, userid, active, status, datecreated) VALUES (%s, %s, %s, %s, %s, %s, %s, %s)",
                        GetSQLValueString( $modelname, "text" ),
                        GetSQLValueString( $modellanguage, "text" ),
                        GetSQLValueString( $customizationid, "text" ),
                        GetSQLValueString( $modeldescription, "text" ),
                        GetSQLValueString( $_POST[ 'uid' ], "int" ),
                        GetSQLValueString( 1, "int" ),
                        GetSQLValueString( 0, "int" ),
                        GetSQLValueString( $date, "date" ) );

                    mysql_select_db( $database_transcribe, $transcribe );
                    $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );
                    
                    $status = "model created successfully";
                    $modelid = mysql_insert_id();
                    
                    if(isset($_POST['mobile']))
                    {
                        $myObj = new stdClass;
                        $myObj->status = $status;
                        $myObj->modelid = blankNull(strval($modelid));
                        $myObj->customizationid = blankNull($customizationid);

                        echo "{\"data\":";
                        echo "{\"modelData\":";
                        echo json_encode( $myObj );
                        echo "}";
                        echo "}";
                    }
                    else
                    {
                        //echo "custom model saved successfully. conversion successful"; 
                        
                        //echo "id: {$customizationid}";
                        
                        $insertGoTo = "my-models.php";
//                        if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
//                            $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
//                            $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
//                        }
                        header( sprintf( "Location: %s", $insertGoTo ) );
                    }
                }
                else
                {
                    $status = "error. no results " . $response2;
                    
                    if(isset($_POST['mobile']))
                    {
                        $myObj = new stdClass;
                        $myObj->status = $status;

                        echo "{\"data\":";
                        echo "{\"modelData\":";
                        echo json_encode( $myObj );
                        echo "}";
                        echo "}";
                    }
                    else
                    {
                        //echo "<p>{$status}</p>";
                    }
                }
            }
        }
    }
}

//$0.02 USD/MINUTE


//https://cloud.ibm.com/apidocs/speech-to-text#create-a-custom-language-model


//audio formats

//https://cloud.ibm.com/docs/services/speech-to-text?topic=speech-to-text-audio-formats#audio-formats

//https://cloud.ibm.com/apidocs/speech-to-text

?>      