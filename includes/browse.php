<?php

session_start();

if(isset($_POST['fileList']))
{	
	//add is Dir condition
	
	$_SESSION['file'] = $_POST['fileList'];

		
		$code = htmlspecialchars(file_get_contents($_SESSION['file']));
		$_SESSION['code'] = $code;
	
//	if($_POST['fileList'] == "browse.php")
//	{
//		$_SESSION['code'] = null;
//		$_SESSION['file'] = null;
//		header("Location: Examples/browse.php");
//	}
//	else
//	{
//		$_SESSION['file'] = $_POST['fileList'];
//
//		
//		$code = htmlspecialchars(file_get_contents($_SESSION['file']));
//		$_SESSION['code'] = $code;
//	}	
	
	//$_SESSION['code'] = file_get_contents($_SESSION['file']);
}

if(isset($_POST['codeTxt']))
{
	//echo "save code<br>";
	
	$_SESSION['file'] = $_POST['fileTxt'];
	
	$code = htmlspecialchars($_POST['codeTxt']);
	$_SESSION['code'] = $code;
	
	//serialize($_SESSION['code']) = $_POST['codeTxt'];
	
	file_put_contents($_POST['fileTxt'],htmlspecialchars_decode($code));
}

?>

<style type="text/css">
#codeTxt {
    width: 100%;
    height: 87%;
    margin-bottom: 5px;
}
</style>

<head>
	
<meta charset="utf-8">
<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0">

</head>

<?php 

if ($handle = opendir('.')) {

	while (false !== ($entry = readdir($handle))) 
	{
		if ($entry != "." && $entry != ".."  && $entry != ".DS_Store") { // && !is_dir($entry)
			
			$entries[] = $entry;

//			if($_SESSION['file'] == $entry)
//			{
//				echo "<option value='".$entry."' selected='selected'>".$entry."</option>";
//			}
//			else
//			{
//				echo "<option value='".$entry."'>".$entry."</option>";
//			}
		}
	}

	closedir($handle);
}

?>

<form name="form1" method="post" action="">
  <label for="fileTxt">File: </label>
  <select name="fileList" id="fileList">
    <option value="1">Select file...</option>
	  
<?php
	  
	  sort($entries);
	  
	foreach($entries as $entry)
	{
		if($_SESSION['file'] == $entry)
		{
			echo "<option value='".$entry."' selected='selected'>".$entry."</option>";
		}
		else
		{
			echo "<option value='".$entry."'>".$entry."</option>";
		}
	}

?>
  
  </select>
  <input type="submit" name="submit" id="submit" value="Open">
</form>


<form name="form2" method="post" action="">
	<p>
	  <label for="textfield">Filename:</label>
	  <input type="text" name="fileTxt" id="fileTxt" value="<?php if(isset($_SESSION['file'])){echo $_SESSION['file'];}?>">
  </p>
	<p>
	  <textarea name="codeTxt" id="codeTxt" cols="45" rows="5"><?php if(isset($_SESSION['code'])){echo $_SESSION['code'];}?>
  </textarea>
	  <br>
	  <input type="submit" name="submit" id="submit" value="Save">
  </p>
</form>
        