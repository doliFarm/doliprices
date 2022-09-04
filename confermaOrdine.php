<html>
<head>

				<!-- Required meta tags -->
				<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		   <link rel="stylesheet" href="style.css">
		   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
				<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
				<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>
</head>
<body>


<?php

// Load Dolibarr environment$res = 0;// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";}// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME']; $tmp2 = realpath(__FILE__); $i = strlen($tmp) - 1; $j = strlen($tmp2) - 1;while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {	$i--; $j--;}if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1))."/main.inc.php")) {	$res = @include substr($tmp, 0, ($i + 1))."/main.inc.php";}if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php")) {	$res = @include dirname(substr($tmp, 0, ($i + 1)))."/main.inc.php";}// Try main.inc.php using relative pathif (!$res && file_exists("../main.inc.php")) {	$res = @include "../main.inc.php";}if (!$res && file_exists("../../main.inc.php")) {	$res = @include "../../main.inc.php";}if (!$res && file_exists("../../../main.inc.php")) {	$res = @include "../../../main.inc.php";}if (!$res) {	die("Include of main fails");}require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';// Load translation files required by the page$langs->loadLangs(array("doliprices@doliprices"));global $conf, $mysoc;$action = GETPOST('action', 'aZ09');// Security check// if (! $user->rights->doliprices->myobject->read) {// 	accessforbidden();// }$socid = GETPOST('socid', 'int');if (isset($user->socid) && $user->socid > 0) {	$action = '';	$socid = $user->socid;}$max = 5;$now = dol_now();
	    require 'listini/vendor/autoload.php';
		require 'listini/vendor/PHPMailer-5.2-stable/PHPMailerAutoload.php';

        use PhpOffice\PhpSpreadsheet\Spreadsheet;
        use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

        use PhpOffice\PhpSpreadsheet\IOFactory;
        use PhpOffice\PhpSpreadsheet\Style\Fill;
        use PhpOffice\PhpSpreadsheet\Style\Border;

        $DEBUG = FALSE   ;
		if ($DEBUG) {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}
		
        set_error_handler(function(int $number, string $message) {
                GLOBAL $DEBUG;
				if ($DEBUG) {
                        echo "Handler captured error $number: '$message'" . PHP_EOL."<br>"  ;
                }
        });



        function cercaFile($chiaveFile, $percorso = "offers") {
                global $DEBUG;
                if ($DEBUG) echo "Cerco file sul percorso $percorso il file $chiaveFile<br>";

                $path = scandir($percorso, 1);
                $array = [];
                $trovato = FALSE;
                foreach ($path as $x) {
                        if ((strpos($x,  $chiaveFile) !== false) && (strpos($x,  ".pdf") == false)) {
                                return $x;
                                $trovato = TRUE;
                        }
                }
                if(!$trovato) return NULL;

        }
		
		
		function aggiornamento_foglio($listinoMargineSheet,$listinoMargineSheetArray,$client,$numeroProdotto,$quantitaProdotto) {
			
			GLOBAL $DEBUG;
			$row=1; // TODO PUNTO CRITICO!!!!!
			if ($DEBUG) {
				echo "sto cercando $numeroProdotto perchè mi hanno dato questa quantità $quantitaProdotto per il cliente $client<br>. Aggiorno la riga $row";
			}
			
			foreach( $listinoMargineSheetArray as $prodotto )
                        {  
						   if ($prodotto[0] == $numeroProdotto) {  // Aggiorno la quantità 
						   	    if ($DEBUG) echo "TROVATO numero prodotto $prodotto[0]---------------------------------<br>";	
								$listinoMargineSheet->setCellValue('O'.$row, $quantitaProdotto);    
								if ($DEBUG) echo "AGGIORNATO CAMPO $prodotto[0] sulla riga del foglio $row";
						   }
						   $row++;
						}
						
		}
		
		// TODO Da organizzare in una libreria. 
		function spedisciEmail($destinatario,$subject,$messaggio,$fileAttached=NULL) {
	            global $DEBUG, $conf;
					// Pear Mail Library
					// require_once "Mail.php";
				if ($DEBUG) echo "<br>Funzione spedisciEmail() preparo email";
				$from = $conf->global->DOLIPRICES_FROMMAIL; //change this to your email address
				$to =   explode(",",$destinatario); // change to address
				$subject = $subject; // subject of mail
				$body = $messaggio;
				
				$username=$conf->global->DOLIPRICES_FROMMAIL;
				$password=$conf->global->DOLIPRICES_PWDMAIL;
				// $mail = new PHPMailer(true);
				
				$mail = new PHPMailer;
				$mail->CharSet = 'UTF-8';
				$mail->Encoding = 'base64';
				//$mail->SMTPDebug = 3;                               // Enable verbose debug output

				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = 'ssl0.ovh.net';  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = $conf->global->DOLIPRICES_FROMMAIL;                 // SMTP username
				$mail->Password = $conf->global->DOLIPRICES_PWDMAIL;                           // SMTP password
				$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
				$mail->Port = 465;                                    // TCP port to connect to

				$mail->setFrom($conf->global->DOLIPRICES_FROMMAIL, 'Name');
				
				
				for ($i=0;$i<count($to);$i++) {
					$mail->addAddress(str_replace(' ', '',$to[$i]));     // Add a recipient
				}
				$mail->addCC($conf->global->DOLIPRICES_CCMAIL);
				// $mail->addAddress('user@none.com');               // Name is optional
				// $mail->addReplyTo('info@example.com', 'Information');
				// $mail->addCC('cc@example.com');
				// $mail->addBCC('bcc@example.com');

				$mail->addAttachment($fileAttached);         // Add attachments
				// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
				$mail->isHTML(true);                                  // Set email format to HTML

				$mail->Subject = $subject;
				$mail->Body    = $body;
				$mail->AltBody = $body;

				if(!$mail->send()) {
					echo 'Message could not be sent.';
					echo 'Mailer Error: ' . $mail->ErrorInfo;
				} else {
					if ($DEBUG) echo '<br>[spedisciEmail] Message has been sent<br>';
				}
					
		}

      

        if (isset($_POST["k"])){
           $chiave = $_POST["k"];
        } else {
           echo "<p>No input file</p>";
           exit();
        }
?>

<!-- *************************** HEADER ************************************** -->
<div class="card mb-4" style="max-width: 540px;">
  <div class="row g-0">
    <div class="col-md-4">
      <?php echo $mysoc->logo_small; ?>
    </div>
    <div class="col-md-8">
      <div class="card-body">
        <h5 class="card-title"><?php echo $mysoc->name; ?></h5>
                <p class="card-text">
					<small class="text-muted"><?php echo $mysoc->getFullAddress(); ?>
					</small>
				</p>
        <p class="card-text"></p>
      </div>
    </div>
  </div>
</div>




<?php

	if ($DEBUG) print_r($_POST);
	
	// Setting variables
	$ddate = date('Y/m/d');
	$edate = strtotime($ddate."+ 5 days");
	$date = new DateTime($ddate);

	$week = $_POST["settimana"];
	$k = $_POST["k"];
	$client = $_POST["client"];
	$send = $_POST["send"];
    $emailContattoCliente = $_POST["emailContattoCliente"];
	$ContattoCliente = $_POST["contattoCliente"];
	$totINDUpallet = $_POST["totINDUpallet"];
	$totEUROpallet = $_POST["totEUROpallet"];
	$autoEmail = $_POST["autoEmail"];
	// unsetting hidden or unuseful variables 
	unset($_POST["emailContattoCliente"]);
	unset($_POST["contattoCliente"]);
	unset($_POST["settimana"]);
	unset($_POST["k"]);
	unset($_POST["client"]);
	unset($_POST["send"]);
	unset($_POST["totINDUpallet"]);
	unset($_POST["totEUROpallet"]);
	unset($_POST["autoEmail"]);


	foreach($_POST as $x => $x_value) {
		if (strpos($x, 'somma') !== false) {
		   unset($_POST[$x]);
		}
	}	
    $inputFileNameMargine = cercaFile($chiave.'_marge',"offers/");
	// TODO CERCA ERRORI
	
	$outputFileNameMargineConfirmed = DOL_DOCUMENT_ROOT."/custom/doliprice/ordersConfirmedMarge/".$inputFileNameMargine;	
	$outputOrderConfirmed = DOL_DOCUMENT_ROOT."/custom/doliprice/ordersConfirmed/".$week."-".$client."-".$k."-Confirmed-.xlsx";
	$inputFileNameMargine =  DOL_DOCUMENT_ROOT."/custom/doliprice/offers/".$inputFileNameMargine;
	
	if ($DEBUG) {
		echo "<br>";
		echo "File inputFileNameMargine---------------------------------------->$inputFileNameMargine--<br>";
		echo "File outputFileNameMargineConfirmed--------------------------->$outputFileNameMargineConfirmed--<br>";
		echo "File outputOrderConfirmed---------------------------------------->$outputOrderConfirmed--<br>";
		echo "<br>";
	}
	

		
	    // Reading the spreadsheet file with Marge
        // ****************************************


        /**  Identify the type of $inputFileName  **/
        //$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileNameMargine);

        /**  Create a new Reader of the type that has been identified  **/
        // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
        /**  Load $inputFileName to a Spreadsheet Object  **/
        // $reader->load($inputFileNameMargine);
		
		
        $listinoMargine = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileNameMargine);
		$maxCell = $listinoMargine->getSheetByName($client)->getHighestRowAndColumn();
        $listinoMargineSheetArray = $listinoMargine->getSheetByName($client)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);
		$listinoMargineSheet = $listinoMargine->getSheetByName($client);
        /**  Convert Spreadsheet Object to an Array for ease of use  **/
        // $listinoMargineSheet = $listinoMargine->getActiveSheet()->toArray();

		
	// Creating spreadsheet of confirmation
	// *************************************************************


        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

         // Insert LOGO
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setName('Paid');
                        $drawing->setDescription('Paid');
                        $drawing->setPath('listini/imgs/logo.jpg'); // put your path and image here
                        $drawing->setCoordinates('B1');
                        $drawing->setOffsetX(0);
                        $drawing->setRotation(0);
                        $drawing->getShadow()->setVisible(true);
                        $drawing->getShadow()->setDirection(45);
                        $drawing->setWorksheet($spreadsheet->getActiveSheet());

        // Insert Week of the year
                        $sheet->getStyle('B9')->getAlignment()->setHorizontal('center');
                        $sheet->getStyle('B9')->getFont()->setBold( true );
                        $sheet->setCellValue('B9', 'Liste des prix / Prices List week: *'.$week.'* ('.$date->setISODate(date("Y"), $week, "1")->format('d/m/Y')." - ".$date->setISODate(date("Y"), $week, "7")->format('d/m/Y').')');

                        $row = 10;   // Starting row

        // Insert Head

 
                         $spreadsheet
                                                ->getActiveSheet()
                                                ->getStyle('J'.$row.':O'.$row)
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
                                                ->getStyle('J'.$row.':O'.$row)
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

                      /*
                        $sheet->getStyle('L'.$row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValue('L'.$row,'Prix + transp. INDU pallet');
                        $sheet->getStyle('M'.$row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValue('M'.$row,'Prix + transp. EURO pallet');
                     */

                        $sheet->getStyle('M'.$row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValue('M'.$row,'Nbre Boxes');
                        $sheet->getStyle('N'.$row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValue('N'.$row,'EURO Pallet');
                        $sheet->getStyle('O'.$row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValue('O'.$row,'INDU Pallet');

                        $sheet->getStyle('A'.$row.':Q'.$row)
                                ->getAlignment()->setWrapText(true);

                        // Freeze Rows Above (A3)
                        $sheet->freezePane('D12');

                        // Set Worksheet Name
                        $sheet->setTitle($client);

                        $sheet->getStyle('M')
                                          ->getNumberFormat()
                                          ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER);
										  
                        $sheet->getStyle('N')
                                          ->getNumberFormat()
                                          ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                        $sheet->getStyle('O')
                                          ->getNumberFormat()
                                          ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);


			/* Filling up the spreadsheet with the products 
			******************************************************************** */


				
			$nfields = 13;        //TODO CRITICO DIPENDE DAL FOGLIO EXCEL
			$num = count($_POST)/$nfields;   // numero di prodotti

			$chiavi = array_keys($_POST);

			$row++;
			$n=1;
			$numProdottiProduttoreI=$row;
			$numProdottiProduttoreF=$row;
			$r=0;
			for ($i=0;$i<$num;$i++) {    // Per ogni prodotto
				        $listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
				        $sheet->setCellValue('A'.$row,$listino[$i][$chiavi[$r]]);
						$numeroProdotto = $listino[$i][$chiavi[$r]];
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
				        $sheet->setCellValue('B'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('C'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('D'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('E'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('F'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('G'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('H'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('J'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('K'.$row,$listino[$i][$chiavi[$r]]);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
				        $sheet->setCellValue('M'.$row,$listino[$i][$chiavi[$r]]);
						$quantitaProdotto = $listino[$i][$chiavi[$r]];
						$r++;
						$spreadsheet
								->getActiveSheet()
								->getStyle('M'.$row)
								->getFill()
								->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
								->getStartColor()
								->setARGB('E2E0E0');
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('N'.$row,($listino[$i][$chiavi[$r]])/100);
						$r++;
						$listino[$i][$chiavi[$r]] = $_POST[$chiavi[$r]];
                        $sheet->setCellValue('O'.$row,($listino[$i][$chiavi[$r]])/100);
				        $r++;
					   $row++;
					   // DEBUG  print_r($listino[$i]);
					   
					   
					   // Aggiorno quantità ordine su DB TODO
					   //     $listino[$i][$chiavi[$r-13]]
					   //     $listino[$i][$chiavi[$r-3]]
					   //     $week
					   //     $key
					   //     $client
					   if ($quantitaProdotto>0) aggiornamento_foglio($listinoMargineSheet,$listinoMargineSheetArray,$client,$numeroProdotto,$quantitaProdotto);
			} // FINE FOR elenco prodotti
			
			// Inserimento somme totali pallet
			$listinoMargineSheet->setCellValue('Q7',"Tot. EURO Pallet");
			$listinoMargineSheet->setCellValue('Q8',$totEUROpallet);
			$listinoMargineSheet->setCellValue('P7',"Tot. INDU Pallet");
		    $listinoMargineSheet->setCellValue('P8',$totINDUpallet);
			
			$listinoMargineSheet->getStyle('P8')
					->getNumberFormat()
					->setFormatCode(
						\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
					);
			$listinoMargineSheet->getStyle('Q8')
					->getNumberFormat()
					->setFormatCode(
						\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00
					);
			
			
			// Setting autosize columns
			 for ($col='A';$col<'O';$col++) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			 }
			 
            // Aggiornamento file Margine

			    $writerMargine = new Xlsx($listinoMargine);
			 
				$writerMargine = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($listinoMargine, "Xlsx");
                $writerMargine->save(__DIR__.$outputFileNameMargineConfirmed);
        
			 // Utilizzo il listino margine per generare la conferma listino cliente
				 $listinoMargineSheet->removeColumn('L');
				 $listinoMargineSheet->removeColumn('L');
				 $listinoMargineSheet->removeColumn('L');

			 
			// Writing to file(s)  EXL
            // *****************************************************
               $writer = new Xlsx($listinoMargine);
			   $writer->save(__DIR__.$outputOrderConfirmed);

			// WIRITNG  pdf_add_annotation
            // ***********************************************
				$xmlWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($listinoMargine,'Mpdf');
				$xmlWriter->writeAllSheets();
				$xmlWriter->save(__DIR__.$outputOrderConfirmed.".pdf");



/******** OUTPUT THE RESULTS
*********************************************************/

			echo '<div class="alert alert-light text-end" role="alert">';
			echo "<h3>".$client."</h3>";
			echo "Prices List. Week:".' '.$week.' ('. $date->setISODate(date("Y"), $week, "1")->format('d/m/Y')." - ".$date->setISODate(date("Y"), $week, "7")->format('d/m/Y').")";
			echo '</div>';
			
			
		
			// Spedizione email di avviso ricezione nuovo ordine 
            if ($autoEmail == 0 ) {
				$messaggio = "<html><body> Ricevuto ordine</body></html> "; //content of mail
				$subject ="Ricevuto nuovo ordine $week ** $client **";
			} elseif ($autoEmail == 1 ) {
				$messaggio = "<html><body> Il nuovo ordine è stato caricato: ".DOL_URL_ROOT."$outputFileNameMargineConfirmed<br>Se il link non funziona fare copia&incolla con il tasto destro del mouse:-P<br><br> NB:L'ordine è pronto per essere elaborato su Dolibarr</body></html> "; //content of mail
				$subject ="Caricato nuovo ordine $week ** $client **";
			}
			              if ($DEBUG) echo "spedisco......<br>a:..... <br>$subject<br>$messaggio<br>allego: $outputFileNameMargineConfirmed";

			              if ($DEBUG) echo "<br>spedito......<br>";
						  
			// Spedizione email di conferma al cliente
			// $messaggio="Merci pour votre commande. Il s'agit d'un courriel généré automatiquement. N'hésitez pas à nous contacter pour toute information nécessaire.";             $messaggio=$conf->global->DOLIPRICE_MSGCONFIRMORDER;
			
	        // $subject ="Confirmation de la commande week $week ** $client **";			$subject = str_replace('[week]', $week, str_replace('[client]',$client,$conf->global->DOLIPRICE_OBJCONFIRMORDER));
			               if ($DEBUG) echo "spedisco......<br>a: $emailContattoCliente <br>$subject<br>$messaggio<br>allego: $outputOrderConfirmed";
			
			if ($autoEmail == 0 ) {
			   spedisciEmail($emailContattoCliente,$subject,$messaggio,__DIR__.$outputOrderConfirmed);
			   echo '<h4 class="text-center">Merci, votre commande a été envoyée avec succès! Vous recevrez un courriel de confirmation sur le'.$emailContattoCliente.'</h4>';

			} elseif ($autoEmail == 1 ) {
			   echo '<h4>Il nuovo ordine è pronto per essere caricato su <a href="'.DOL_DOCUMENT_URL.'/custom/orderimport/orderimportindex.php" target=new>Dolibarr</a></h4>';
			}


?>




<p class="text-center">
            Vous pouvez télécharger votre commande ici:
			  <a class="btn btn-link" role="button" href="<?php echo $outputOrderConfirmed; ?>"  target="_new"> <img src="imgs/xlsx.png" width="30px" heigth="30px"> </a>
			  <a class="btn btn-link" role="button" href="<?php echo $outputOrderConfirmed; ?>.pdf" target="_new"> <img src="imgs/pdf.svg"  width="30px" heigth="30px" target="_new"> </a>
</p>

<hr>
<?php

echo '<h5 class="text-center">Veuillez prendre quelques minutes pour consulter les nouvelles de nos producteurs ci-dessous. </h5>';

	require_once 'news/letturanews.php';
	letturaNews($week);
	
?>
