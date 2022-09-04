<?php

	if($_FILES['file']['name'] != ''){
		/*
			$test = explode('.', $_FILES['file']['name']);
			$extension = end($test);    
			$name = rand(100,999).'.'.$extension;
		*/
        $name =$_FILES['file']['name'];
		$location = 'imagesNews/'.$name;
		move_uploaded_file($_FILES['file']['tmp_name'], $location);

		echo $location;
	} else {
		echo '<p>Error reading image file </p>';
	}

?>