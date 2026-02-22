<?php

use common\models\myTools\MyFormatter;

$delimiter = ",";
$project = $projQRevision->projectQType->project;
$filename = "revision-" . $project->quotation_no . "-" . $projQRevision->projectQType->type0->code . "-" . $projQRevision->revision_description . "-" . date("d-m-Y") . ".csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

$header1 = array('Item', "Panel's Name", "Quantity", "Unit Price (" . $projQRevision->currency->currency_sign . ")", "Amount w/o Tax", "Tax Amount", "Amount with Tax");

fputcsv($f, $header1, $delimiter);


foreach ($panelList as $key => $panel) {

    $amt = $panel['amount'] ?? 0;
    $totAmt = $panel['amount'] * $panel['quantity'];
    $amtSST = $totAmt * $sst / 100;
    $totAmtWSST = $totAmt + $amtSST;
    $lineData = array($key + 1,
        $panel['panel_description'],
        $panel['quantity'] . " " . $panel['unitCode']['unit_name'] . ($panel['quantity'] > 1 ? "S" : ""),
        MyFormatter::asDecimal2($amt),
        MyFormatter::asDecimal2($totAmt),
        MyFormatter::asDecimal2($amtSST),
        MyFormatter::asDecimal2($totAmtWSST));
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