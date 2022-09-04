<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

$sql = "SELECT * from _posts"; 
$posts = $db_handle->runSelectQuery($sql);
?>


<h4>Dernières nouvelles de nos producteurs</h4>
<br><br><br><br>
<div class="row d-flex align-items-center justify-content-center">

<?php
if(!empty($posts)) { 
	foreach($posts as $k=>$v) {
	  ?>
		<div class="col-2">
			<div class="card">
					<img src="<?php echo $posts[$k]["photo"]; ?>" class="card-img-top" alt="...">
					<div class="card-body">
						<h5 class="card-title"><?php echo $posts[$k]["titre"]; ?></h5>
						<p class="card-text"><?php echo $posts[$k]["texte"]; ?></p>
				  <!--  <a href="#" class="btn btn-primary">Go somewhere</a> -->
					</div>
			</div>
		</div>
<?php
    }
}
?>
</div>