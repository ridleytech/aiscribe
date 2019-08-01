<?php

require_once('Connections/transcribe.php');
include("functions.php");

$filename = $_FILES['file']['name'];
//$_POST['filename'] = $filename;
$uid = $_POST['uid'];
$language = $_POST['language'];
$estimatedCost = $_POST['estimatedCost'];
$extension = ltrim(strtolower(strrchr($filename, '.')),".");


if(isset($_POST['cid']))
{
    $cid = $_POST['cid'];
}

//query balance

mysql_select_db( $database_transcribe, $transcribe );
$query_rsModelOptions = sprintf( "SELECT credits FROM users WHERE userid = %s", GetSQLValueString( $uid , "int" ) );
$rsModelOptions = mysql_query( $query_rsModelOptions, $transcribe )or die( mysql_error() );
$row_rsModelOptions = mysql_fetch_assoc( $rsModelOptions );
$totalRows_rsModelOptions = mysql_num_rows( $rsModelOptions );


$currentBalance = $row_rsModelOptions['credits'];

//if($estimatedCost < $currentBalance)
//{
//    $enough = "yes";
//}
//else
//{
//    $enough = "no";
//}
//
//
//$response = [ "balance" => $currentBalance, "estimatedCost"=>$estimatedCost, "status"=>$enough];
//
//echo "{\"data\":";
//echo "{\"transcribeData\":";
//echo json_encode( $response );
//echo "}";
//echo "}";
//
//return;

if($estimatedCost < $currentBalance)
{
    //echo "ext: {$extension}\n";

    $mime_types = array(
       'mp3' => 'audio/mp3',
       'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
       'flac' => 'audio/flac');

    if (array_key_exists($extension, $mime_types)) {

        $filetype = $mime_types[$extension];

        //echo "filetype: {$filetype}\n";

        if ( 0 < $_FILES['file']['error'] ) {

            echo 'Error: ' . $_FILES['file']['error'] . '<br>';
        }
        else 
        {
            move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $filename);

            //echo "file uploaded successfully. ";

            //insert into document here

             $insertSQL = sprintf("INSERT INTO documents (filename, filetype, languagemodel, userid, estimatedCost, customizationid, datecreated) VALUES (%s, %s, %s, %s, %s, %s, %s)",
                GetSQLValueString(mysql_real_escape_string($filename), "text"),
                GetSQLValueString(mysql_real_escape_string($filetype), "text"),
                GetSQLValueString(mysql_real_escape_string($language), "text"),
                GetSQLValueString(mysql_real_escape_string($uid), "int"),
                GetSQLValueString(mysql_real_escape_string($estimatedCost), "text"),
                GetSQLValueString(mysql_real_escape_string($cid), "text"),                  
                GetSQLValueString(mysql_real_escape_string($date), "date"));

            mysql_select_db($database_transcribe, $transcribe);
            $Result1 = mysql_query($insertSQL, $transcribe) or die(mysql_error());	

            //echo "insertSQL: {$insertSQL}\n";

            $did = mysql_insert_id();
            
            //echo json_encode("done");
//            $_POST['did'] = blankNull(strval($did));
//
//            //echo "document saved";
//
            
            $status = "document saved";
            
            
            $myObj = new stdClass;
            $myObj->status = $status;
            $myObj->did = blankNull(strval($did));
            
//            $_POST['did'] = $did;
            
//            $status = "go2";
//            
//            
            echo "{\"data\":";
            echo "{\"transcribeData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";

            //$myJSON = json_encode($myObj);

           // echo $myJSON;
        }
    }
}
else
{
//    $myObj = new stdClass;
//    $myObj->status = "Not enough credits";

    //$myJSON = json_encode($myObj);

    //echo $myJSON;
    
    $status = "You do not have enough credits for this transcription.\nPlease add more credits to your account.";
    
    if(isset($_POST['mobile']))
    {
        $response = [ "status" => $status];

        echo "{\"data\":";
        echo "{\"transcribeData\":";
        echo json_encode( $response );
        echo "}";
        echo "}";
    }
    else
    {
        echo $status;
    }
}


?>  