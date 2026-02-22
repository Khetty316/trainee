<?php

$delimiter = ",";
$filename = "leave_entitlement_" . date('Y-m-d H:i:s') . ".csv";

// Create a file pointer 
$f = fopen('php://memory', 'w');

// Set column headers 
$yearRow = array('', 'Year:', $selectYear);
fputcsv($f, $yearRow, $delimiter);

$header1 = array('#', "Staff No.", 'Name', "Entitlement Id", "User Id",
    "Brought Forward", "Annual Leave Entitlement", "Sick Leave Entitlement");
$header2 = array('', "(Do Not Change)", '(Do Not Change)', "(Do Not Change)", "(Do Not Change)",
    "(From Last Year)", "", "");
fputcsv($f, $header1, $delimiter);
fputcsv($f, $header2, $delimiter);

foreach ($userList as $key => $user) {
    if (array_key_exists('annual_bring_forward_days', $user)) {
        $lineData = array($key + 1, $user['staff_id'], $user['fullname'], $user['id'], $user['user_id'], number_format($user['annual_bring_forward_days'], 1), number_format($user['annual_entitled'], 1), number_format($user['sick_entitled'], 1));
    } else {
        $lineData = array($key + 1, $user['staff_id'], $user['fullname'], '', $user['id'], '', '', '');
    }

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