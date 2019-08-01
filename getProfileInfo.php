<?php require_once('Connections/transcribe.php'); 

include("functions.php");
include("en-de.php");

//$_POST['uid'] = "1";
//$_POST['receiverid'] = "3";

if(isset($_POST['uid']))
{
    mysql_select_db($database_transcribe, $transcribe);
    $query_rsProfileInfo = "SELECT * FROM users WHERE userid = '".$_POST['uid']."'";

    $rsProfileInfo = mysql_query($query_rsProfileInfo, $transcribe) or die(mysql_error());
    $row_rsProfileInfo = mysql_fetch_assoc($rsProfileInfo);
    $totalRows_rsProfileInfo = mysql_num_rows($rsProfileInfo);

    if($totalRows_rsProfileInfo)
    {
        do {

            $object = new stdClass();

            $object->firstname = blankNull($row_rsProfileInfo['firstname']);
            $object->lastname = blankNull($row_rsProfileInfo['lastname']);
            $object->email = blankNull($row_rsProfileInfo['email']);
            $object->dob = blankNull($row_rsProfileInfo['dob']);
            $object->password = blankNull($row_rsProfileInfo['password']);
    //		$object->phone = $row_rsProfileInfo['phone'];
    //		$object->address = $row_rsProfileInfo['address'];
    //		$object->city = $row_rsProfileInfo['city'];
    //		$object->state = $row_rsProfileInfo['state'];
    //		$object->zip = $row_rsProfileInfo['zip'];
            $object->username = blankNull($row_rsProfileInfo['username']);
            $object->credits = blankNull(number_format($row_rsProfileInfo['credits'],2));

        }  while ($row_rsProfileInfo = mysql_fetch_assoc($rsProfileInfo));
    }
}
	
echo "{\"data\":";
echo "{\"profileData\":";
echo json_encode( $object );
echo "}";
echo "}";
?>
