
<?php

require_once('Connections/transcribe.php'); 
include("functions.php");
include("en-de.php");

 if(!isset($_POST['mobile']))
{
    $_POST[ 'uid' ] = $_SESSION[ 'uid' ];
}



//https://cloud.ibm.com/apidocs/speech-to-text#add-a-corpus
//https://cloud.ibm.com/docs/services/speech-to-text?topic=speech-to-text-manageGrammars#listGrammars

//$_POST['mobile'] = true;

//$_POST['customizationid'] = "ff8b45ef-cd5d-4299-8483-c84761526ebd";
//$_POST['filename'] = "rom";
//$testfile = "uploads/rom transcription.txt";


if(isset($_POST['cid']) && isset($_POST['mobile']))
{
    $customizationid = $_POST['cid'];
}
else if(isset($_POST['cid']))
{
    $customizationid = de($_POST['cid']);
}
else
{
    $customizationid = $_POST['customizationid'];
}

if(isset($_POST['content']))
{
    $content = $_POST['content'];
}

$date = date("Y-m-d H:i:s");

mysql_select_db( $database_transcribe, $transcribe );

$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo['apikey'];

if(isset($_POST['filename']) && isset($customizationid) && isset($apiKey))
{
    
    $a = explode(".",$_POST['filename']);
    $filename = $a[0];
    
    //$_POST['filename'] = urlencode("rom");
    
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
        
        if(isset($testfile))
        {
            $path = $testfile;
        }
        else if(!isset($content))
        {
            $path = $_FILES['file']['tmp_name'];
            $data = file_get_contents($path);
        }
        
        if(isset($content))
        {
            $data = $content;
        }

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if($convert == true)
        {
            $curl2 = curl_init();
            
            //echo "url: {$url}";
            
            $formatted = urlencode($filename);
            
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
              $status = $err2;
        
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
                    //insert file info into DB
                    
                    if(isset($content))
                    {
                        $updateSQL = sprintf( "UPDATE corpus set content=%s WHERE customizationid = %s",
                        GetSQLValueString( $content, "text" ),
                        GetSQLValueString( $customizationid, "text" ));

                        mysql_select_db( $database_transcribe, $transcribe );
                        $Result1 = mysql_query( $updateSQL, $transcribe )or die( mysql_error() );
                        
                        $status = "corpus updated successfully";
                        
                        if(isset($_POST['mobile']))
                        {
                            $corpusid = $_POST['cpid'];  
                        }
                        else
                        {
                            $corpusid = de($_POST['cpid']);
                        }
                        
                        //reset custommodel status
                        
                        $updateSQL2 = sprintf( "UPDATE custommodels set status=%s,dateupdated=%s WHERE customizationid = %s",
                        GetSQLValueString( 0, "text" ),
                        GetSQLValueString( $date, "date" ),
                        GetSQLValueString( $customizationid, "text" ));

                        mysql_select_db( $database_transcribe, $transcribe );
                        $Result1 = mysql_query( $updateSQL2, $transcribe )or die( mysql_error() );
                        
                        
                        $query_rsModelInfo = sprintf( "SELECT modelid FROM custommodels WHERE customizationid = %s", GetSQLValueString( $customizationid, "text" ) );
                        $rsModelInfo = mysql_query( $query_rsModelInfo, $transcribe )or die( mysql_error() );
                        $row_rsModelInfo = mysql_fetch_assoc( $rsModelInfo );                        
                        $modelid = $row_rsModelInfo['modelid'];
                    }
                    else
                    {
                        $insertSQL = sprintf( "INSERT INTO corpus (filename, customizationid, content, userid, status, datecreated) VALUES (%s, %s, %s, %s, %s, %s)",
                        GetSQLValueString( $filename, "text" ),
                        GetSQLValueString( $customizationid, "text" ),
                        GetSQLValueString( $data, "text" ),
                        GetSQLValueString( $_POST[ 'uid' ], "int" ),
                        GetSQLValueString( 0, "int" ),
                        GetSQLValueString( $date, "date" ) );

                        mysql_select_db( $database_transcribe, $transcribe );
                        $Result1 = mysql_query( $insertSQL, $transcribe )or die( mysql_error() );

                        $status = "corpus created successfully";
                        $corpusid = mysql_insert_id();
                    }                    
                    
                    if(isset($_POST['mobile']))
                    {
                        $myObj = new stdClass;
                        //$myObj->mid = $modelid;
                        $myObj->status = $status;
                        $myObj->cpid = blankNull(strval($corpusid));

                        echo "{\"data\":";
                        echo "{\"corpusData\":";
                        echo json_encode( $myObj );
                        echo "}";
                        echo "}";
                    }
                    else
                    {
                        //echo $status . " file2: " . $_FILES['file']['tmp_name'];
                        //echo $status;
                        
                        $myObj = new stdClass;

                        $myObj->status = $status;
                        $myObj->cid = urlencode(en($customizationid));
                        $myObj->mid = urlencode(en($modelid));

                        $myJSON = json_encode( $myObj );

                        echo $myJSON;
                    }
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