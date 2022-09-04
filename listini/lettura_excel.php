<?php
set_error_handler(function(int $number, string $message) {
   echo "Handler captured error $number: '$message'" . PHP_EOL  ;
});


require __DIR__.'/listini/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
$inputFileName = "terroirist.xlsx";
?>

<table>
	
<?php
  // (A) PHPSPREADSHEET TO LOAD EXCEL FILE
  require "vendor/autoload.php";
  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $spreadsheet = $reader->load($inputFileName);
  $worksheet = $spreadsheet->getActiveSheet();
  foreach ($worksheet->getRowIterator() as $row) {
 
  // (B) READ CELLS
  $cellIterator = $row->getCellIterator();
  $cellIterator->setIterateOnlyExistingCells(false);
 
  // (C) OUTPUT HTML
  echo "<tr>";
  foreach ($cellIterator as $cell) { echo "<td>". $cell->getValue() ."</td>"; }
  echo "</tr>";
}
?>

</table>
