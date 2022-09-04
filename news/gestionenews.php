<?php
require_once("dbcontroller.php");
$db_handle = new DBController();

$sql = "SELECT * from _posts"; 
$posts = $db_handle->runSelectQuery($sql);

?>
<table class="table table-striped table-bordered table-responsive">
  <thead>
	<tr>
	  <th style="width: 100px;" class="table-header">Titre</th>
	  <th style="width:300px" class="table-header">Texte</th>
	  <th class="table-header">Photo</th>
	  <th class="table-header">Programmation</th>
	  <th class="table-header">Sorties</th>
	  <th class="table-header">Notes privées</th>
	  <th class="table-header">Date</th>
	  <th class="table-header">Auteur</th>
	  <th class="table-header">Actions</th>
	</tr>
  </thead>
  <tbody id="table-body">
  <?php
		if(!empty($posts)) { 
			foreach($posts as $k=>$v) {
	?>
				<tr  id="table-row-<?php echo $posts[$k]["id"]; ?>">
					<td class="align-top" contenteditable="true" onBlur="saveToDatabase(this,'titre','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["titre"]; ?></td>
					<td class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'texte','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["texte"]; ?></td>
					<td id="photo-<?php echo $posts[$k]["id"]; ?>" class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'photo','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo '<img width="100px" heigth="100px" src="'.$posts[$k]["photo"].'">'; ?></td>
					<td class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'programmation','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["programmation"]; ?></td>
					<td class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'sorties','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["sorties"]; ?></td>
					<td class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'notes_priv','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["notes_priv"]; ?></td>
					<td class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'date','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["date"]; ?></td>
					<td class="align-top"  contenteditable="true" onBlur="saveToDatabase(this,'auteur','<?php echo $posts[$k]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[$k]["auteur"]; ?></td>
					<td><a class="ajax-action-links" onclick="deleteRecord(<?php echo $posts[$k]["id"]; ?>);">Delete</a></td>
			  </tr>

	<?php		
			}
		}
	?>
	
</tbody>
</table>
<div class="btn btn-primary" id="add-more" onClick="createNew();">Add More</div>

