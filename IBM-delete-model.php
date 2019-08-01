<?php

if ( isset( $_POST[ 'mobile' ] ) ) {

    require_once( 'Connections/transcribe.php' );
    include( "functions.php" );
    include( "en-de.php" );
}

if ( !isset( $_POST[ 'mobile' ] ) ) {
    $_POST[ 'uid' ] = $_SESSION[ 'uid' ];
}

if ( $_GET[ 'cid' ] ) {
    $cid = $_GET[ 'cid' ];
} else if ( $_POST[ 'cid' ] ) {
    $cid = $_POST[ 'cid' ];
}

$date = date( "Y-m-d H:i:s" );

mysql_select_db( $database_transcribe, $transcribe );

$query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "stt", "text" ) );
$rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
$row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

$apiKey = $row_rsKeyInfo[ 'apikey' ];

if ( isset( $cid ) && isset( $apiKey ) ) {

    //echo "apiKey: {$apiKey}<br>";

    $customizationid = de( $cid );

    //$status = "customizationid: {$customizationid}";

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

        //$status = "cURL Error1 #:" . $err;
        $status = $err;

        if ( isset( $_POST[ 'mobile' ] ) ) {
            $myObj = new stdClass;
            $myObj->status = $status;

            echo "{\"data\":";
            echo "{\"deleteData\":";
            echo json_encode( $myObj );
            echo "}";
            echo "}";
        } else {
            echo "token error status: " . $status;
        }

    } else {

        //echo "token response: $response";

        $decodedData = json_decode( $response );

        //var_dump($decodedData);

        $token = $decodedData->access_token;

        //echo "<p>token: {$token}</p>";

        $convert = true;

        if ( $convert == true ) {

            //echo "go<br>";

            $curl2 = curl_init();

            curl_setopt_array( $curl2, array(
                CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations/{$customizationid}/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "DELETE",
                CURLOPT_HTTPHEADER => array(
                    "Authorization: Bearer {$token}",
                    "Postman-Token: edba2e70-8f94-44ba-9213-35099c905b07",
                    "cache-control: no-cache"
                ),
            ) );

            $response3 = curl_exec( $curl2 );
            $err3 = curl_error( $curl2 );

            curl_close( $curl2 );

            if ( $err3 ) {
                $status = "cURL train Error #:" . $err3;

                echo "error status: {$status}";
            } else {

                $deleteSQL = sprintf( "DELETE FROM custommodels WHERE customizationid=%s",
                    GetSQLValueString( $customizationid, "int" ) );

                mysql_select_db( $database_transcribe, $transcribe );
                $Result1 = mysql_query( $deleteSQL, $transcribe )or die( mysql_error() );

                $deleteSQL2 = sprintf( "DELETE FROM corpus WHERE customizationid=%s",
                    GetSQLValueString( $customizationid, "int" ) );

                mysql_select_db( $database_transcribe, $transcribe );
                $Result1 = mysql_query( $deleteSQL2, $transcribe )or die( mysql_error() );

                $status = " Model deleted successfully";

                //echo "status: {$status}";

                if ( isset( $_POST[ 'mobile' ] ) ) {
                    $myObj = new stdClass;
                    $myObj->status = $status;

                    echo "{\"data\":";
                    echo "{\"deleteData\":";
                    echo json_encode( $myObj );
                    echo "}";
                    echo "}";
                } else {
                    //echo $status;

                    $insertGoTo = "my-models.php";
                    if ( isset( $_SERVER[ 'QUERY_STRING' ] ) ) {
                        $insertGoTo .= ( strpos( $insertGoTo, '?' ) ) ? "&" : "?";
                        $insertGoTo .= $_SERVER[ 'QUERY_STRING' ];
                    }
                    header( sprintf( "Location: %s", $insertGoTo ) );
                }
            }
        }
    }
} else {
    $status = "missing params";

    if ( isset( $_POST[ 'mobile' ] ) ) {
        $myObj = new stdClass;
        $myObj->status = $status;

        echo "{\"data\":";
        echo "{\"deleteData\":";
        echo json_encode( $myObj );
        echo "}";
        echo "}";
    } else {
        echo $status;
    }
}

?>