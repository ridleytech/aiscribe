<?php

header('Content-type: application/json');

error_reporting(E_ERROR | E_PARSE);

require_once('../../Connections/chewsrite.php');
include("functions.php");

mysql_select_db($database_chewsrite, $chewsrite);

$request = json_decode(file_get_contents('php://input'), TRUE);

//$response = ["URLOfTheSecondWebsite" => $request['websites'][1]['URL']];

$string;
$date = date("Y-m-d H:i:s");

$status = "data not saved";

//$request['recipename'] = "a";

if($request['recipename'])
{
	$insertSQL = sprintf("INSERT INTO recipes (recipename, userid, description, source, servings, preptime, cooktime, totaltime, calories, imagename, cuisinetags, dateadded) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
            GetSQLValueString(mysql_real_escape_string($request['recipename']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['userid']), "int"),
            GetSQLValueString(mysql_real_escape_string($request['description']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['source']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['servings']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['preptime']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['cooktime']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['totaltime']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['calories']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['imagename']), "text"),
            GetSQLValueString(mysql_real_escape_string($request['cuisinetags']), "text"),
            GetSQLValueString($date, "date"));
			
	mysql_select_db($database_chewsrite, $chewsrite);
	$Result1 = mysql_query($insertSQL, $chewsrite) or die(mysql_error());	

	$last_id = mysql_insert_id();	
	$status = "recipe saved";
    
    
    //send push to all users with dietary concerns in cuisine tags
    
    mysql_select_db($database_pushtrades, $pushtrades);
    $query_rsCuisineRecipients = "SELECT a.*,b.firstname,b.lastname,b.deviceid FROM (SELECT userid,selections FROM usercuisines WHERE selections IN ({$_POST['cuisinetags']})) as a INNER JOIN (SELECT * FROM users WHERE deviceid IS NOT NULL) as b ON a.userid = b.userid";

    $rsCuisineRecipients = mysql_query($query_rsCuisineRecipients, $pushtrades) or die(mysql_error());
    $row_rsCuisineRecipients = mysql_fetch_assoc($rsCuisineRecipients);
    $totalRows_rsCuisineRecipients = mysql_num_rows($rsCuisineRecipients);
    
    do {
        
        $url = "http://chewsrite.com";
        $deviceToken = $row_rsCuisineRecipients['deviceid'];
        $_POST['token'] = $row_rsCuisineRecipients['deviceid'];

        $message = "{$row_rsCuisineRecipients['firstname']}, New recipes have been added!";

        $body['aps'] = array(
                  'alert' => $message,
                  'sound' => 'default',
                  'link_url' => $url,
                  'category' => "NEW_RECIPE_SIGNAL",
                  'body' => (string)$signalid,
                  'badge' => 1,
                  );

        //echo "send to user: ".$deviceToken."<br>";

        include("send-push-notification.php");
        
    }  while ($row_rsCuisineRecipients = mysql_fetch_assoc($rsCuisineRecipients));
    
    $pushStatus = "New recipe signal sent at $date EST to totalRows_rsCuisineRecipients users.";
    
    if(isset($request['ingredients']))
    {
        foreach ($request['ingredients'] as $ingredient) 
        {	
            //to do randall
            //look into query ingredientid on insert in app

            $insertSetSQL = sprintf("INSERT INTO recipeingredients (ingredientname, quantity, unit, recipeorder, recipeid) VALUES (%s, %s, %s, %s, %s)",
                GetSQLValueString(mysql_real_escape_string($ingredient['ingredient']), "text"),
                GetSQLValueString(mysql_real_escape_string($ingredient['quantity']), "text"),
                GetSQLValueString(mysql_real_escape_string($ingredient['unit']), "text"),
                GetSQLValueString(mysql_real_escape_string($ingredient['recipeorder']), "text"),
                GetSQLValueString(mysql_real_escape_string($last_id), "int"));

                mysql_select_db($database_chewsrite, $chewsrite);
                $Result2 = mysql_query($insertSetSQL, $chewsrite) or die(mysql_error());
        }
        
        
        $status .= ". ingredients saved";
    }
    
    if($request['folderids'] && $request['folderids'] != "")
    {
        $insertSetSQL = sprintf("INSERT INTO recipefolders (folderids, recipeid, userid) VALUES (%s, %s, %s)",
            GetSQLValueString(mysql_real_escape_string($request['folderids']), "text"),
            GetSQLValueString(mysql_real_escape_string($last_id), "int"),
            GetSQLValueString(mysql_real_escape_string($request['userid'])));

            mysql_select_db($database_chewsrite, $chewsrite);
            $Result2 = mysql_query($insertSetSQL, $chewsrite) or die(mysql_error());
        
        $status .= ". folders saved";
    }
    
    if(isset($request['nutritionValLabels']))
    {
        $request['nutritionValLabels'] .= ",recipeid";
        $request['nutritionValues'] .= ",{$last_id}";
        
        
        $insertSQL = "INSERT INTO recipenutrition ({$request['nutritionValLabels']}) VALUES ({$request['nutritionValues']})";

        mysql_select_db($database_chewsrite, $chewsrite);
        $Result1 = mysql_query($insertSQL, $chewsrite) or die(mysql_error());	
        $status .= ". nutrition info saved";
    }
    
    if(isset($request['directions']))
    {
        foreach ($request['directions'] as $ingredient) 
        {	
            //to do randall
            //look into query ingredientid on insert in app

            $insertSetSQL = sprintf("INSERT INTO recipedirections (title, directions, directionsorder, recipeid) VALUES (%s, %s, %s, %s)",
                GetSQLValueString(mysql_real_escape_string($ingredient['title']), "text"),
                GetSQLValueString(mysql_real_escape_string($ingredient['directions']), "text"),
                GetSQLValueString(mysql_real_escape_string($ingredient['directionsorder']), "text"),
                GetSQLValueString(mysql_real_escape_string($last_id), "int"));

                mysql_select_db($database_chewsrite, $chewsrite);
                $Result2 = mysql_query($insertSetSQL, $chewsrite) or die(mysql_error());
        }
        
        
        $status .= ". directions saved";
    }

}

$response = ["status" => $status, "recipeid" => $last_id, "pushStatus" => $pushStatus];

echo json_encode($response);

?>
