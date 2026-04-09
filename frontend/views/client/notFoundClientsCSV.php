<?php

echo "<table border='1' style='font-size:14px;'>";
echo "<tr>
        <th>No.</th>
        <th>Cust No</th>
        <th>Fullname</th>
        <th>Balance (RM)</th>
        <th>Company Group</th>
      </tr>";

foreach ($data as $index => $detail) {

    if (empty($detail))
        continue;

    echo "<tr>";
    echo "<td>" . ($index + 1) . "</td>";
    echo "<td>" . $detail->cust_no . "</td>";
    echo "<td>" . $detail->name . "</td>";
    echo "<td>" . number_format($detail->balance, 2) . "</td>";
    echo "<td>" . $detail->company_group . "</td>";
    echo "</tr>";
}
echo "</table>";
