<?php

// Output BOM (Byte Order Mark) for UTF-8
echo "\xEF\xBB\xBF";

// Generate HTML table
echo "<table border='1' style='font-size:20px;>";
echo "<tr><th colspan='4'></th></tr>";
echo "<tr>";
echo "<th>No.</th>";
echo "<th>Staff ID</th>";
echo "<th>Fullname</th>";
echo "<th>Pending Work (RM)</th>";
echo "<th>Contribution (RM)</th>";
echo "<th>Incentive (RM)</th>";
echo "</tr>";

// Populate table rows with report details
foreach ($reportDetail as $index => $detail) {
    echo "<tr>";
    echo "<td>" . ($index + 1) . "</td>";
    echo "<td>" . $detail->staffId . "</td>";
    echo "<td>" . ucwords(strtolower($detail->fullname)) . "</td>";
    echo "<td>" . number_format($detail->totalPendingWorkAmount, 2) . "</td>";
    echo "<td>" . number_format($detail->totalPerformance, 2) . "</td>";
    echo "<td>" . number_format($detail->incentiveAmount, 2) . "</td>";
    echo "</tr>";
}
echo "<tr>";
echo "<td style='text-align:right;' colspan='3'><strong>Total:</strong></td>";
echo "<td style='text-align:right;'><strong>MYR $totalPendingWorkAmount</strong></td>";
echo "<td style='text-align:right;'><strong>MYR $totalPerformanceAmount</strong></td>";
echo "<td style='text-align:right;'><strong>MYR $totalIncentiveAmount</strong></td>";
echo "</tr>";

echo "</table>";
