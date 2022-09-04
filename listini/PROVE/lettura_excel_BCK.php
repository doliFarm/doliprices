<?php

set_error_handler(function(int $number, string $message) {
   echo "Handler captured error $number: '$message'" . PHP_EOL  ;
});


require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
$inputFileName = "uploads/test.xlsx";

// Reading the spreadsheet file
// **************************************


/**  Identify the type of $inputFileName  **/
$inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);

/**  Create a new Reader of the type that has been identified  **/
$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
echo "ciao";
/**  Load $inputFileName to a Spreadsheet Object  **/

$spreadsheet = $reader->load($inputFileName);
echo "miao";
/**  Convert Spreadsheet Object to an Array for ease of use  **/
$listino = $spreadsheet->getActiveSheet()->toArray();



// Parsing PRICES LIST
// ***************************************
echo '<div id="priceslist">';
echo '<table border=1>';
foreach( $listino as $prodotto )
{               
   if ($prodotto[0]!=NULL) {  // Avoid empty rows
	    echo '<tr>';
	    foreach( $prodotto as $dettaglio )
	    {
		echo '<td> ' . $dettaglio . '</td>';
	    }
	    echo '</tr>';
    }
}
echo '</table>';
echo '</div>  <!-- listino -->';

?>
