 
<html>
    <title>Mandala News</title>
    <head>
            <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>			
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>				
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="gestionenews.js"></script>
		<link href="gestionenews.css">

	</head>
	
<body>

<?php
	require_once 'letturanews.php';	
	echo '
		<div class="card">
				<div class="card-img-top d-flex align-items-center bg-light">
						<div>
								<img class="img-fluid" src="logo.jpg" alt="Card image cap">
						</div>
						<h5 class="card-title">Gestione Notizie Mandala</h5>
				 </div>
		</div>';
	
	echo '<hr>';
	echo '<br><br><br><br>';
	echo '<div id="pages">';
		
		// SHOW menu bar
		// **************************************************************
		echo '<ul class="nav nav-tabs" id="myTab" role="tablist">
				  <li class="nav-item" role="presentation">
					<button class="nav-link active" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos" type="button" role="tab" aria-controls="photos" aria-selected="true">Galleria Foto</button>
				  </li>
				  <li class="nav-item" role="presentation">
					<button class="nav-link" id="news-tab"  data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab" aria-controls="false" aria-selected="true">News della settimana</button>
				  </li>
				   <li class="nav-item" role="presentation">
					 <button class="nav-link" id="newscollection-tab"  data-bs-toggle="tab" data-bs-target="#newscollection" type="button" role="tab" aria-controls="false" aria-selected="true">News collection</button>
				  </li>
			</ul>';
	


		echo '<div class="tab-content" id="myTabContent">';     
			
			
			echo '<div class="tab-pane fade" id="photos" role="tabpanel" aria-labelledby="photos-tab">';
			echo '     <hr>';
			echo '<h4>Photos Gallery</h4> ';
			echo '	<iframe width="100%" height="100%" frameborder="0" src="/mandalagallery"> </iframe>';
			echo '</div>';
			
			echo '<div class="tab-pane fade" id="news" role="tabpanel" aria-labelledby="news-tab">';
			echo '     <hr>';
			echo '<h4>Dernières nouvelles de nos producteurs</h4>
						<br><br>';
			// echo '	<iframe width="100%" height="100%" frameborder="0" src="/mandalanews/letturanews.php?week=49"> </iframe>';
			letturanews($week);
			echo '</div>';
			
			echo '<div class="tab-pane fade" id="newscollection" role="tabpanel" aria-labelledby="news-tab">';
			echo '     <hr>';
			echo '<h4>News collection</h4>
						<br><br>';
			echo '	<iframe width="100%" height="100%" frameborder="0" src="/gestionenews/gestionenews.php"> </iframe>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
?>

</body>
</html>