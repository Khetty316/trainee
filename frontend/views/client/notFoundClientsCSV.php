<?php

while (ob_get_level()) {
    ob_end_clean();
}

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=NotFoundClients.xls");

// UTF-8 fix
echo "\xEF\xBB\xBF";

echo "<table border='1' style='font-size:14px;'>";

// header
echo "<tr>
        <th>No.</th>
        <th>Cust No</th>
        <th>Fullname</th>
        <th>Balance (RM)</th>
        <th>Company Group</th>
      </tr>";

$index = 1;

foreach ($data as $row) {

    if (!is_array($row) || empty($row['cust_no'])) continue;

    echo "<tr>";
    echo "<td>{$index}</td>";
    echo "<td>{$row['cust_no']}</td>";
    echo "<td>{$row['name']}</td>";
    echo "<td>{$row['balance']}</td>";
    echo "<td>{$row['company_group']}</td>";
    echo "</tr>";

    $index++;
}

echo "</table>";

exit;

