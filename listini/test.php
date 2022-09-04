<?php
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

   require "key.php";
   
   $k = $_GET['k']; 
   $s = "12345carlo"; 
   for ($i=1;$i<56;$i++) {
	    echo "$i - ";
		echo  "stringa base $s$i =>".keygen($s.$i,$i);
		echo "<br>";
   }
   
?>