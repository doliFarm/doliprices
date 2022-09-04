function createNew() {
	$("#add-more").hide();
	
	
	var data = '<tr id="new_row_ajax">' +
	'<td style="background:#f9fc9a;" contenteditable="true" id="titre" onBlur="addToHiddenField(this,\'titre\')" onClick="editRow(this);"></td>' +
	'<td style="background:#f9fc9a; width:300px; " contenteditable="true" id="texte" onBlur="addToHiddenField(this,\'texte\')" onClick="editRow(this);"></td>' +
	'<td style="background:#f9fc9a;" contenteditable="true" id="photo" onBlur="addToHiddenField(this,\'photo\')" onClick="editRow(this);">'+
		   '<img id="newsImage" src="#">'+
		   '<input type="file" accept="image/*" name="image" id="image" style="display: inline;">'+
	'</td>' +
	'<td style="background:#f9fc9a;" contenteditable="true" id="programmation" onBlur="addToHiddenField(this,\'programmation\')" onClick="editRow(this);"></td>' +
	'<td style="background:#f9fc9a;" contenteditable="true" id="sorties" onBlur="addToHiddenField(this,\'sorties\')" onClick="editRow(this);"></td>' +
	'<td style="background:#f9fc9a;" contenteditable="true" id="notes_priv" onBlur="addToHiddenField(this,\'notes_priv\')" onClick="editRow(this);"></td>' +
	'<td style="background:#f9fc9a;" contenteditable="true" id="date" onBlur="addToHiddenField(this,\'date\')" onClick="editRow(this);">'+
	'<td style="background:#f9fc9a;" contenteditable="true" id="auteur" onBlur="addToHiddenField(this,\'auteur\')" onClick="editRow(this);"></td>' +
	'<td><input type="hidden" id="save" /><input type="hidden" id="description" /><span id="confirmAdd"><a onClick="addToDatabase()" class="ajax-action-links">Save</a> / <a onclick="cancelAdd();" class="ajax-action-links">Cancel</a></span></td>' +	
	'</tr>';
  $("#table-body").append(data);
}

function cancelAdd() {
	$("#add-more").show();
	$("#new_row_ajax").remove();
}

function editingImg(e){
	// alert("da editingImg: "+e);
	var file_data = $('#image-'+e).prop('files')[0];
	$("#e").html('<img src="/mandalanews/imagesNews/'+e+'">');
}

function editRow(editableObj) {	
	$(editableObj).css("background","#f2f79e");
}

function saveToDatabase(editableObj,column,id) {
  $(editableObj).css("background","#F1F2EF url(/mandalanews/loaderIcon.gif) no-repeat right");
  $.ajax({
	url: "/mandalanews/edit.php",
	type: "POST",
	data:'column='+column+'&editval='+$(editableObj).text()+'&id='+id,
	success: function(data){
	  $(editableObj).css("background","#FFFFFF");
	}
  });
}

function addToDatabase() {
  var titre = $("#titre").val();
  var texte = $("#texte").val();
  var photo = document.getElementById('image').value; 
  var file_photo = $('#image').prop('files')[0];
  
  var programmation = $("#programmation").val();
  var sorties = $("#sorties").val();
  var notes_priv = $("#notes_priv").val();
  var date = $("#date").val();
  var auteur = $("#auteur").val();
  if (photo) {
		var startIndex = (photo.indexOf('\\') >= 0 ? photo.lastIndexOf('\\') : photo.lastIndexOf('/'));
		var filenamePhoto = photo.substring(startIndex);
		if (filenamePhoto.indexOf('\\') === 0 || filenamePhoto.indexOf('/') === 0) {
			filenamePhoto = filenamePhoto.substring(1);
		}
		// alert(filenamePhoto);
  }
  alert('filenamePhoto: '+filenamePhoto);
  
	  $("#confirmAdd").html('<img src="/mandalanews/loaderIcon.gif" />');
	  
	  // Uploading image
        var form_data = new FormData();                  
        form_data.append('file',file_photo);
		$.ajax({
			url: 'upload_img.php', // <-- point to server-side PHP script 
			dataType: 'text',  // <-- what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: form_data,                         
			type: 'post',
			success: function(php_script_response){
               // ??????????
			}
		 });
		 
		 
	  // Registering data into database
	  $.ajax({
		url: "/mandalanews/add.php",
		type: "POST",
		data:'titre='+titre+'&texte='+texte+'&photo='+filenamePhoto+'&programmation='+programmation+'&sorties='+sorties+'&notes_priv='+notes_priv+'&date='+date+'&auteur='+auteur,
		success: function(data){
		  $("#new_row_ajax").remove();
		  $("#add-more").show();		  
		  $("#table-body").append(data);
		},
	error: function(xhr, status) {
		// check if xhr.status is defined in $.ajax.statusCode
		// if true, return false to stop this function
		if (typeof this.statusCode[xhr.status] != 'undefined') {
			return false;
		}
		// else continue
		console.log('ajax.error');
		},
		statusCode: {
			404: function(response) {
				console.log('ajax.statusCode: 404');
			},
			500: function(response) {
				console.log('ajax.statusCode: 500');
			}
		}
	  });
}

function addToHiddenField(addColumn,hiddenField) {
	var columnValue = $(addColumn).text();
	$("#"+hiddenField).val(columnValue);
}

function deleteRecord(id) {
	if(confirm("Are you sure you want to delete this row?")) {
		$.ajax({
			url: "/mandalanews/delete.php",
			type: "POST",
			data:'id='+id,
			success: function(data){
			  $("#table-row-"+id).remove();
			}
		});
	}
}

function news_img(id) {
						var file_data = $('#userImage_'+id).prop('files')[0]; 
						document.getElementById("newsIMGpath_"+id).text=file_data;						
						var form_data = new FormData();                  
						form_data.append('file', file_data);
						alert(form_data);                             
						$.ajax({
							url: '/mandalanews/upload_img.php', // <-- point to server-side PHP script 
							dataType: 'text',  // <-- what to expect back from the PHP script, if anything
							cache: false,
							contentType: false,
							processData: false,
							data: form_data,                         
							type: 'post',
							success: function(php_script_response){
								alert(php_script_response); // <-- display response from the PHP script, if any
								document.getElementById("newsIMG_"+id).innerHTML = php_script_response;
								saveToDatabase(document.getElementById("newsIMGpath_"+id),'image',id);
							}
						 });
			}