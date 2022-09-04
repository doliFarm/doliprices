<?php
/* Procedure Name: Price List generator
*  Description: take in input an Excel file in a specific format and generate lists prices for the customer
*  Author: Luigi Grillo @ Mandala Organic Growers
*  Ver: 1.0
*  Date: Sept, 2021
*  TODO:
*
*
* 
*
*/
/*

set_error_handler(function(int $number, string $message) {
   echo "Handler captured error $number: '$message'" . PHP_EOL  ;
});
*/

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

$DEBUG = TRUE;

$numCLIENTI = 0;
$numProduttori = 5;

// Setting the Week number of the year
//*************************************
$ddate = date('Y/m/d');
$edate = strtotime($ddate."+ 5 days");
$date = new DateTime($ddate);
$week = $date->format("W");


// Setting HTML Header
// ************************************
?>
<html> 
	<head> 
		<title>Prices List generator - Mandala, sept 2021</title> 
		    <!-- Required meta tags -->
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

           <link rel="stylesheet" href="style.css">
           <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
			<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-W8fXfP3gkOKtndU4JGtKDvXbO53Wy8SZCQHczT5FMiiqmQfUpWbYdTil/SxwZgAN" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>         <script>
         </script>
   
    </head>
    <body>
               
<?php

// Setting HTML Page
// ***********************************
echo '<div id="container">';

echo '<div id="header">';
echo '
	<div class="card">
		<div class="card-img-top d-flex align-items-center bg-light">
			<div>
				<img class="img-fluid" src="imgs/logo.jpg" alt="Card image cap">
			</div>
			<h5 class="card-title">Génération automatique d\'offres clients</h5>
		 </div>

		<div class="card-body">
			<p class="card-text">
					  <a class="btn btn-primary" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
						Instructions
					  </a>
					 <div class="collapse" id="collapseExample">
							<div class="card card-body">	
							     Outil pour la génération automatique d\'offres clients à partir d\'une liste de prix au format suivant :
								 <p><em>oui/no - Produits - Producteur - N° de caisses INDU - N° de caisses EURO - Kg/Box - Kg/INDUpallet - Kg/EUROpallet - PV Producteur - PV Producteur (LANGRIDGE)</em></p>
								 <div class="alert alert-secondary" role="alert">
										Exemple :
										<table>
											<tr>
											<td>*</td>   <td>Sicile Côte Sud-EST </td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
											</tr>
											<tr>
											<td>si</td><td>  Poivron Rouge / Red Pepper</td><td>	Bionatura</td><td>	110</td><td>	88</td>	<td>5</td><td>	550</td><td>	440</td><td>2.74 €</td>		<td>2.96 €</td> 
											</tr>
											<tr>
											<td>no  </td><td>Tomate Ovale / Plum tomatoes</td><td> 	Bionatura</td>	<td>140</td>	<td>112v	<td>6</td>	<td>840</td>	<td>672v		<td>2.10 €</td>		<td>2.25 € </td>
											</tr>
										</table>
										Note: l\'étoile indique une zone de production. "si" signifie que le produit est disponible. "No" signifie que le produit n\'est pas disponible. 
								</div>
								La liste des prix ainsi constituée est conservée sur une feuille Excel (GDrive). Le même fichier Excel doit comporter une feuille contenant une liste de clients  au format suivant :
								<p><em>Client - Prix Transport INDU - Prix Transport EURO - Marge</em></p>
								Les étapes pour générer des offres clients sont donc les suivantes : 
								    <ol>
										<li>Sur Google Drive, mettez à jour la liste des prix des Producteurs (Produits Frais).</li>
										<li>Téléchargez sur votre ordinateur le fichier Excel.</li>
										<li>Le télécharger sur cette page pour générer des offres pour les clients.</li>
									</ol>
						   </div>
					 </div>
			</p>
		</div>
	</div>
		<p class="col p-2 m-0"><h4 class="text-end">Numéro de semaine:'. $week .' ('. $date->setISODate(date("Y"), $week, "1")->format('d/m/Y')." - ".$date->setISODate(date("Y"), $week, "7")->format('d/m/Y').')</h4></p>

	 <p>
	 1) Téléchargez le fichier "Produits frais" de Google Drive sur votre bureau: <a  class="btn btn-primary" href="https://docs.google.com/spreadsheets/d/1Vkf5osNcUMww7s7pwZWsDBqNsEBrk2hCVg2PvIdModE/edit?usp=sharing" target=_new>
	Google Drive</a> 
	</p>     
	<form action="index.php" method="post" id="myForm" enctype="multipart/form-data">
		2) Chargez le fichier enregistré sur le bureau:
		<input type="file" name="fileToUpload" id="fileToUpload" > <button id="loadFilebtn" name="submit" class="btn btn-primary" type="submit" value="submit">Carica file</button>
	</form>
    <p>
			
';

echo '</div> <!-- header -->';


echo '<div id="menu"><hr></div>';





// Loading the spreadsheet file
// **************************************
?>


<?php
	$target_dir = "uploads/";
	$target_file = $target_dir . $week.'-'. basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

	// Check if image file is a actual image or fake image
	if (isset($_POST["submit"])) {

		if ($target_file == "uploads/") {
			$msg = "cannot be empty";
			$uploadOk = 0;
		} // Check if file already exists
		else if (file_exists($target_file)) {
			$msg = "The File already exists.";
			$uploadOk = 1;
		} 
		// Check file size
		if ($_FILES["fileToUpload"]["size"] > 5000000) {
			$msg = "Sorry, your file is too large.";
			$uploadOk = 0;
		} 
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			$msg = "Sorry, your file was not uploaded.";

			// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$msg = "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
			}
		}
	}

	$inputFileName = $target_file;
	
	echo '<div  class="alert alert-info" role="alert">'.$msg.'</div>';

  //   $inputFileName = "test.xlsx";

// Reading the spreadsheet file
// **************************************

	/**  Identify the type of $inputFileName  **/
	$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

	/**  Create a new Reader of the type that has been identified  **/
	$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");

	/**  Load $inputFileName to a Spreadsheet Object  **/
     $reader->setReadDataOnly(TRUE);

	$spreadsheet = $reader->load($inputFileName);
 
 	/**  Convert Spreadsheet Object to an Array for ease of use  **/
	// $listino = $spreadsheet->getActiveSheet()->toArray();
	
	// $listino = $spreadsheet->getSheetByName('mandala')->toArray();
	$maxCell = $spreadsheet->getSheetByName('mandala')->getHighestRowAndColumn();
    $listino = $spreadsheet->getSheetByName('mandala')->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);
    
    // $clienti = $spreadsheet->getSheetByName('clients')->toArray();
    $maxCell = $spreadsheet->getSheetByName('clients')->getHighestRowAndColumn();
    $clienti = $spreadsheet->getSheetByName('clients')->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row']);


// Parsing CUSTOMER list
// ***************************************

echo '<div id="customerslist">
          <h3>liste de clients</h3> ';
echo '<table class="table">';

			
echo '<tr><th>Clients</th><th>Transp. Indu</th><th>Transp. Euro</th><th>Marge</th><th>Lites des Prix</th><th>Actions</th></tr>';
foreach( $clienti as $cliente )
{               
   if (($cliente[0]!=NULL) && (strtoupper($cliente[0])!='CLIENTS')) {  // Avoid empty rows
	    echo '<tr>';
	    /*
	    foreach( $cliente as $dettaglio )
	    {
			if ($dettaglio != NULL) echo '<td> ' . $dettaglio . '</td>';
	    }*/
	        $outputOfferClient[strtoupper($cliente[0])] = 'offers/'.$week.'-Listino Prezzi Mandala-'.$cliente[0].'.xlsx';
	        $outputOfferClientMarge[strtoupper($cliente[0])] = 'offers/'.$week.'-Listino Prezzi Mandala-'.$cliente[0].'_marge.xlsx';

	        echo '<td> <a href="'.$outputOfferClientMarge[strtoupper($cliente[0])].'" target="new">'.$cliente[0].'</a></td>';  // Link al file generat
	        echo '<td>' . $cliente[1] . '€</td>';
	        echo '<td>' . $cliente[2] . '€</td>'; 
	        echo '<td>' . ($cliente[3]*100-100) . '%</td>';			
			echo '<td> <a href="'.$outputOfferClient[strtoupper($cliente[0])].'"><img src="imgs/xlsx.png"  width="30px" heigth="30px" target="_new"></a> | <a href="'.$outputOfferClient[strtoupper($cliente[0])].'.pdf" target="_new"><img src="imgs/pdf.svg" width="30px" heigth="30px"></a> | <a href="#" target="_new"><img src="imgs/link.png" width="30px" heigth="30px"></a></td>';  // Link al file generat
			echo '<td> <a href="#"> </a></td>';  // Link al file generat		
	        echo '</tr>'; 
    }
}
echo '</table>';
echo '</div>  <!-- listino -->';
echo '</div>';


// Parsing PRICES LIST
// ***************************************


echo '<hr>';

echo '<div id="customerslist">
          <h3>liste de prix</h3> ';
echo '<table class="table">';

echo '<div id="priceslist">';
echo $inputFile;
echo '<table class="table table-striped">';
echo '<tr><th></th><th>Produits</th><th>Producteur</th><th>N° de caisses INDU</th><th>N° de caisses EURO</th><th>Kg/Box</th><th>Kg/INDUpallet</th><th>Kg/EUROpallet</th><th></th><th>PV Producteur</th><th></th><th>PV Producteur (LANGRIDGE)</th></tr>';
foreach( $listino as $prodotto )
{               
   if (strtoupper($prodotto[0])!='NO' && $prodotto[0]!=NULL) {  // Avoid empty rows
	    if ($prodotto[0] == '*') {
	        echo '<tr style="background-color:#FFCC00">';
            echo '<td></td><td>'.$prodotto[1].'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
			echo '</tr>';

	    } else {
	        echo '<tr>';	
			echo '<td>'.$prodotto[0].'</td><td>'.$prodotto[1].'</td><td>'.$prodotto[2].'</td><td>'.$prodotto[3].'</td><td>'.$prodotto[4].'</td><td>'.$prodotto[5].'</td><td>'.$prodotto[6].'</td><td>'.$prodotto[7].'</td><td>'.$prodotto[8].'</td><td>'.$prodotto[9].'€</td><td></td><td>'.$prodotto[11].'€</td><td>';
			echo '</tr>';
        }
     } 
}
echo '</table>';
echo '</div>  <!-- listino -->';



// Creating spreadsheet  
// *************************************************************

if ($DEBUG) {
	echo '<h5>DEBUG ATTIVO</h5>';
} 



foreach( $clienti as $cliente ) {   // for each customer
	    $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
	    
	    
	    if ($cliente[0]==NULL) break;                     // termina sulle righe nulle
	    if (strtoupper($cliente[0])=='CLIENTS') {
		    // Si tratta della riga di intestazione.
		   
		    // ATTENZIONE LAVORA SOLO PER I FORNITORI ATTUALI
			for ($i=4;$i<$numProduttori+4;$i++) $elencoFornitori[]=strtoupper($cliente[$i]);    // Leggo l'elenco dei fornitori 
	    } else {
	       	
	       	$elencoClienti[]=strtoupper($cliente[0]);
	       	
	       	// ATTENZIONE LAVORA SOLO PER I FORNITORI ATTUALI
            for ($i=4;$i<$numProduttori+4;$i++) $fornitoriAmmessi[strtoupper($cliente[0])][$elencoFornitori[$i-4]]=strtoupper($cliente[$i]); 
              
    //			$outputOfferClient = 'offers/'.$week.'-Listino Prezzi Mandala-'.$cliente[0].'.xlsx';  // Nome del file per ogni cliente
			

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
			$sheet->setCellValue('B9', 'Liste des prix / Prices List (week: '.$week.' ** '.$date->setISODate(date("Y"), $week, "1")->format('d/m/Y')." - ".$date->setISODate(date("Y"), $week, "7")->format('d/m/Y').')');

			$row = 10;   // Starting row
			
			// Insert Head
			
			// $sheet->setCellValue('N'.$row,'Commande/Order');
			// $sheet->getStyle("N".$row.":P".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// $sheet->mergeCells("N".$row.":P".$row);

			//  $sheet->setCellValue('J'.$row,'Des prix/Prices');
			// $sheet->getStyle("J".$row.":L".$row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			// $sheet->mergeCells("J".$row.":L".$row);
			
			 $spreadsheet
						->getActiveSheet()
						->getStyle('J'.$row.':M'.$row)
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
						->getStyle('J'.$row.':M'.$row)
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
			$sheet->setCellValue('L'.$row,'Prix + transp. INDU pallet');	
			$sheet->getStyle('M'.$row)->getAlignment()->setHorizontal('center');
			$sheet->setCellValue('M'.$row,'Prix + transp. EURO pallet');	
			
			
			$sheet->getStyle('O'.$row)->getAlignment()->setHorizontal('center');
			$sheet->setCellValue('O'.$row,'Nbre Boxes');	
			$sheet->getStyle('P'.$row)->getAlignment()->setHorizontal('center');
			$sheet->setCellValue('P'.$row,'EURO');	
			$sheet->getStyle('Q'.$row)->getAlignment()->setHorizontal('center');
			$sheet->setCellValue('Q'.$row,'INDU');	

			$sheet->getStyle('A'.$row.':Q'.$row)
				->getAlignment()->setWrapText(true);
				
			// Freeze Rows Above (A3)
			$sheet->freezePane('C12'); 

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
				   if (strtoupper($prodotto[0]) != 'NO' && $prodotto[0] != NULL) {  // se non è una riga esclusa o una riga vuota
							
							if ($DEBUG) {
								print_r($fornitoriAmmessi);
								echo "<br>------cliente:".$cliente[0]." prodott(ORE):".$prodotto[2]."....(".$prodotto[1].")-----".$fornitoriAmmessi[strtoupper($cliente[0])][strtoupper($prodotto[2])]."<br><br><br><br>";
							}
						 					   																						
							  // if ( ($numProdottiProduttoreF>$numProdottiProduttoreI)) { // Non si tratta della prima intestazione e Inserisco la somma delle commande
								  
						      if ($prodotto[0] == '*') {   // Si tratta della riga intestazione località
									   
									   $spreadsheet
											->getActiveSheet()
											->getStyle('A'.$row.':M'.$row)
											->getFill()
											->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
											->getStartColor()
											->setARGB('ffcc00');	
								
									   $sheet->setCellValue('B'.$row, $prodotto[1]);   // Denominazione zona produttore		
									   
									    if ($DEBUG) {
												  echo 'Inserito INTESTAZIONE: '. $prodotto[1].'('.$prodotto[2].')<br>';
												  echo 'n: '.$n.'<br>';
												  echo 'row: '.$row.'<br>';
												  echo 'numProdottiProduttoreF:'.$numProdottiProduttoreF.'<br>';
											   }
									   						   
									   $row++;
									   $numProdottiProduttoreI=$row;   // Si riparte con un nuovo produttore
									   $numProdottiProduttoreF=$row;							      
							 } else if ((strtoupper($prodotto[0]) == 'FINE') && ($numProdottiProduttoreI!=$numProdottiProduttoreF)) {   //  Si sono finiti i prodotti e si inserisce la somma pedane
								  // $numProdottiProduttoreI++;
									$numProdottiProduttoreF--;
									$sheet->setCellValue('P'.$row, '=sum(P'.$numProdottiProduttoreI.':P'.$numProdottiProduttoreF.')');	 // totale composizione pedana EURO
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
												  echo 'Inserito SOOMATORIE COLONNE CLIENTI: '. $prodotto[1].'('.$prodotto[2].')<br>';
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
											 $prezzoVenditaINDU = ($prodotto[9]+($cliente[1]/$prodotto[6]))*$cliente[3];
											 $prezzoVenditaEURO = ($prodotto[9]+($cliente[2]/$prodotto[7]))*$cliente[3];
											 $sheet->setCellValue('J'.$row, $prezzoVenditaINDU);    // PV + trasporto INDU
											 $sheet->setCellValue('K'.$row, $prezzoVenditaEURO);    // PV + trasporto EURO
											 
											 
											 
											 $sheet->getStyle('L')
													  ->getNumberFormat()
													  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE); 											 
											 $sheet->getStyle('M')
													  ->getNumberFormat()
													  ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
										     $spreadsheet
												->getActiveSheet()
												->getStyle('L'.$row.':M'.$row)
												->getFill()
												->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
												->getStartColor()
												->setARGB('deb887');
											
											 $prezzo_INDU = ($prodotto[9]+($cliente[1]/$prodotto[6]));
											 $prezzo_EURO = ($prodotto[9]+($cliente[2]/$prodotto[7]));
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
													$sheet->setCellValue('P'.$row, '=O'.$row.'/E'.$row);	
													$sheet->setCellValue('Q'.$row, '=O'.$row.'/D'.$row);	
										  
										  
										      if ($DEBUG) {
												  echo 'Inserito nuovo prodotto: '. $prodotto[1].'('.$prodotto[2].')<br>';
												  echo 'n: '.$n.'<br>';
												  echo 'row: '.$row.'<br>';
												  echo 'numProdottiProduttoreF:'.$numProdottiProduttoreF.'<br>';
											   }
										  
											 $n++; // numero progressivo prodotti
											 $row++;  // riga per nuovo prodotto
											 $numProdottiProduttoreF++;				
											  				 										  
								 }     	// FINE ELSE *    				   
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
	     
	    if ( strtoupper($cliente[0]) == 'LANGRIDGE') { // Gestione eccezione Langridge
			$sheet->removeColumn('O');
			$sheet->removeColumn('P');
			$sheet->removeColumn('Q');
		}
		
		
        // Writing to file(s)
        $writer = new Xlsx($spreadsheet);

		$writer->save($outputOfferClientMarge[strtoupper($cliente[0])]);
        // Removing Price+Transport for users spreadsheet
        
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

		$xmlWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet,'Mpdf');
		$xmlWriter->writeAllSheets();
		// $xmlWriter->setFooter("Sdfsdf");
		$num = rand(00, 99);
		//create folder named files
		$xmlWriter->save($outputOfferClient[strtoupper($cliente[0])].".pdf");
		
		
		
	
		unset ($xmlWriter);
		unset ($spreadsheet);
        unset ($sheet);
		
		// $writerPDF     =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Tcpdf');
		// $writer   =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Dompdf');
		// $writer   =\PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Mpdf');
		// $pdf_path   ='prova.pdf'; 
		// $writerPDF->save($pdf_path);
	} // else
} // END for each customer

?>

  <div id="footer">....</div>
</div>
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>
</html>

