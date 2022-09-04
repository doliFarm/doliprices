<?php

// TODO - mysql_real_escape_string


require_once("dbcontroller.php");
$db_handle = new DBController();

if(!empty($_POST["titre"])) {
	
 /*	
	$title = mysql_real_escape_string(strip_tags($_POST["title"]));
	$description = mysql_real_escape_string(strip_tags($_POST["description"]));
	*/
	$titre = $_POST["titre"];
	$texte = $_POST["texte"];
	$photo = $_POST["photo"];
	$programmation = $_POST["programmation"];
	$sorties = $_POST["sorties"];
	$notes_priv = $_POST["notes_priv"];
	$date = $_POST["date"];
	$auteur = $_POST["auteur"];

    $sql = "INSERT INTO _posts (titre,texte,photo,programmation,sorties,notes_priv,date,auteur) VALUES ('" . $titre . "','" . $texte. "','" . $photo. "','" . $programmation. "','" . $sorties. "','" . $notes_priv. "','" . $date. "','" . $auteur . "')";
    $faq_id = $db_handle->executeInsert($sql);
	if(!empty($faq_id)) {
		$sql = "SELECT * from _posts WHERE id = '$faq_id' ";
		$posts = $db_handle->runSelectQuery($sql);
	}
?>
<tr class="table-row" id="table-row-<?php echo $posts[0]["id"]; ?>">
<td contenteditable="true" onBlur="saveToDatabase(this,'titre','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["titre"]; ?></td>
<td  style="width:300px" contenteditable="true" onBlur="saveToDatabase(this,'texte','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["texte"]; ?></td>
<td id="photo" name="photo" contenteditable="true" onBlur="saveToDatabase(this,'photo','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo '<img width="100px" heigth="100px" src="'.$posts[0]["photo"].'">'; ?></td>
<td contenteditable="true" onBlur="saveToDatabase(this,'programmation','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["programmation"]; ?></td>
<td contenteditable="true" onBlur="saveToDatabase(this,'sorties','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["sorties"]; ?></td>
<td contenteditable="true" onBlur="saveToDatabase(this,'notes_priv','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["notes_priv"]; ?></td>
<td contenteditable="true" onBlur="saveToDatabase(this,'date','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["date"]; ?></td>
<td contenteditable="true" onBlur="saveToDatabase(this,'auteur','<?php echo $posts[0]["id"]; ?>')" onClick="editRow(this);"><?php echo $posts[0]["auteur"]; ?></td>
<td><a class="ajax-action-links" onclick="deleteRecord(<?php echo $posts[0]["id"]; ?>);">Delete</a></td>
</tr>  
<?php } ?>
