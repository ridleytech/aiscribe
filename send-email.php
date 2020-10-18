<?php 

	$url = 'https://api.sendgrid.com/';
	//$user = '';
    $user = '';
	$pass = '';

	$params = array(
	  'api_user' => $user,
	  'api_key' => $pass,
	  'to' => $to,
	  'subject' => $subject,
	  'html' => $html,
	  'text' => $text,
	  'from' => $from,
	);

	$request = $url.'api/mail.send.json';

	// Generate curl request
	$session = curl_init($request);

	// Tell curl to use HTTP POST
	curl_setopt ($session, CURLOPT_POST, true);

	// Tell curl that this is the body of the POST
	curl_setopt ($session, CURLOPT_POSTFIELDS, $params);

	// Tell curl not to return headers, but do return the response
	curl_setopt($session, CURLOPT_HEADER, false);
	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

	// obtain response
	$response = curl_exec($session);
	curl_close($session);


?>
