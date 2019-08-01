<?php
	
	function en ($string) 
	{	
		$pass = '12342lkr32jlasflk';
		$method = 'aes128';
		
		$en =  openssl_encrypt ($string, $method, $pass);
		
		//echo "en: $en";
			
		return $en;		
	}
	
	function de ($string)
	{		
		//echo "<br>de param: $string<br>";
		//echo "<br>count: " . count($string) . "<br>";
		
		$de;
		
		if(count($string) > 0)
		{
			$pass = '12342lkr32jlasflk';
			$method = 'aes128';
			
			$de = openssl_decrypt ($string, $method, $pass);
			//echo "<br>dec: $de<br>";
		}
				
		return $de;
	}
	
?>