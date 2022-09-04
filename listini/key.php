<?php
/*
function keygen($s, int $week) {
	//  $week = 40;
	 
		$DEBUG = true; 

        $l = strlen($s);
        $m = 30;
        $k_str = "AqwIEil9zHZhjuIWJHGFKIUmdcaHjk";   // stringa di codifica
		// $pass = ($week) %  abs($m-$l);
		$pass = 4;
		
		if ($DEBUG) echo " keygen (pass): $pass ";
		
        // Aggiusto dimensione stringa	
		
        for ($i=0; $i < abs($m-$l); $i++) {
            $c = substr($k_str,$i,1);
  		    $s = $s.$c;
        }
	
        $s = $s.substr($k_str,0,$m-$l);
        for ($i=1; $i < $m; $i=$i+$pass) {
			$x = substr($k_str,$i,1);
			$s = substr_replace($s, $x, $i, 1);
		}
		
		
		
		if ($DEBUG) echo " keygen (s): $s ";
		
		$sb = base64_encode($s);
		
		for ($i=1; $i < $m; $i=$i+$pass) {
			$x = substr($k_str,$i,1);
			$sb = substr_replace($sb, $x, $i, 1);
		}
		
		if ($DEBUG) echo " keygen (sb): $sb <br>";
		
		return $sb;
}

*/

function keygen($simple_string,$week, $m=30 ) {
	/*
	$k_str = 'A96IEil9zHZhjuIejuGFKIUmdcaHjk';
	// Aggiusto la lunghezza minima della stringa da codificare 
		for ($i=0; $i < abs($m-strlen($simple_string)); $i++) {
            $c = substr($k_str,$i,1);
  		    $simple_string = $simple_string.$c;
        }*/
		
		// Storingthe cipher method 
		$ciphering = "AES-128-CTR";

		// Using OpenSSl Encryption method 
		$iv_length = openssl_cipher_iv_length($ciphering);
		$options   = 0;

		// Non-NULL Initialization Vector for encryption 
		// $encryption_iv = '1234567891011121';
        $encryption_iv = 'AqwIEil9zHZhjuIWJHGFKIUmdcaHjk';
		// Storing the encryption key 
		// $encryption_key = "W3docs";
		 $encryption_key = "Ma".$week."ndala";
     
		// Using openssl_encrypt() function to encrypt the data 
		$encryption = openssl_encrypt($simple_string, $ciphering, $encryption_key, $options, $encryption_iv);
	
		// Displaying the encrypted string 	
		$s = str_replace(['+','/','='],['P10s','D4sH','3q84L'],$encryption);
		return $s;
/*
		// Non-NULL Initialization Vector for decryption 
		// $decryption_iv = '1234567891011121';
        $decryption_iv = 'AqwIEil9zHZhjuIWJHGFKIUmdcaHjk';
		// Storing the decryption key 
		// $decryption_key = "W3docs";
		$decryption_key = "Ma".$week."ndala";
		
		// Using openssl_decrypt() function to decrypt the data 
		$decryption = openssl_decrypt($encryption, $ciphering, $decryption_key, $options, $decryption_iv);

		// Displaying the decrypted string 
		echo "Decrypted String: " . $decryption;
*/
}
		
?>

