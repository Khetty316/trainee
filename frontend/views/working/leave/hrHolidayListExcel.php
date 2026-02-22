<?php

use common\models\myTools\MyFormatter;

$delimiter = ",";
$filename = "holiday_" . $selectYear . "_" . date('Y-m-d H:i:s') . ".csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

// Set column headers 
$yearRow = array('For Year:', $selectYear);

fputcsv($f, $yearRow, $delimiter);

$header1 = array('Date', "Holiday");
$header2 = array('(d/m/Y)', "");

fputcsv($f, $header1, $delimiter);
fputcsv($f, $header2, $delimiter);

foreach ($holidayList as $key => $holiday) {
    $lineData = array(MyFormatter::asDate_Read($holiday['holiday_date']), $holiday['holiday_name']);
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