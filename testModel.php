<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://stream.watsonplatform.net/speech-to-text/api/v1/customizations",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\"name\": \"t1\", \"base_model_name\": \"en-US_NarrowbandModel\", \"description\": \"t1d\"}",
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Authorization: Bearer eyJraWQiOiIyMDE5MDIwNCIsImFsZyI6IlJTMjU2In0.eyJpYW1faWQiOiJpYW0tU2VydmljZUlkLTg3OTVmNzY1LTkwYWItNDFkZC05ZTZkLTUxNWJlNDYwYjIyYiIsImlkIjoiaWFtLVNlcnZpY2VJZC04Nzk1Zjc2NS05MGFiLTQxZGQtOWU2ZC01MTViZTQ2MGIyMmIiLCJyZWFsbWlkIjoiaWFtIiwiaWRlbnRpZmllciI6IlNlcnZpY2VJZC04Nzk1Zjc2NS05MGFiLTQxZGQtOWU2ZC01MTViZTQ2MGIyMmIiLCJzdWIiOiJTZXJ2aWNlSWQtODc5NWY3NjUtOTBhYi00MWRkLTllNmQtNTE1YmU0NjBiMjJiIiwic3ViX3R5cGUiOiJTZXJ2aWNlSWQiLCJ1bmlxdWVfaW5zdGFuY2VfY3JucyI6WyJjcm46djE6Ymx1ZW1peDpwdWJsaWM6c3BlZWNoLXRvLXRleHQ6dXMtc291dGg6YS9iODYxNzIzMDA3NjAwNjc0MDk5YWUzN2JjYTBjNjJkYjo5ODA2OGY0Yi04ZmE3LTRlMWEtOTMzMy01MDU1MzU0YWQzMzQ6OiJdLCJhY2NvdW50Ijp7InZhbGlkIjp0cnVlLCJic3MiOiJiODYxNzIzMDA3NjAwNjc0MDk5YWUzN2JjYTBjNjJkYiJ9LCJpYXQiOjE1NjE1ODQ1MTUsImV4cCI6MTU2MTU4ODExNSwiaXNzIjoiaHR0cHM6Ly9pYW0uYmx1ZW1peC5uZXQvaWRlbnRpdHkiLCJncmFudF90eXBlIjoidXJuOmlibTpwYXJhbXM6b2F1dGg6Z3JhbnQtdHlwZTphcGlrZXkiLCJzY29wZSI6ImlibSBvcGVuaWQiLCJjbGllbnRfaWQiOiJkZWZhdWx0IiwiYWNyIjoxLCJhbXIiOlsicHdkIl19.o8Z4RKxT8YSFhuomMbrrY3eVdIxhD7fd0MWn-nId_OXg4E1RTDYQ80yh0qpDgjzr0CtpZH-G56Daju0uXSKavTlPJFCMD8TQH5_lJdJIGiCOCsXpOVGJIq-uFEiFs41O0v8YDly0ogy2xRo-rkr2WlgTqo9h6s6UXRDlclRBxtE0GGh6rgtNvxX8MPd1tkIzdBnihygM5hqCOp-OgY1fbf2yeBiBPj8rXViqKV2rUw7AlnIm1GZvdjwy6L2dcwADxfEthkyAgRY3WlYtQ6uNgLr0To6ZsffWwAUrw9t3FPqF0TAp2uDp6kHFpo3UiSi2QRQ8lrSskV6UIxH6ied9bQ",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Content-Type: application/json",
    "Host: stream.watsonplatform.net",
    "Postman-Token: f64d38b3-033f-4030-9db8-fb6111a7f5f4,f9aa703e-8a06-4979-a4c0-f634f8e9bbce",
    "User-Agent: PostmanRuntime/7.15.0",
    "accept-encoding: gzip, deflate",
    "cache-control: no-cache",
    "content-length: 80"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

?>