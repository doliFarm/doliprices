<?php
/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *	\file       doliprices/dolipricesindex.php
 *	\ingroup    doliprices
 *	\brief      Home page of doliprices top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--; $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
	$res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';

// Load translation files required by the page
$langs->loadLangs(array("doliprices@doliprices"));
global $conf, $mysoc;

$action = GETPOST('action', 'aZ09');


// Security check
// if (! $user->rights->doliprices->myobject->read) {
// 	accessforbidden();
// }
$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
	$action = '';
	$socid = $user->socid;
}

$max = 5;
$now = dol_now();


/*
 * Actions
 */

// None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);

llxHeader("", $langs->trans("DolipricesArea"));

print load_fiche_titre($langs->trans("DolipricesArea"), '', 'doliprices.png@doliprices');

// ---------------------- BEGINNING --------------------------------
// print '<div class="fichecenter"><div class="fichethirdleft">';

$DEBUG = FALSE;
$MANUTENZIONE = FALSE;

if ($MANUTENZIONE) {
	echo "<h3>Sistema in manutenzione</h3>";
	return;
}


set_error_handler(function(int $number, string $message) {
	GLOBAL $DEBUG;
   if ($DEBUG) {
	   echo "Handler captured error $number: '$message'" . PHP_EOL  ;
   }
});


function generateRandomString($length = 30) {
	return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

// require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpspreadsheet/src/autoloader.php';
require_once DOL_DOCUMENT_ROOT.'/includes/phpoffice/phpspreadsheet/src/autoloader.php';
require_once DOL_DOCUMENT_ROOT.'/includes/Psr/autoloader.php';
require_once PHPEXCELNEW_PATH.'Spreadsheet.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

require_once  DOL_DOCUMENT_ROOT.'/custom/doliprices/listini/key.php';  // required to generate the key for the file

$numCLIENTI = 0;
$numProduttori = 7;

// Setting the Week number of the year
//*************************************
$ddate = date('Y/m/d');
$edate = strtotime($ddate."+ 5 days");
$date = new DateTime($ddate);
$week = $date->format("W");

// echo "1.<br>";
// Setting HTML Header
// ************************************
?>
<html>
		<head>
				<!-- Required meta tags -->
                <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>				
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>				
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
				<script src="mandalanews/gestionenews.js"></script>
				<link href="mandalanews/gestionenews.css">
				
				
				
				<style>
						
					.LockOn {
						display: block;
						visibility: visible;
						position: absolute;
						z-index: 999;
						top: 0px;
						left: 0px;
						width: 105%;
						height: 105%;
						background-color:white;
						vertical-align:bottom;
						padding-top: 20%; 
						filter: alpha(opacity=75); 
						opacity: 0.75; 
						font-size:large;
						color:blue;
						font-style:italic;
						font-weight:400;
						background-image: url("../Common/loadingGIF.gif");
						background-repeat: no-repeat;
						background-attachment: fixed;
						background-position: center;
					}
												
						.sticky {
						  position: fixed;
						  top: 0;
						  width: 100%;
						}

						.sticky + .content {
						  padding-top: 50px;
						}
						
						
						.progress {
							  position: relative;
							  height: 2px;
							  display: block;
							  width: 100%;
							  background-color: white;
							  border-radius: 2px;
							  background-clip: padding-box;
							  /*margin: 0.5rem 0 1rem 0;*/
							  overflow: hidden;

							}
							.progress .indeterminate {
								background-color:black; }
							.progress .indeterminate:before {
							  content: '';
							  position: absolute;
							  background-color: #2C67B1;
							  top: 0;
							  left: 0;
							  bottom: 0;
							  will-change: left, right;
							  -webkit-animation: indeterminate 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
									  animation: indeterminate 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite; }
							.progress .indeterminate:after {
							  content: '';
							  position: absolute;
							  background-color: #2C67B1;
							  top: 0;
							  left: 0;
							  bottom: 0;
							  will-change: left, right;
							  -webkit-animation: indeterminate-short 2.1s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
									  animation: indeterminate-short 2.1s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
							  -webkit-animation-delay: 1.15s;
									  animation-delay: 1.15s; }

							@-webkit-keyframes indeterminate {
							  0% {
								left: -35%;
								right: 100%; }
							  60% {
								left: 100%;
								right: -90%; }
							  100% {
								left: 100%;
								right: -90%; } }
							@keyframes indeterminate {
							  0% {
								left: -35%;
								right: 100%; }
							  60% {
								left: 100%;
								right: -90%; }
							  100% {
								left: 100%;
								right: -90%; } }
							@-webkit-keyframes indeterminate-short {
							  0% {
								left: -200%;
								right: 100%; }
							  60% {
								left: 107%;
								right: -8%; }
							  100% {
								left: 107%;
								right: -8%; } }
							@keyframes indeterminate-short {
							  0% {
								left: -200%;
								right: 100%; }
							  60% {
								left: 107%;
								right: -8%; }
							  100% {
								left: 107%;
								right: -8%; } }
								
				</style>
				
				<script>
					function CopyURL(evt,ref) {
							riferimento = document.getElementById(ref);
							evt.preventDefault();
							navigator.clipboard.writeText(riferimento.href.replace(/auto=1/,"auto=0")).then(() => {
							  /* clipboard successfully set */
							}, () => {
							  /* clipboard write failed */
							});
					}
				</script>
		   
			    <script>
					function sendingEmail(e) {
						// Fetching Form data
						var	 emailTO = document.getElementById("formEmail_"+e).value;
						var	 emailCC = document.getElementById("formCC_"+e).value;
						var	 emailMSG = "<html><body>"+document.getElementById("formMessage_"+e).value+"</body></html>";
						var	 emailSBJ = document.getElementById("formSubject_"+e).value;
						var	 emailPDF = document.getElementById("formPDF_"+e).checked;
						var	 emailXLS = document.getElementById("formXLS_"+e).checked;
						var	 emailPDFfile = document.getElementById("formPDFfile_"+e).value;
						var	 emailXLSfile = document.getElementById("formXLSfile_"+e).value;
						
						emailPDFfile = emailPDFfile.replace("../", "");
						emailXLSfile = emailXLSfile.replace("../", "");
						
						
						// jQuery Ajax Post Request						
						$.ajax({
									url: "../sendEmail.php",
									type: "POST",
									data:'emailTO='+emailTO+'&emailCC='+emailCC+'&emailMSG='+emailMSG+'&emailSBJ='+emailSBJ
									     +'&emailPDF='+emailPDF+'&emailXLS='+emailXLS+'&emailPDFfile='+emailPDFfile+'&emailXLSfile='+emailXLSfile,
									success: function(data){
										if ( data.includes("ERROR")) {
											document.getElementById("formFEEDBACK_"+e).innerHTML = data;
										} else {
									  // $(editableObj).css("background","#FDFDFD");
											document.getElementById("formEmail_"+e).readOnly = true;
											document.getElementById("formMessage_"+e).readOnly = true;
											document.getElementById("formSubject_"+e).readOnly = true;
											document.getElementById("formSubmit_"+e).disabled = true;
											document.getElementById("formPDF_"+e).disabled = true;
											document.getElementById("formXLS_"+e).disabled = true;
											document.getElementById("formCC_"+e).disabled = true;
											document.getElementById("formSubmit_"+e).innerHTML = "Envoyé";
											document.getElementById("formFEEDBACK_"+e).innerHTML = data;
										}
									}
									// TODO ERROR PART
						});
							
					}
			</script>
			
			<script type="text/javascript">
				function news_img(e) {
							var file_data = $('#userImage').prop('files')[0];   
							var form_data = new FormData();                  
							form_data.append('file', file_data);
							alert(form_data);                             
							$.ajax({
								url: 'upload_img.php', // <-- point to server-side PHP script 
								dataType: 'text',  // <-- what to expect back from the PHP script, if anything
								cache: false,
								contentType: false,
								processData: false,
								data: form_data,                         
								type: 'post',
								success: function(php_script_response){
									alert(php_script_response); // <-- display response from the PHP script, if any
								    document.getElementById("newsIMG").innerHTML = php_script_response;
								}
							 });
				}
				
				// Per la barra di progressione
				$(window).on('load', function () {
					// alert('Listini Caricati');
				     document.getElementById("PreLoaderBar").style.display = "none";
				}); 
				 function loadingProgress () {
					 document.getElementById("PreLoaderBar").style.display = "block";
				 }
			</script>
		

	</head>
	<body>
		
<?php

// Setting HTML Page 
// ***********************************

// echo '<div id="coverScreen"  class="LockOn"></div>';

echo '<div id="container-fluid">';

echo '<div id="header">';
echo '
		<p class="col p-2 m-0">
		   <h4 class="text-end">Settimana numero: *'. $week .'* ('. $date->setISODate(date("Y"), $week, "1")->format('d/m/Y')." - ".$date->setISODate(date("Y"), $week, "5")->format('d/m/Y').')</h4>
   
		</p>
		<p>		
			<form action="index.php" method="post" onsubmit="return loadingProgress()" id="loadFileForm" enctype="multipart/form-data">
				<b>Caricare un file Excel di listino nel formato appropriato(*):</b> <input type="file" name="fileToUpload" id="fileToUpload"> 
					<input type="hidden" name="token" value="'.newToken().'">
				<input id="loadFilebtn" name="submit" class="btn btn-primary" type="submit" value="Carica file">
			</form>
		</p>
		<p>(*)Poi utilizzare il template che trovi in questo link: <a href="template/priceListTemplate.xlsx">Template Listini</a></p>';
echo '<div class="progress" id="PreLoaderBar"> <div class="indeterminate"></div></div>';
echo '</div> <!-- header -->';


		// Loading the spreadsheet file with the List of prices
		// TODO:
		//   - direct connection with GDrive
		// **************************************
		$target_dir = "uploads/";
		// $target_file = $target_dir . $week.'-'. basename($_FILES["fileToUpload"]["name"]);
		$target_file = $target_dir . $week.'-priceslist.xlsx';
		$uploadOk = 0;
		$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

		// Check the status of the file to be uploaded

		if (isset($_POST["submit"])) {  // A new file has been uploaded
			    // TODO Check the type of the file
				if ($target_file == "uploads/") {
						$msg = "cannot be empty";
						$uploadOk = 0;
				} // Check if file already exists
				elseif (file_exists($target_file)) {
						$msg = "The File already exists.";
						$uploadOk = 1;
				}
				// Check file size
				elseif ($_FILES["fileToUpload"]["size"] > 5000000) {
						$msg = "Sorry, your file is too large.";
						$uploadOk = 0;
				}else {
					$uploadOk = 1;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
						$msg = "Sorry, your file was not uploaded.";
				} else { // if everything is ok, try to upload file
						if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
								$uploadOk = 2;
								$msg = "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
						}
				}
				
				$inputFileName = $target_file;
				
		} else {  // We try with an existing file
				$target_file = glob($target_file.'*');
				$inputFileName = $target_file[0];
		}
		if (file_exists($inputFileName)) {	
            if ($DEBUG) echo "trovato 	$inputFileName";
		    $messaggioOrigineDati = $inputFileName.' ('. date("F d Y H:i",filemtime($inputFileName)).')';
		}else {
			$messaggioOrigineDati = "Données non disponibles";  // TODO TRADUZIONE
			echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
			  <strong><img src="imgs/alert.ico"> Dati non caricati. E\' necessario caricare i dati da GDrive.</strong>
			  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>';
		}	

	    echo '<p class="text-end"><b>Origine listino:</b> '.$messaggioOrigineDati.'<p>';
			
		echo '<div id="pages">';
		
		// SHOW menu bar
		// **************************************************************
		echo '<ul class="nav nav-tabs" id="myTab" role="tablist">
				  <li class="nav-item" role="presentation">
					<button class="nav-link active" id="listaPrezzi-tab" data-bs-toggle="tab" data-bs-target="#listaPrezzi" type="button" role="tab" aria-controls="listaPrezzi" aria-selected="false">Listino Prezzi Attuale</button>
				  </li>
				  <li class="nav-item" role="presentation">
					<button class="nav-link" id="news-tab" data-bs-toggle="tab" data-bs-target="#news" type="button" role="tab" aria-controls="news" aria-selected="true">Notizie dei produttori</button>
				  </li>
				  <li class="nav-item" role="presentation">
					<button class="nav-link " id="invioOfferte-tab" data-bs-toggle="tab" data-bs-target="#invioOfferte" type="button" role="tab" aria-controls="invioOfferte" aria-selected="false">Invio Listini</button>
				  </li>
				  <!--
				  <li class="nav-item" role="presentation">
					<button class="nav-link" id="storicoPrezzi-tab" data-bs-toggle="tab" data-bs-target="#storicoPrezzi" type="button" role="tab" aria-controls="storicoPrezzi" aria-selected="false">Historique des prix</button>
				  </li> -->
			</ul>';
	


	echo '<div class="tab-content" id="myTabContent">';           

// Reading the spreadsheet file
// **************************************
if ($DEBUG) echo "caricamento spreadsheet INIZIO<br>";
	if (file_exists($inputFileName)) {
			/**  Identify the type of $inputFileName  **/
		 $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

			/**  Create a new Reader of the type that has been identified  **/
		 $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");

			/**  Load $inputFileName to a Spreadsheet Object  **/
		$reader->setReadDataOnly(TRUE);
		$spreadsheet = $reader->load($inputFileName);
		
		
		// $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
		// $spreadsheet = $reader->getActiveSheet();

			/**  Convert Spreadsheet Object to an Array for ease of use  **/
			// $listino = $spreadsheet->getActiveSheet()->toArray();

			// $listino = $spreadsheet->getSheetByName('mandala')->toArray();
		$maxCell = $spreadsheet->getSheetByName('listino')->getHighestRowAndColumn();
		$listino = $spreadsheet->getSheetByName('listino')->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);

		// $clienti = $spreadsheet->getSheetByName('clients')->toArray();
		$maxCell = $spreadsheet->getSheetByName('clients')->getHighestRowAndColumn();
		$clienti = $spreadsheet->getSheetByName('clients')->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);
	}
if ($DEBUG) echo "caricamento spreadsheet FINE<br>";

// News from the producers
// ***************************************

echo '<div class="tab-pane fade" id="news" role="tabpanel" aria-labelledby="news-tab">';
echo '     <hr>';
echo '	   <h4>Notizie dei produttori</h4> ';
echo '<table border=0 width="100%"><tr><td>
<iframe id="NotizieProduttori"
    title=Notizie dei produttori"
    width="100%"
    height="500"
    src='.DOL_DOCUMENT_ROOT.'"/custom/doliprices/gallery/index.php">
</iframe></td><td>';
require_once DOL_DOCUMENT_ROOT.'/custom/doliprices/news/letturanews.php';
letturaNews($week);
echo '</td></tr></table>';

echo '</div>';


// (Envoi d'offre)
// Parsing CUSTOMER list 
// ***************************************

echo '<div class="tab-pane fade" id="invioOfferte" role="tabpanel" aria-labelledby="invioOfferte-tab">';

echo '<hr>';
echo '	   <h4>Invio Offerte</h4> ';
//echo '<tr><th>Clients</th><th>Transp. Indu</th><th>Transp. Euro</th><th>Marge</th><th>Listes des Prix</th><th>Actions</th></tr>';

/*
echo '<div  class="headerClient bg-light" id="listOfClient" >';
echo '	   <h6>Clients:</h4> ';
foreach( $clienti as $cliente )
{
	if (($cliente[0]!=NULL) && (strtoupper($cliente[0])!='CLIENTS')) {  // Avoid empty rows && header rows
	   print " ** <a href=".$_SERVER['PHP_SELF']."#$cliente[0]>$cliente[0]</a>";
	}
}
echo "</div>";
*/
echo '<table class="table  table-striped">';

// if (isset($_POST["submit"])) {   TEST
			
		foreach( $clienti as $cliente )
		{
			if ($DEBUG) echo "Elaborazione RIGA: $cliente[0]<br>";
			if (($cliente[0]!=NULL) && (strtoupper($cliente[0])!='CLIENTS')) {  // Avoid empty rows && header rows
					if ($DEBUG) echo "Elaborazione CLIENTE: $cliente[0]<br>";

					echo '<tr><th>Client</th><th>Actions</th></tr>';
					echo '<tr>';

						$chiavi[strtoupper($cliente[0])]=keygen($cliente[0],$week);
						$outputOfferClient[strtoupper($cliente[0])] = 'offers/'.$week.'_ListinoPrezzi'.$mysoc->name.'_'.$cliente[0].'_'.$chiavi[strtoupper($cliente[0])].'.xlsx';
						$outputOfferClientMarge[strtoupper($cliente[0])] = 'offersMarge/'.$week.'_ListinoPrezziMandala_'.$cliente[0].'_'.$chiavi[strtoupper($cliente[0])].'_marge.xlsx';
						$outputOfferClientMargeConfirmed[strtoupper($cliente[0])] = 'ordersConfirmedMarge/'.$week.'_ListinoPrezziMandala_'.$cliente[0].'_'.$chiavi[strtoupper($cliente[0])].'_marge.xlsx';
						if (file_exists($outputOfferClientMargeConfirmed[strtoupper($cliente[0])])) {
							$linkConfirmedMarge[strtoupper($cliente[0])] = DOL_DOCUMENT_ROOT.'/custom/doliprice/ordersConfirmedMarge/'.$week.'_ListinoPrezziMandala_'.$cliente[0].'_'.$chiavi[strtoupper($cliente[0])].'_marge.xlsx';
						} else {
							$linkConfirmedMarge[strtoupper($cliente[0])] = DOL_DOCUMENT_ROOT.'/custom/doliprice/offers/'.$week.'_ListinoPrezziMandala_'.$cliente[0].'_'.$chiavi[strtoupper($cliente[0])].'_marge.xlsx';
						}
						
						/*
						echo '<td> <a href="'.$linkConfirmedMarge[strtoupper($cliente[0])].'" target="new">'.$cliente[0].'</a></td>';  // Link al file generat
						echo '<td>' . $cliente[3] . '€</td>';
						echo '<td>' . $cliente[4] . '€</td>';
						echo '<td>' . ($cliente[5]*100-100) . '%</td>';
						
						*/
						echo '<td> 
							 <p>';
							if (file_exists($outputOfferClientMargeConfirmed[strtoupper($cliente[0])])) {				      
							  echo '<a href="'.$linkConfirmedMarge[strtoupper($cliente[0])].'" target="new"><h3>'.$cliente[0].'</h3></a>';
							} else {
								 echo '<a id="'.$cliente[0].'">';
								 echo '<h3>'.$cliente[0].'</h3>(in attesa dell\'ordine)';
							}
						echo '</p>
							 <p>
								 <b>Transp. Indu: </b>' . $cliente[3] . '€  <br>
								 <b>Transp. Euro: </b>' . $cliente[4] . '€ <br>
								 <b>Marge:</b> ' . ($cliente[5]*100-100) . '%
							 </p>
						</td>';  

						// link ai file XLS e PDF generati e alla pagina cliente
						echo '<td><p> 
								 <a href="'.$outputOfferClient[strtoupper($cliente[0])].'"><img src="imgs/xlsx.png"  width="30px" heigth="30px" target="_new"  title="offerta Excel"></a> |
														<a href="'.$outputOfferClient[strtoupper($cliente[0])].'.pdf" target="_new"><img src="imgs/pdf.svg" width="30px" heigth="30px"  title="offerta PDF"></a> |
														<a href="lettura_file.php?k='.$chiavi[strtoupper($cliente[0])].'&auto=1" id="id_'.$chiavi[strtoupper($cliente[0])].'" target="_new"><img src="imgs/link.png" width="30px" heigth="30px" title="offerta utente"></a>
														<button class="btn btn-link" onclick="CopyURL(event,\'id_'.$chiavi[strtoupper($cliente[0])].'\')"  title="copia il link offerta da inviare">Copy Link</button>
														
														</p>
								';  // Link al file generat

							           /*
										$messaggioCliente = "Bonjour ".str_replace(',', ', Bonjour', $cliente[1]).", <br>Voici la liste de prix de cette semaine:  http://www.mandalaorganicgrowers.com/lettura_file.php?k=".$chiavi[strtoupper($cliente[0])].". <br><br>
												
												
												<br><br>
												Anne Marie <br>
												 <br>
												Mobile :  0032 495 61 97 51 <br>
												Mandala Srl <br>
												Frankrijkstraat 27 <br>
												1755 Gooik <br>
												Belgium
										";
										$subjectEmail = "Mandala - liste des prix SEMAINE $week ";
										*/
										$messaggioCliente = str_replace('[client]', $cliente[1], str_replace('[key]','http://'.$_SERVER['HTTP_HOST'].DOL_URL_ROOT.'custom/doliprice/lettura_file.php?k='.$chiavi[strtoupper($cliente[0])],str_replace('[week]', $week, $conf->global->DOLIPRICES_BODYMAIL)));
										$subjectEmail = str_replace('[week]', $week, str_replace('[company]','Company',$conf->global->DOLIPRICES_OBJMAIL));
								// Action
						echo '<p style="background-color:#f8f8ff"> 
									 <form id="formMail_'.$cliente[0].'">
										 <input type="hidden" id="formPDFfile_'.$cliente[0].'" name="formPDFfile_'.$cliente[0].'" value="'.$outputOfferClient[strtoupper($cliente[0])].'.pdf"">
										 <input type="hidden" id="formXLSfile_'.$cliente[0].'" name="formXLSfile_'.$cliente[0].'" value="'.$outputOfferClient[strtoupper($cliente[0])].'">

											<div class="mb-3">
											  <label for="exampleFormControlInput1" class="form-label">Email address (indirizzi separati da virgola)</label>
											  <input type="email" class="form-control" id="formEmail_'.$cliente[0].'" value="'.$cliente[2].'">
											</div>
											<div class="mb-3">
											  <label for="exampleFormControlInput1" class="form-label">Email CC (indirizzi separati da virgola)</label>
											  <input type="email" class="form-control" id="formCC_'.$cliente[0].'" value="'.$conf->global->DOLIPRICES_CCMAIL.'">
											</div>
											<div class="mb-3">
											  <label for="exampleFormControlTextarea2" class="form-label">Object</label>
											  <textarea class="form-control" id="formSubject_'.$cliente[0].'" rows="1" >'.$subjectEmail.'</textarea>
											</div>
											<div class="mb-3">
											  <label for="exampleFormControlTextarea1" class="form-label">Message</label>
											  <textarea class="form-control" id="formMessage_'.$cliente[0].'" rows="3">'.$messaggioCliente.'</textarea>
											</div>
											<div class="mb-3">
												  <b>Allegati:</b>
												  PDF <input type="checkbox" id="formPDF_'.$cliente[0].'"  value="PDF">
												  XLS <input type="checkbox" id="formXLS_'.$cliente[0].'"  value="XLS">
											  </div>
											<button id="formSubmit_'.$cliente[0].'" type="button" onClick="sendingEmail(\''.$cliente[0].'\');" >Envoyer l\'offre</Button>
									 </form>	
									 </p>
									 <p id="formFEEDBACK_'.$cliente[0].'"></p>					 
								</td>';  // Link al file generat
						echo '</tr>';
			}
		}
// } TEST
echo '</table>';
echo '</div>  <!-- offerte clienti pannel tab -->';
// echo '</div>';


// Parsing PRICES LIST
// ***************************************



echo '<div class="tab-pane fade  show active" id="listaPrezzi" role="tabpanel" aria-labelledby="listaPrezzi-tab">';
echo '<hr>';


echo '<h3>Listino prezzi attuale</h3> ';




if ($DEBUG) echo '----------------------------------------------------------->'. $inputFileName;
echo '<table class="table table-striped">';
echo '<tr><th></th><th>Prodotti</th><th>Produttore</th><th>N° casse INDU</th><th>N° casse EURO</th><th>Kg/Box</th><th>Kg/INDUpallet</th><th>Kg/EUROpallet</th><th></th><th>PV Produttore</th><th></th></tr>';
foreach( $listino as $prodotto )
{
   if (strtoupper($prodotto[0])!='NO' && $prodotto[0]!=NULL) {  // Avoid empty rows
			if ($prodotto[0] == '*') {
				echo '<tr style="background-color:#FFCC00">';
			echo '<td></td><td>'.$prodotto[1].'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
						echo '</tr>';

			} else {
				echo '<tr>';
						echo '<td>'.$prodotto[0].'</td><td>'.$prodotto[1].'</td><td>'.$prodotto[2].'</td><td>'.$prodotto[3].'</td><td>'.$prodotto[4].'</td><td>'.$prodotto[5].'</td><td>'.$prodotto[6].'</td><td>'.$prodotto[7].'</td><td>'.$prodotto[8].'</td><td>'.$prodotto[9].'€</td>';
						echo '</tr>';
			}
	 }
}
echo '</table>';
echo '</div>  <!-- listino pannel -->';



// Storico Prezzi
// ****************************************************************	
/*		
echo '<div class="tab-pane fade" id="storicoPrezzi" role="tabpanel" aria-labelledby="storicoPrezzi-tab">
		  <hr>
		  <h4>Historique des prix</h4> 
	  </div> ';
		
*/

// Creating spreadsheets for each Customer
// *************************************************************

if ($DEBUG) {
	
		echo '<h5>DEBUG ATTIVO</h5>';
}

// Leggo la prima riga di intestazione con l'elenco dei fornitori
// TODO !!!!

// if ( ($uploadOk == 1) && isset($_POST["submit"]) )  {  //LG OTTIMIZZAZIONE
	
   foreach( $clienti as $cliente ) {   // for each customer
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		if ($cliente[0]==NULL) break;                     // termina sulle righe nulle
		
		if (strtoupper($cliente[0])=='CLIENTS') {
				// Si tratta della riga di intestazione.

				// TODO ATTENZIONE LAVORA SOLO PER I FORNITORI ATTUALI
					for ($i=6;$i<$numProduttori+6;$i++) $elencoFornitori[]=strtoupper($cliente[$i]);    // Leggo l'elenco dei fornitori
					// $elencoFornitori[]= 'BONETTI';
					// print_r($elencoFornitori);
		} else {
			
				$elencoClienti[]=str_replace(' ','',strtoupper($cliente[0]));

				// Prelevo lo stato dei fornitori per il cliente corrente
				// ATTENZIONE LAVORA SOLO PER I FORNITORI ATTUALI
				for ($i=6;$i<$numProduttori+6;$i++) $fornitoriAmmessi[strtoupper($cliente[0])][$elencoFornitori[$i-6]]=str_replace(' ','',strtoupper($cliente[$i]));

	//                  $outputOfferClient = 'offers/'.$week.'-Listino Prezzi Mandala-'.$cliente[0].'.xlsx';  // Nome del file per ogni cliente


				// Insert LOGO
				$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing->setName('Paid');
				$drawing->setDescription('Paid');
				$drawing->setPath('imgs/logo.jpg'); // put your path and image here
				$drawing->setCoordinates('B1');
				$drawing->setOffsetX(0);
				$drawing->setRotation(0);
				$drawing->getShadow()->setVisible(true);
				$drawing->getShadow()->setDirection(45);
				$drawing->setWorksheet($spreadsheet->getActiveSheet());

				// Insert Week of the year
				$sheet->getStyle('B9')->getAlignment()->setHorizontal('center');
				$sheet->getStyle('B9')->getFont()->setBold( true );
				$sheet->setCellValue('B9', 'Liste des prix / Prices List week: *'.$week.'* ('.$date->setISODate(date("Y"), $week, "1")->format('d/m/Y')." - ".$date->setISODate(date("Y"), $week, "5")->format('d/m/Y').')');


				// Insert Client information 
				$sheet->getStyle('D2')->getAlignment()->setHorizontal('right');
				$sheet->getStyle('D2')->getFont()->setBold( true );
				$sheet->setCellValue('D2', 'Client:');
				
				$sheet->getStyle('D3')->getAlignment()->setHorizontal('right');
				$sheet->getStyle('D3')->getFont()->setBold( true );
				$sheet->setCellValue('D3', 'Contact:');
				
				$sheet->getStyle('D4')->getAlignment()->setHorizontal('right');
				$sheet->getStyle('D4')->getFont()->setBold( true );
				$sheet->setCellValue('D4', 'email:');

				$sheet->getStyle('E2')->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('E2', $cliente[0]);
				
				$sheet->getStyle('E3')->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('E3',$cliente[1]);
				
				$sheet->getStyle('E4')->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('E4', $cliente[2]);
				
				$row = 10;   // Starting row

		
				$spreadsheet
										->getActiveSheet()
										->getStyle('J'.$row.':N'.$row)
										->getFill()
										->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
										->getStartColor()
										->setARGB('D0E5F6');
				$spreadsheet
										->getActiveSheet()
										->getStyle('O'.$row.':Q'.$row)
										->getFill()
										->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
										->getStartColor()
										->setARGB('D0E5F6');
				$row++;
				$spreadsheet
										->getActiveSheet()
										->getStyle('A'.$row.':H'.$row)
										->getFill()
										->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
										->getStartColor()
										->setARGB('D0E5F6');
				$spreadsheet
										->getActiveSheet()
										->getStyle('J'.$row.':N'.$row)
										->getFill()
										->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
										->getStartColor()
										->setARGB('D0E5F6');
				$spreadsheet
										->getActiveSheet()
										->getStyle('O'.$row.':Q'.$row)
										->getFill()
										->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
										->getStartColor()
										->setARGB('D0E5F6');

				$sheet->getStyle('B'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('B'.$row,'PRODUITS');
				$sheet->getStyle('C'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('C'.$row,'PRODUCTEUR');
				$sheet->getStyle('D'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('D'.$row,'N° de caisses INDU');
				$sheet->getStyle('E'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('E'.$row,'N° de caisses EURO');
				$sheet->getStyle('F'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('F'.$row,'Kg/ Box');
				$sheet->getStyle('G'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('G'.$row,'Kg/ INDUpallet');
				$sheet->getStyle('H'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('H'.$row,'Kg/ EUROpallet');

				$sheet->getStyle('J'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('J'.$row,'PV  transport incl. INDU pallet');
				$sheet->getStyle('K'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('K'.$row,'PV transport incl. EURO pallet');


				$sheet->getStyle('L'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('L'.$row,'Prix + transp. INDU pallet ('.$cliente[3].'€)');
				$sheet->getStyle('M'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('M'.$row,'Prix + transp. EURO pallet ('.$cliente[4].'€)');
                $sheet->getStyle('N'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('N'.$row,'Prix d\'achat');

				$sheet->getStyle('O'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('O'.$row,'Nbre Boxes');
				$sheet->getStyle('P'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('P'.$row,'INDU');
				$sheet->getStyle('Q'.$row)->getAlignment()->setHorizontal('center');
				$sheet->setCellValue('Q'.$row,'EURO');

				$sheet->getStyle('A'.$row.':Q'.$row)
						->getAlignment()->setWrapText(true);

				// Freeze Rows Above (A3)
				$sheet->freezePane('D12');

				// Set Worksheet Name
				$sheet->setTitle($cliente[0]);

				$sheet->getStyle('O')
								  ->getNumberFormat()
								  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
				$sheet->getStyle('P')
								  ->getNumberFormat()
								  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
				$sheet->getStyle('Q')
								  ->getNumberFormat()
								  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

				$row++;
				$n=1;
				$numProdottiProduttoreI=$row;
				$numProdottiProduttoreF=$row;

				foreach( $listino as $prodotto )
				{
					if ($fornitoriAmmessi[strtoupper($cliente[0])][strtoupper($prodotto[2])] == 'SI') // il fornitore è ammesso
					{
						    if (strtoupper($prodotto[0]) != 'NO' && $prodotto[0] != NULL) 
							{  // se non è una riga esclusa o una riga vuota

								if ($DEBUG) {
										print_r($fornitoriAmmessi);
										echo "<br>------cliente:".$cliente[0]." prodott(ORE):".$prodotto[2]."....(".$prodotto[1].")-----".$fornitoriAmmessi[strtoupper($cliente[0])][strtoupper($prodotto[2])]."<br><br><br><br>";
								}

									  // if ( ($numProdottiProduttoreF>$numProdottiProduttoreI)) { // Non si tratta della prima intestazione e Inserisco la somma delle commande

								if ($prodotto[0] == '*') {   // Si tratta della riga intestazione località

									   $spreadsheet
													->getActiveSheet()
													->getStyle('A'.$row.':N'.$row)
													->getFill()
													->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
													->getStartColor()
													->setARGB('ffcc00');

									   $sheet->setCellValue('B'.$row, $prodotto[1]);   // Denominazione zona produttore
									   $sheet->setCellValue('A'.$row, $prodotto[0]);
										if ($DEBUG) {
															  echo 'Inserito INTESTAZIONE: '. $prodotto[1].'('.$prodotto[2].')<br>';
															  echo 'n: '.$n.'<br>';
															  echo 'row: '.$row.'<br>';
															  echo 'numProdottiProduttoreF:'.$numProdottiProduttoreF.'<br>';
													   }

									   $row++;
									   $numProdottiProduttoreI=$row;   // Si riparte con un nuovo produttore
									   $numProdottiProduttoreF=$row;
								} else if ((strtoupper($prodotto[0]) == 'FINE') && ($numProdottiProduttoreI!=$numProdottiProduttoreF)) 
								       {   //  Si sono finiti i prodotti e si inserisce la somma pedane
									  // $numProdottiProduttoreI++;
											$numProdottiProduttoreF--;
											$sheet->setCellValue('P'.$row, '=sum(P'.$numProdottiProduttoreI.':P'.$numProdottiProduttoreF.')');       // totale composizione pedana EURO
											$sheet->setCellValue('Q'.$row, '=sum(Q'.$numProdottiProduttoreI.':Q'.$numProdottiProduttoreF.')');     // totale composizione pedana INDU
											$sheet->setCellValue('O'.$row,'Total:');						
											$spreadsheet
													->getActiveSheet()
													->getStyle('O'.$row.':Q'.$row)
													->getFill()
													->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
													->getStartColor()
													->setARGB('E97A53');
											if ($DEBUG) {
												  echo 'Inserito SOMATORIE COLONNE CLIENTI: '. $prodotto[1].'('.$prodotto[2].')<br>';
												  echo 'n: '.$n.'<br>';
												  echo 'row: '.$row.'<br>';
												  echo 'numProdottiProduttoreF:'.$numProdottiProduttoreF.'<br>';
										    }
											$row=$row+1;
										} else {  // Si tratta di un prodotto da includere: produttore ammesso, prodotto disponibile
											 $sheet->setCellValue('A'.$row, $n);    // numero progressivo dei prodotti
											 $sheet->setCellValue('B'.$row, $prodotto[1]);    // prodotti
											 $sheet->setCellValue('C'.$row, $prodotto[2]);    // produttore
											 $sheet->setCellValue('D'.$row, $prodotto[3]);    // n° casse INDU
											 $sheet->setCellValue('E'.$row, $prodotto[4]);    // n° casse EURO
											 $sheet->setCellValue('F'.$row, $prodotto[5]);    // Kg/box
											 $sheet->setCellValue('G'.$row, $prodotto[6]);    // Kg/INDUpallet
											 $sheet->setCellValue('H'.$row, $prodotto[7]);    // Kg/EUROpallet
											 $sheet->getStyle('J')
															  ->getNumberFormat()
															  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
											  $sheet->getStyle('K')
															  ->getNumberFormat()
															  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
											if ( strtoupper($cliente[0]) == 'LANGRIDGE') {
												 $prezzoVenditaINDU = ($prodotto[11]+($cliente[3]/$prodotto[6]))*$cliente[5];
												 $prezzoVenditaEURO = ($prodotto[11]+($cliente[4]/$prodotto[7]))*$cliente[5];
												 $sheet->setCellValue('N'.$row, $prodotto[11]);
											} else {
												 $prezzoVenditaINDU = ($prodotto[9]+($cliente[3]/$prodotto[6]))*$cliente[5];
												 $prezzoVenditaEURO = ($prodotto[9]+($cliente[4]/$prodotto[7]))*$cliente[5];
												 $sheet->setCellValue('N'.$row, $prodotto[9]);

											}
											 $sheet->setCellValue('J'.$row, $prezzoVenditaINDU);    // PV + trasporto EURO
											 $sheet->setCellValue('K'.$row, $prezzoVenditaEURO);    // PV + trasporto INDU



											 $sheet->getStyle('L')
															  ->getNumberFormat()
															  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
											 $sheet->getStyle('M')
														  ->getNumberFormat()
														  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
											 $sheet->getStyle('N')
														  ->getNumberFormat()
														  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
											
											 $spreadsheet
														->getActiveSheet()
														->getStyle('L'.$row.':N'.$row)
														->getFill()
														->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
														->getStartColor()
														->setARGB('deb887');
                                             if ( strtoupper($cliente[0]) == 'LANGRIDGE') {
												 $prezzo_INDU = ($prodotto[11]+($cliente[3]/$prodotto[6]));
												 $prezzo_EURO = ($prodotto[11]+($cliente[4]/$prodotto[7]));
											 } else {
												 $prezzo_INDU = ($prodotto[9]+($cliente[3]/$prodotto[6]));
												 $prezzo_EURO = ($prodotto[9]+($cliente[4]/$prodotto[7]));
											 
											 }
											 $sheet->setCellValue('L'.$row, $prezzo_INDU);    // Prix  + trasporto INDU
											 $sheet->setCellValue('M'.$row, $prezzo_EURO);    // Prix + trasporto EURO
											
											 
											
											 // Spazio riservato agli ordini clienti
											 $spreadsheet
															->getActiveSheet()
															->getStyle('O'.$row.':O'.$row)
															->getFill()
															->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
															->getStartColor()
															->setARGB('F0E6E4');
															$sheet->setCellValue('P'.$row, '=O'.$row.'/D'.$row);
															$sheet->setCellValue('Q'.$row, '=O'.$row.'/E'.$row);


											  if ($DEBUG) {
														  echo 'Inserito nuovo prodotto: '. $prodotto[1].'('.$prodotto[2].')<br>';
														  echo 'n: '.$n.'<br>';
														  echo 'row: '.$row.'<br>';
														  echo 'numProdottiProduttoreF:'.$numProdottiProduttoreF.'<br>';
												   }

											 $n++; // numero progressivo prodotti
											 $row++;  // riga per nuovo prodotto
											 $numProdottiProduttoreF++;
																																																	  
										 }      // FINE INSERIMENTO PRODOTTO da produttore ammesso prodotto disponibile
						   } // riga NO
						   else {
								if ($DEBUG) {
									echo $prodotto[1].'('.$prodotto[2].'): prodotto non disponibile<br>';
								}
						   }
				    } //  Fornitore AMMESSO
				    else {
						if ($DEBUG) {
							   echo $cliente[0].': Il fornitore '.strtoupper($prodotto[2]).' NON è ammesso<br>';
					   }
				    }
				 } // END foreach su ogni riga del listino


				// Setting autosize columns
				for ($col='A';$col<'H';$col++) {
						$sheet->getColumnDimension($col)->setAutoSize(true);
				}

			  
		// Writing to file(s)  EXL
				// *****************************************************
		$writer = new Xlsx($spreadsheet);

		$writer->save($outputOfferClientMarge[strtoupper($cliente[0])]);


		if ( strtoupper($cliente[0]) == 'LANGRIDGE') { // Langridge exeption management
				 //	$sheet->removeColumn('N');
					// $sheet->removeColumn('O');
				 //	$sheet->removeColumn('P');
				   // $sheet->removeColumn('Q');
		}
	
		// Removing Price+Transport for users spreadsheet
		$sheet->removeColumn('L');
		$sheet->removeColumn('L');
		$sheet->removeColumn('L');
		$writer->save($outputOfferClient[strtoupper($cliente[0])]);

		$spreadsheet ->getDefaultStyle()->applyFromArray(
			[
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['rgb' => '000000'],
					],
				]
			]
		);

             
		// WIRITNG  pdf_add_annotation
		// ***********************************************
		// Per i file pdf elimino le colonne del cqlcolo pedane
		// TODO LG PDF File
		
		if ( strtoupper($cliente[0]) != 'LANGRIDGE') {
				$sheet->removeColumn('N');
				$sheet->removeColumn('O');
				$sheet->removeColumn('P');
		}

		//$xmlWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet,'Mpdf');
		//$xmlWriter->writeAllSheets();
		// $xmlWriter->setFooter("Sdfsdf");
		$num = rand(00, 99);
		//create folder named files
		//$xmlWriter->save($outputOfferClient[strtoupper($cliente[0])].".pdf");
                     
                

				// Elimino gli spreadsheet che non servono
				// ***********************************************

				unset ($xmlWriter);
				unset ($spreadsheet);
				unset ($sheet);


		} // else
   } // END for each customer
// } // LG OTTIMIZZAZIONE
   // echo  '<div id="coverScreen"  class="LockOn">END</div>';
echo '</div>   <!-- myTabContent --> ';

?>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-kQtW33rZJAHjgefvhyyzcGF3C5TFyBQBA13V1RKPf4uH+bwyzQxZ6CmMZHmNBEfJ" crossorigin="anonymous"></script>

	<script>
		window.onscroll = function() {myFunction()};

		var header = document.getElementById("listOfClient");
		var sticky = header.offsetTop;

		function myFunction() {
		  if (window.pageYOffset > sticky) {
			header.classList.add("sticky");
		  } else {
			header.classList.remove("sticky");
		  }
		}		
	</script>
	
	<script>
	   /*     $('coverScreen').on('DOMSubtreeModified', function(){
                 			$("#coverScreen").hide();
			} )
			
			$(window).on('load', function () {
			$("#coverScreen").hide();
			});
			$("#loadFilebtn").click(function () {
				$("#coverScreen").show();
			});
			*/
	</script>
	
	<script>
	    // per la barra di prorgessione
		document.onreadystatechange = function () {
            if (document.readyState === "complete") {
                console.log(document.readyState);
                document.getElementById("PreLoaderBar").style.display = "none";
            }
        }
	</script>

<?php


// print '</div><div class="fichetwothirdright">';
// ---------------------- END --------------------------------

$NBMAX = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;
$max = $conf->global->MAIN_SIZE_SHORTLIST_LIMIT;


print '</div></div>';

// End of page
llxFooter();
$db->close();
