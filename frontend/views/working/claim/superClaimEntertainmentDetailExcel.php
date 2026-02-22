<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;
use common\models\myTools\MyCommonFunction;
use yii\bootstrap4\ActiveForm;

$delimiter = ",";
$filename = "Entertainment_Claim_Detail_$selectYear" . "_" . date('Y-m-d H:i:s') . ".csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

// Set column headers 
$yearRow = array('', 'Year:', $selectYear);
fputcsv($f, $yearRow, $delimiter);


$header = array('#', "Date", "Staff Name", "Project Code", "Amount (RM)");
fputcsv($f, $header, $delimiter);

foreach ($claimsDetails as $key => $details) {
    $lineData = array($key + 1, MyFormatter::asDate_Read($details['invoice_date']), $details['claimant'],
        $details['project_account'],
        MyFormatter::asDecimal2($details['total']));
    fputcsv($f, $lineData, $delimiter);
}


// Move back to beginning of file 
fseek($f, 0);

// Set headers to download file rather than displayed 
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '";');

//output all remaining data on a file pointer 
fpassthru($f);

exit;
?>


