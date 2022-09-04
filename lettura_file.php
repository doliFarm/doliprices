<html> 
<head>
				<!-- Required meta tags -->
           <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1"/>				
		   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
				<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
				<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

				<style>
						div.fixed {
						  position: fixed;
						  bottom: 0;
						  right: 0;
						  width: 300px;
						  border: 3px solid #73AD21;
						  background-color: #FFFFFF;
						}
				
				</style>
</head>
<body>

     <?php
	 
	 
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





// Load translation files required by the page
$langs->loadLangs(array("doliprices@doliprices"));
global $conf,  $mysoc;

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


        $DEBUG = 	FALSE   ;
        set_error_handler(function(int $number, string $message) {
			GLOBAL $DEBUG;
                if ($DEBUG) {
                        echo "Handler captured error $number: '$message'" . PHP_EOL  ;
                }
        });



        function cercaFile($chiaveFile, $percorso = "ordersConfirmedMarge") {
                global $DEBUG;
                if ($DEBUG) echo "Cerco file sul percorso $percorso <br>";

                $path = scandir($percorso, 1);
                $array = [];
                $trovato = FALSE;
                foreach ($path as $x) {
                        if ((strpos($x,  $chiaveFile) !== false)&& (strpos($x,  "marge") !== false) && (strpos($x,  ".pdf") == false)) {
                                return $percorso."/".$x;
                                $trovato = TRUE;
                        }
                }
                if(!$trovato) return NULL;
        }





        // iNPUT fILE
        // **************************************


        if (isset($_GET["k"])){
           $chiave = $_GET["k"];
        } else {
           echo "<p>No input file</p>";
           exit();
        }
         
		 if (isset($_GET["auto"])) {
			 $autoEmail = $_GET["auto"];
		 } else {
			 $autoEmail = 0;
		 }
		 
            if ($DEBUG) echo "ricervuta chiave $chiave <br>";

        $inputFileName = cercaFile($chiave,"offersMarge");
                if ($DEBUG) echo "$inputFileName <br>";

    if ($inputFileName   == NULL){
                echo "Error reading file";
                return;
        }


        // Reading the spreadsheet file
        // **************************************


        /**  Identify the type of $inputFileName  **/
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        /**  Load $inputFileName to a Spreadsheet Object  **/

        $spreadsheet = $reader->load($inputFileName);
        /**  Convert Spreadsheet Object to an Array for ease of use  **/
        $listino = $spreadsheet->getActiveSheet()->toArray();

        $client = $spreadsheet->getSheetNames()[0];

?>


<div class="card mb-4" style="max-width: 540px;">
  <div class="row g-0">
    <div class="col-md-4">
      <?php echo $mysoc->logo_small; ?>
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title"><?php echo $mysoc->name; ?></h5>
                <p class="card-text">
						<small class="text-muted">    
								<?php echo $mysoc->getFullAddress(); ?>
                        </small>
				</p>
        <p class="card-text"></p>
      </div>
    </div>
  </div>
</div>

<div class="alert alert-light text-end" role="alert">
    <?php
	        $contattoCliente = $spreadsheet->getActiveSheet()->getCell('E3')->getValue();
			$emailContattoCliente =  $spreadsheet->getActiveSheet()->getCell('E4')->getValue();
			$info = explode('*',$spreadsheet->getActiveSheet()->getCell('B9')->getValue() );
		    $subject =  $info[0];
			$week = $info[1];
			$dateWeek = $info[2];
           echo "<h3>".$client."</h3>";
		   echo "$contattoCliente ($emailContattoCliente)<br>";
           echo $subject.' '.$week.' '.$dateWeek;
         ?>
</div>



<div id="TotalPallet" class="fixed">
     
</div>


<?php

        // Parsing PRICES LIST
        // ***************************************
        echo '<div id="priceslist">';
		 //  echo '<button id="btn_showColsON" class="btn btn-secondary">showColumn</button>';
		 //  echo '<button id="btn_showColsOFF" class="btn btn-secondary">hideColumn</button>';

        echo '<form id="formListino" action="'. $_SERVER['PHP_SELF'].'/confermaOrdine.php" method="POST" >';
		echo '<input  type="hidden" id="settimana" name="settimana" value="'.$week.'">';
		echo '<input  type="hidden" id="k" name="k" value="'.$chiave.'">';
		echo '<input  type="hidden" id="client" name="client" value="'.$client.'">';
		echo '<input  type="hidden" id="contattoCliente" name="contattoCliente" value="'.$contattoCliente.'">';
		echo '<input  type="hidden" id="emailContattoCliente" name="emailContattoCliente" value="'.$emailContattoCliente.'">';
		echo '<input  type="hidden" id="totINDUpallet" name="totINDUpallet" value="">';
		echo '<input  type="hidden" id="totEUROpallet" name="totEUROpallet" value="">';
		echo '<input  type="hidden" id="autoEmail" name="autoEmail" value="'.$autoEmail.'">';
		echo '<input type="hidden" name="token" value="'.newToken().'">';


        echo '<table id="tablePrices" class="table  table-striped">';
        $h=0;
		$n = 1;
        foreach( $listino as $prodotto )
        {
            if ($DEBUG) print_r($prodotto);
			if ( strcmp($prodotto[14],"Total:") == 0 ) {
				
									echo '<tr><td colspan="10"> </td>';
									echo '<td class="text-end">Total %:</td>';

									echo '<td> <input class="form-control-plaintext input-mini" readonly type="text" id="sommaINDU_'.$produttore.'" name="sommaINDU_'.$produttore.'" value=""></td>';
									echo '<td><input class="form-control-plaintext input-mini" readonly type="text" id="sommaEURO_'.$produttore.'" name="sommaEURO_'.$produttore.'" value=""></td>';
									echo '</tr>';
			}
            if ($prodotto[0]!=NULL) {  // Avoid empty rows		                        
                                if ( $prodotto[0]== '*') {
                                   $h++;
                                   if ( $h>1) { // Non è la prima intestazione
								   /*
                                                echo '<tr><td colspan="10"> </td>';
                                                echo '<td class="text-end">Total(%):</td>';

                                                echo '<td> <input class="form-control-plaintext input-mini" readonly type="text" id="sommaINDU_'.$produttore.'" name="sommaINDU_'.$produttore.'" value=""></td>';
                                                echo '<td><input class="form-control-plaintext input-mini" readonly type="text" id="sommaEURO_'.$produttore.'" name="sommaEURO_'.$produttore.'" value=""></td>';
                                                echo '</tr>';
												*/
                                   }
                                        // Intestazione
                                        echo '<tr><td colspan="13"><h4 class="alert alert-light" role="alert"> '.$prodotto[1].'</h4></td>';
                                        echo '</tr>';
                                        echo '<tr>';
										echo '<th> </th>';
										echo '<th>Produits </th>';
										echo '<th>Producteur </th>';
										
										echo '<th data-field="nCasseIndu">N° de caisses INDU</th>';
										echo '<th data-field="nCasseEuro">N° de caisses EURO</th>';
										echo '<th data-field="kgBox">Kg/Box </th>';
										echo '<th data-field="kgIndu">Kg /INDU pallet</th>';
										echo '<th data-field="kgEuro">Kg /EURO pallet </th>';
										// echo '<th></th>';
										echo '<th>PV  transp. incl. INDU pallet</th>';
										echo '<th>PV  transp. incl. EURO pallet</th>';
										// echo '<th></th>';
										echo '<th>Nbre Boxes</th>';
										echo '<th>INDU %</th>';
										echo '<th>EURO %</th>';
										echo '</tr>';

                                } else {
										$produttore = $prodotto[2];
										print '<tr>';
										print '<td> <input  class="form-control-plaintext" readonly type="text" id="num_'.$prodotto[0].'_'.$produttore.'" name="num_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[0] . '"></td>';
										print '<td> <input  class="form-control-plaintext " readonly type="textarea" wrap="hard"  size="1000" id="prodotto_'.$prodotto[0].'_'.$produttore.'" name="prodotto_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[1] . '">
										        
											   </td>';										
										print '<td> <input  class="form-control-plaintext"  readonly type="text" id="produttore_'.$prodotto[0].'_'.$produttore.'" name="produttore_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[2] . '"></td>';
										
										echo '<td> <input class="form-control-plaintext"  readonly type="text" id="nCasseIndu_'.$prodotto[0].'_'.$produttore.'" name="nCasseIndu_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[3] . '"></td>';
										echo '<td> <input  class="form-control-plaintext"  readonly type="text" id="nCasseEuro_'.$prodotto[0].'_'.$produttore.'" name="nCasseEuro_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[4] . '"></td>';
										echo '<td> <input  class="form-control-plaintext"  readonly type="text" id="kgBox_'.$prodotto[0].'_'.$produttore.'" name="kgBox_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[5] . '"></td>';
										echo '<td> <input  class="form-control-plaintext"  readonly type="text" id="kgIndu_'.$prodotto[0].'_'.$produttore.'" name="kgIndu_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[6] . '"></td>';
										echo '<td> <input  class="form-control-plaintext"  readonly type="text" id="kgEuro_'.$prodotto[0].'_'.$produttore.'" name="kgEuro_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[7] . '"></td>';
								//      echo '<td> <input class="form-control-plaintext"  readonly type="text" id="" name="" ' . $prodotto[8] . '></td>';
										echo '<td> <input  class="form-control-plaintext"  readonly type="text" id="PvIndu_'.$prodotto[0].'_'.$produttore.'" name="PvIndu_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[9] . '"></td>';
										echo '<td> <input class="form-control-plaintext"  readonly type="text" id="PvEuro_'.$prodotto[0].'_'.$produttore.'" name="PvEuro_'.$prodotto[0].'_'.$produttore.'" value="' . $prodotto[10] . '"></td>';
								//      echo '<td> <input class="form-control-plaintext"  readonly type="text" id="" name="" ' . $prodotto[11] . '></td>';
										if ( ($prodotto[0]!='*') ) { // E' una riga di intestazione successiva alla prima
												echo '<td><input  tabindex='.$n.' class="" type="textarea" size="2px" '.$prodotto[0].'" id="qta_'.$prodotto[0].'_'.$produttore.'" name="qta_'.$prodotto[0].'_'.$produttore.'" value="" onchange="calcoloPedane(this.id, this.value)"></td>';
										} else {
												echo '<td> </td>';
										}
										echo '<td>  <input   class="form-control-plaintext"  readonly type="text" id="numINDU_'.$prodotto[0].'_'.$produttore.'" name="numINDU_'.$prodotto[0].'_'.$produttore.'" value=""></td>';
										echo '<td>  <input class="form-control-plaintext"  readonly type="text" id="numEURO_'.$prodotto[0].'_'.$produttore.'" name="numEURO_'.$prodotto[0].'_'.$produttore.'" value=""></td>';
										echo '</tr>'; 
									$n++;
							   }

           }
        }


   
        echo '</table>';
        echo '</div>  <!-- listino -->';
        echo '<div class="text-center">
                                   <button tabindex='.$n.' type="submit" id="send"  name="send" value="send"  class="btn btn-primary btn-lg">Send</button>
								   <!--
                                   <button type="submit" id="sendPDF"  name="send" value="sendPDF" class="btn btn-link"> <img src="listini/imgs/pdf.svg" width="30px" heigth="30px"> </button>
                                   <button type="submit"  id="sendXLS"  name="send" value="sendXLS" class="btn btn-link"> <img src="listini/imgs/xlsx.png"  width="30px" heigth="30px" target="_new"> </button> -->

              </div>';
        echo '</form>';

                 



?>



<!-- ************************************* NEWS SECTION ******************************************* -->

<hr>
<?php
	echo '<h5 class="text-center">Les nouvelles de nos producteurs. </h5>';

	require_once 'news/letturanews.php';
	letturaNews($week);
?>

<!-- ************************************* SCRIPT ******************************************* -->

<script>
	 var $table = $('#tablePrices')
	 var $button = $('#btn_showColsON')
	 var $button2 = $('#btn_showColsOFF')
	 
	$(function() {
			$button.click(function () {
			  $table.bootstrapTable('showColumn', 'nCasseIndu')
			})
			$button2.click(function () {
			  $table.bootstrapTable('hideColumn', 'nCasseIndu')
			})
		  })
</script>

<script>

	

		function calcoloPedane(id,val) {
		   // id is the

		   const line = id.substring(id.indexOf('_') + 1);
		   const rifProduttore = line.split('_').pop();
		   const rifProdotto = line.split('_').shift();

		   //  alert(rifProdotto);
		   // alert (rifProduttore);
		   document.getElementById("numEURO_"+line).value = ((document.getElementById(id).value/document.getElementById("nCasseEuro_"+line).value)*100).toFixed(2);
		   document.getElementById("numINDU_"+line).value = ((document.getElementById(id).value/document.getElementById("nCasseIndu_"+line).value)*100).toFixed(2);

		   var elements = document.forms["formListino"].elements;
		   document.getElementById("sommaEURO_"+rifProduttore).value = 0;
		   document.getElementById("sommaINDU_"+rifProduttore).value = 0;



				for (i=0; i<elements.length; i++){
						if ( elements[i].name.includes(rifProduttore)&& elements[i].name.includes("numEURO_") ) {
										// alert(elements[i].value);
										/*
										valore = elements[i].value.replace('%','');
										document.getElementById("sommaEURO_"+rifProduttore).value.replace('%','');
										*/
										document.getElementById("sommaEURO_"+rifProduttore).value = Number(document.getElementById("sommaEURO_"+rifProduttore).value) + Number(elements[i].value);
										/*
										document.getElementById("sommaEURO_"+rifProduttore).value = document.getElementById("sommaEURO_"+rifProduttore).value + "%";
										*/
						} else
						if ( elements[i].name.includes(rifProduttore)&& elements[i].name.includes("numINDU_") ) {
										/*
										valore = elements[i].value.replace('%','');
										document.getElementById("sommaINDU_"+rifProduttore).value.replace('%','');
										*/
										document.getElementById("sommaINDU_"+rifProduttore).value = Number(document.getElementById("sommaINDU_"+rifProduttore).value) + Number(elements[i].value);
										/*
										document.getElementById("sommaINDU_"+rifProduttore).value = document.getElementById("sommaINDU_"+rifProduttore).value + "%";
										*/
						}
				}
				
				// Aggiorno totale generale
				var TotINDUids = document.querySelectorAll('[id^="sommaINDU_"]');
				var TotINDU = 0;
				for (var i=0;i<TotINDUids.length;i++) {
					if ( !isNaN(parseFloat(TotINDUids[i].value)) ){
						TotINDU = TotINDU + parseFloat(TotINDUids[i].value);
					}
				}
				
				var TotEUROids = document.querySelectorAll('[id^="sommaEURO_"]');
				var TotEURO = 0;
				for (var i=0;i<TotEUROids.length;i++) {
					if ( !isNaN(parseFloat(TotEUROids[i].value)) ){
						TotEURO = TotEURO + parseFloat(TotEUROids[i].value);
					}
				}
				if ( (TotEURO > 0) || (TotINDUids >0 ) ) {
				     document.getElementById("TotalPallet").innerHTML = 'Tot. IND: '+ Number((TotINDU/100).toFixed(2)) +' - Tot. EURO: '+ Number((TotEURO/100).toFixed(2));		
				     document.getElementById("totINDUpallet").setAttribute('value',Number((TotINDU/100).toFixed(2)));
					 document.getElementById("totEUROpallet").setAttribute('value',Number((TotEURO/100).toFixed(2)));
                } else {
					document.getElementById("TotalPallet").innerHTML = '';				 
				}
		}
		
		// Make the ENTER Key behave like TAB
					$('form input:not([type="submit"])').keydown(function(e) {
					if (e.keyCode == 13) {
						var inputs = $(this).parents("form").eq(0).find(":input");
						if (inputs[inputs.index(this) + 1] != null) {                    
							inputs[inputs.index(this) + 1].focus();
						}
						e.preventDefault();
						return false;
					}
				});

	</script>	

</body>
</html>