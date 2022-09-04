<?php

function connectDB() {
	$host = "mandalaophotos.mysql.db"; 
	$user = "mandalaophotos";
	$password = "Omhahum12";
	$database = "mandalaophotos";
	$conn = mysqli_connect($host,$user,$password,$database);
	return $conn;
}

function runSelectQuery($query,$conn) {
	$result = mysqli_query($conn,$query);
	while($row=mysqli_fetch_assoc($result)) {
		$resultset[] = $row;
	}
	if(!empty($resultset))
		return $resultset;
}

function letturaNews($week) {
	// header (“Content-Type: text / html; charset = utf-8”);	
	$conn = connectDB();
	//$week=49;
	
	$sql = "SELECT photos_images.name,photos_images.comment, photos_images.file, photos_images.path 
			FROM photos_images 
				JOIN photos_image_tag ON photos_images.id = photos_image_tag.image_id 
				JOIN photos_tags ON photos_image_tag.tag_id = photos_tags.id 
			where photos_tags.name = '$week'";
	$posts = runSelectQuery($sql,$conn);
	echo '<hr>';
	echo '<div class="row d-flex align-items-center justify-content-center">';

	if(!empty($posts)) { 
	    echo '<div class="row  justify-content-center">';
		foreach($posts as $k=>$v) {
		  echo '
			<div class="col-md-4 col-xs-1">
				<div class="card">
					<img  src="/mandalagallery/'.$posts[$k]["path"].'" class="card-img-top" alt="'.$posts[$k]["titre"].'">
					<div class="card-body">
						<h5 class="card-title">'.$posts[$k]["name"].'</h5>
						<p class="card-text">'.$posts[$k]["comment"].'</p>
				  <!--  <a href="#" class="btn btn-primary">Go somewhere</a> -->
					</div>
				</div>
			</div>
			';
		}
		echo '</div>';
	} else echo "Nessuna notizia per questa settimana";

	echo '</div>';
}
	

if (isset($_GET['week'])){
	$week = $_GET['week'];
}
// letturaNews($week);

?>

