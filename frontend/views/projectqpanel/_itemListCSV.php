<?php

use common\models\myTools\MyFormatter;

$delimiter = ",";
$projQRevision = $projQPanel->revision;
$project = $projQRevision->projectQType->project;
$filename = "panel-" . $project->quotation_no . "-" . $projQRevision->projectQType->type0->code . "-" . $projQRevision->revision_description . "-" . $projQPanel->sort . "-" . date("d-m-Y") . ".csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

$header1 = array('#', "Item", "Unit Price (RM)", "Quantity", "Total Price (RM)");

fputcsv($f, $header1, $delimiter);

foreach ($itemList as $key => $item) {
    $lineData = array($key + 1,
        $item['item_description'],
        MyFormatter::asDecimal2($item['amount'], 2),
        MyFormatter::asDecimal2($item['quantity'], 2) . " " . ($item->unitCode ? $item->unitCode->unit_name : null),
        number_format($item['amount'] * $item['quantity'], 2)
    );
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