
<?php

require_once('Connections/transcribe.php'); 
include("functions.php");
include("en-de.php");

 if(!isset($_POST['mobile']))
{
    $_POST[ 'uid' ] = $_SESSION[ 'uid' ];
}

if(isset($_POST['mobile']))
{
    $customizationid = $_POST['cid'];
}
else
{
    $customizationid = de($_POST['cid']) ;
}

//ycbnu931pRfkbJuVEM9Xvw%3D%3D

//https://cloud.ibm.com/apidocs/speech-to-text#add-a-corpus
//https://cloud.ibm.com/docs/services/speech-to-text?topic=speech-to-text-manageGrammars#listGrammars

//$_POST['mobile'] = true;

//$_POST['customizationid'] = "ff8b45ef-cd5d-4299-8483-c84761526ebd";
//$_POST['customizationid'] = "665a8754-bd34-49d5-967f-196202aab43a";

$date = date("Y-m-d H:i:s");

mysql_select_db( $database_transcribe, $transcribe );


$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];

if(isset($customizationid) && isset($apiKey))
{
    //$customizationid = de($_POST['cid']);
    //$customizationid = $_POST['customizationid'];
    
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
        $status = $err;
        
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
            echo $status;
        }

    } else {

        //echo "token response: $response";

        $decodedData = json_decode($response);

        //var_dump($decodedData);

        $token = $decodedData->access_token;

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if($convert == true)
        {
            $curl2 = curl_init();

            curl_setopt_array($curl2, array(
              CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}",
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
            ));

            $response2 = curl_exec($curl2);
            $err2 = curl_error($curl2);

            curl_close($curl2);

            if ($err2) {
              echo "cURL Error #:" . $err2;
            } else {
              
                //echo $response;
                                
                $decodedData = json_decode($response2);

                $status = $decodedData->status;
                $language = $decodedData->language;
                
                if ($status == "pending")
                {                    
                    $update = true;
                    $statusInd = 0;
                }
                else if ($status == "ready")
                {                    
                    $update = true;
                    $statusInd = 1;
                }
                else if ($status == "failed")
                {                    
                    $update = true;
                    $statusInd = 2;
                }
                else if ($status == "available")
                {
                    $update = true;
                    $statusInd = 3;
                }
                else if ($status == "training")
                {
                    $update = true;
                    $statusInd = 4;
                }
                else if ($status == "upgrading")
                {
                    $update = true;
                    $statusInd = 5;
                }
                
                $updateSQL = sprintf("UPDATE custommodels SET status = %s WHERE customizationid = %s",
					GetSQLValueString(mysql_real_escape_string($statusInd), "text"),
					GetSQLValueString(mysql_real_escape_string($customizationid), "text"));
	
                    mysql_select_db($database_transcribe, $transcribe);
                    $Result1 = mysql_query($updateSQL, $transcribe) or die(mysql_error());
                
                
                $query_rsModelInfo = sprintf( "SELECT dateupdated FROM custommodels WHERE customizationid = %s", GetSQLValueString( $customizationid, "text" ) );
                        $rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
                        $row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );                        
                        $dateupdated = $row_rsModelInfo['dateupdated'];
                
                //$timeSince = humanTiming($dateupdated);                
                
                $query_rsModelInfo = sprintf( "SELECT TIME_TO_SEC(TIMEDIFF(%s, %s)) diff", GetSQLValueString( $date, "date" ), GetSQLValueString( $dateupdated, "date" ) );
                        $rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
                        $row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );                        
                        $diff = $row_rsModelInfo['diff'];
                
                $timeSince = $diff;
                
                //$status = "pending";
                
                //$response = ["status" => $status,"language" => $language,"timeSince" => $timeSince,"date" => $date,"dateupdated" => $dateupdated];
                $response = ["status" => $status, "language" => $language, "timeSince" => $timeSince];

                if(isset($_POST['mobile']))
                {
                                        
                    $myObj = new stdClass;
                    $myObj->status = $status;                    
                    $myObj->timeSince = $timeSince;
                    $myObj->language = $language;
                    
                    
                   $query_rsCorpusInfo = sprintf( "SELECT * FROM corpus WHERE customizationid = %s", GetSQLValueString($customizationid, "text" ) );
                    $rsCorpusInfo = mysql_query( $query_rsCorpusInfo, $transcribe )or die( mysql_error() );
                    $row_rsCorpusInfo = mysql_fetch_assoc( $rsCorpusInfo );
                    $totalRows_rsCorpusInfo = mysql_num_rows( $rsCorpusInfo );
                    
                    $myObj->query = json_encode($query_rsCorpusInfo);
                    
                    if($totalRows_rsCorpusInfo){$corpusfile = $row_rsCorpusInfo['filename']. ".txt";} else {$corpusfile = "None";}

                    $myObj->corpusfile = $corpusfile;
                                    
                    
                    $query_rsFilesInfo = sprintf( "SELECT * FROM documents WHERE customizationid = %s", GetSQLValueString( $customizationid, "text" ) );
$rsFilesInfo = mysql_query( $query_rsFilesInfo, $transcribe )or die( mysql_error() );
$row_rsFilesInfo = mysql_fetch_assoc( $rsFilesInfo );
$totalRows_rsFilesInfo = mysql_num_rows( $rsFilesInfo );
                    
                    $myObj->transcriptions = strval($totalRows_rsFilesInfo);

                    echo "{\"data\":";
                    echo "{\"modelData\":";
                    echo json_encode( $myObj );
                    echo "}";
                    echo "}";
                }
                else
                {
                    echo json_encode($response);
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
        echo "{\"modelData\":";
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