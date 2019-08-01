<?php

	//https://blog.serverdensity.com/how-to-build-an-apple-push-notification-provider-server-tutorial/
	//https://www.raywenderlich.com/123862/push-notifications-tutorial

	//setup payload

    $_POST['devStatus'] = "prod";

    require_once( 'Connections/transcribe.php' );
    include( "functions.php" );


    $message = "hi Randall";
    $deviceToken = "281538e8a2454fbfbadce459c6e50ad650b8607c096c44817a01cba908bad352"; // iPad
    //$deviceToken = "0b906cfdb63701a5edf762a92bed3cce818a0c9c6c302eec9b5cafc628a27606"; // iPhone5
    $_POST['isBrowser'] = true;

    $url = "http://myaiscribe.com";

    //$deviceToken = $row_rsCuisineRecipients['deviceid'];
    $_POST['token'] = $deviceToken;

    $body['aps'] = array(
              'alert' => $message,
              'sound' => 'default',
              'link_url' => $url,
              'category' => "NEW_TRANSCRIBE_SIGNAL",
              'body' => "body test",
              'badge' => 1,
              );

	if(isset($message) && strlen($message) > 0)
	{
        mysql_select_db( $database_transcribe, $transcribe );
        $query_rsKeyInfo = sprintf( "SELECT apikey FROM apikeys WHERE service = %s AND active = 1", GetSQLValueString( "push", "text" ) );
        $rsKeyInfo = mysql_query( $query_rsKeyInfo, $transcribe )or die( mysql_error() );
        $row_rsKeyInfo = mysql_fetch_assoc( $rsKeyInfo );

        $apiKey = $row_rsKeyInfo[ 'apikey' ];

		$passphrase = $apiKey;
        
		$url = "http://dto.iwatch.com";

		if (!$message || !$url)
			exit('Example Usage: $php newspush.php \'Test notification\' \'https://dto.com\'' . "\n");

		////////////////////////////////////////////////////////////////////////////////
        
        if( $_POST['devStatus'] == "dev")
        {
            $pem = "aiScribe-aps-dev.pem";
            $pushurl = "ssl://gateway.sandbox.push.apple.com:2195";
        }
        else
        {
            $pem = "aiScribe-aps-prod-final.pem";
            $pushurl = "ssl://gateway.push.apple.com:2195";
        }
        
		$ctx = stream_context_create();
		stream_context_set_option($ctx, 'ssl', 'local_cert', $pem);
		stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

		// Open a connection to the APNS server

		$fp = stream_socket_client(
		 $pushurl, $err,
		  $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

		if (!$fp)
		  exit("Failed to connect: $err code: $errstr" . PHP_EOL);

		//echo '<br>Connected to APNS' . PHP_EOL;

		// Create the payload body
		
		//echo date('Y-m-d', strtotime("+5 days"));

		// Encode the payload as JSON

		$payload = json_encode($body);

		// Build the binary notification

		$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

		// Send it to the server

		$result = fwrite($fp, $msg, strlen($msg));

		if (!$result)
        {
            //echo '<br>Message not delivered' . PHP_EOL;

        }
		else
        {
            //echo '<br>Message successfully delivered' . PHP_EOL;
  
            date_default_timezone_set('America/Detroit');

            $date = date("m-d-Y g:i A");
            
            if(isset($_POST['isBrowser']))
            {
                echo "<p>Push notification sent at $date to device: $deviceToken</p>";
            }
            else
            {
                //echo "<p>Push notification sent at $date to device: $deviceToken</p>";
            }            
        }

		// Close the connection to the server

		fclose($fp);
	}
	
?>
