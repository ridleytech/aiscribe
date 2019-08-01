
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
$_POST['customizationid'] = "ff8b45ef-cd5d-4299-8483-c84761526ebd";
$_POST['filename'] = "rom&allow_overwrite=true";

$date = date("Y-m-d H:i:s");

mysql_select_db( $database_transcribe, $transcribe );
$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];



if(isset($_POST['filename']) && isset($_POST['customizationid']) && isset($apiKey))
{
    $customizationid = $_POST['customizationid'];
    $filename = $_POST['filename'];
    
    
    
//$query_rsCorpusInfo = sprintf( "SELECT corpusid FROM corpus WHERE filename = %s AND customizationid = %s AND userid = %s", GetSQLValueString( $filename, "text" ), GetSQLValueString( $customizationid, "text" ), GetSQLValueString( $_POST[ 'uid' ], "int" ) );
//                    $rsCorpusInfo = mysql_query( $query_rsCorpusInfo, $transcribe )or die( mysql_error() );
//                    $row_rsCorpusInfo = mysql_fetch_assoc( $rsCorpusInfo );
//                    
//                    $corpusid = $row_rsCorpusInfo['corpusid'];
//
//
//echo "query: " . $query_rsCorpusInfo  . "<br>id: " . $corpusid;
//
//return;
    
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
            echo "<p>{$status}</p>";
        }

    } else {

        //echo "token response: $response";

        $decodedData = json_decode($response);

        //var_dump($decodedData);

        $token = $decodedData->access_token;
        
        //$file = $_FILES['file']['tmp_name'];
        
        //$data = file_get_contents($file);
        
       
                
        //$data = file_get_contents("uploads/{$filename}");

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if($convert == true)
        {
            $curl2 = curl_init();
            
            //$url = "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/corpora/{$filename}&allow_overwrite=true";
            
            //echo "url: {$url}";
            
            $url = "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/corpora/{$filename}";

            curl_setopt_array($curl2, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "DELETE",
              CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer {$token}",
                "cache-control: no-cache"),
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
                    echo "<p>{$status}</p>";
                }

            } else {

                //echo "<p>convert response: {$response2}</p>";
                
                //var_dump($response2);
                
                $json = json_decode($response2);
                
                //["code"]=> int(400)

                $code = $json->code;
                
                if(!isset($code))
                {                    
                    //get corpus ID
                        
                    $query_rsCorpusInfo = sprintf( "SELECT corpusid FROM corpus WHERE filename = %s AND customizationid = %s AND userid = %s", GetSQLValueString( $filename, "text" ), GetSQLValueString( $customizationid, "text" ), GetSQLValueString( $_POST[ 'uid' ], "int" ) );
                    $rsCorpusInfo = mysql_query( $query_rsCorpusInfo, $transcribe )or die( mysql_error() );
                    $row_rsCorpusInfo = mysql_fetch_assoc( $rsCorpusInfo );
                    
                    $corpusid = $row_rsCorpusInfo['corpusid'];

                    //delete corpus
                    
                    if($corpusid)
                    {
                        $query_rsDeleteInfo = sprintf( "delete FROM corpus WHERE corpusid = %s", GetSQLValueString( $corpusid, "int" ));
                        $rsDeleteInfo = mysql_query( $query_rsDeleteInfo, $transcribe )or die( mysql_error() );                    

                        $status = "corpus deleted successfully";     
                    }
                    else
                    {
                        $status = "corpus id not available";
                    }
                    
                    
                    if(isset($_POST['mobile']))
                    {
                        $myObj = new stdClass;
                        $myObj->status = $status;
                        $myObj->corpusid = blankNull(strval($corpusid));

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
                else
                {
                    $status = $json->code_description . ": " . $json->error;
                    
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
                        echo "<p>$status</p>";
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