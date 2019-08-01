<?php


//$request = json_decode(file_get_contents('php://input'), TRUE);


include("getID3/getid3/getid3.php");

if(isset($_FILES['file']))
{
    $file = basename($_FILES['file']['name']);

    if (move_uploaded_file($_FILES['file']['tmp_name'], "uploads/{$file}")) 
    {
        $status = "file uploaded successfully";
        
        $pathName = "uploads/{$file}";

        $getID3 = new getID3;
        $ThisFileInfo = $getID3->analyze($pathName);
        $len= @$ThisFileInfo['playtime_seconds']; //
                
        $costPerSecond = 0.02/60;
        $markup = 0.5;
        $markupTotal = (costPerSecond * markup) + costPerSecond;

        //console.log("costPerSecond: " + costPerSecond);
        //console.log("markupTotal: " + markupTotal);


        $estimatedCost = $len * $markupTotal;

        //console.log("estimatedCost: " + estimatedCost.toFixed(2));


        if($estimatedCost < 1)
        {
            $estimatedCost = 1;
        }
        
        try 
        {
            //unlink($pathName);
            $filestatus = "delete success";
        }
        catch(Exception $e)
        {
            $filestatus = "delete error";
        }        
                
        $response = ["status" => $status,"filestatus" => $filestatus, "len" => $len, "estimatedCost" =>  floatval(number_format($total,2))];

        echo json_encode($response);
    }
    else
    {
        $status = "file not uploaded";
        
        $response = ["status" => $status];

        echo json_encode($response);
    }
}
else
{
    $status = "no file";
    $response = ["status" => $status];

    echo json_encode($response);
}

?>