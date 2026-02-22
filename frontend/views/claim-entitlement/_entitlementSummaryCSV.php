<?php
// UTF-8 BOM for Excel compatibility (if the output is later copied/streamed as CSV)
echo "\xEF\xBB\xBF";

// --- Dynamic Column Setup ---
// Calculate the number of columns per month (5 without entitlement, 7 with)
$hasEntitlement = (bool)$hasEntitlement;
$colsPerMonth = $hasEntitlement ? 7 : 5; 

// Generate short month names (Jan, Feb, etc.)
$shortMonthMap = [];
$allShortMonthNames = [];

if (isset($monthList) && is_array($monthList)) {
    foreach ($monthList as $key => $longName) {
        // Take the first 3 characters (Jan, Feb, etc.)
        $shortName = substr($longName, 0, 3); 
        $shortMonthMap[$key] = $shortName;
        $allShortMonthNames[] = $shortName;
    }
}

// Determine which month headers to display based on $month (single) or $intMonth (multiple)
$displayMonths = [];
if (!empty($month) && isset($shortMonthMap[$month])) {
    $displayMonths = [$shortMonthMap[$month]];
} else if (!empty($intMonth) && is_array($intMonth)) {
    foreach($intMonth as $mNum) {
        $monthIndex = sprintf('%02d', $mNum);
        if (isset($shortMonthMap[$monthIndex])) {
             $displayMonths[] = $shortMonthMap[$monthIndex];
        }
    }
} else {
    $displayMonths = $allShortMonthNames; // Fallback to all months if $intMonth isn't set
}

// Total number of columns in the data section
$totalDataColumns = count($intMonth) * $colsPerMonth;
?>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            table {
                border-collapse: collapse;
                width: 100%;
                font-family: Arial, sans-serif;
            }
            th, td {
                border: 1px solid #dee2e6;
                padding: 8px;
            }
            thead th {
                background-color: #f8f9fa; /* Light background for headers */
                font-weight: bold;
                border-bottom: 2px solid #adb5bd;
            }
            .text-right { text-align: right; }
            .textcenter { text-align: center; }
            .textleft { text-align: left; }
            .bg-light { background-color: #e9ecef; } /* Total row background */
        </style>
    </head>
    <body>
        <h2>Claim Summary Report - <?= htmlspecialchars($year) ?></h2>
        <?php if (!empty($monthName)): ?>
            <h4>Month: <?= htmlspecialchars($monthName) ?></h4>
        <?php endif; ?>
        <?php if (!empty($claimType)): ?>
            <h4>Claim Type: <?= htmlspecialchars($claimType) ?></h4>
        <?php endif; ?>

        <table>
            <thead>
                <!-- Row 1: Month Names (Spanning 5 or 7 columns) -->
                <tr>
                    <th rowspan="2" class="textcenter">Staff ID</th>
                    <th class="textleft" rowspan="2">Staff Name</th>
                    <?php
                    // Display the months that are being reported
                    foreach ($displayMonths as $monthName) {
                        ?>
                        <th colspan="<?= $colsPerMonth ?>" class="textcenter"><?= htmlspecialchars($monthName) ?></th>
                    <?php }
                    ?>
                </tr>
                <!-- Row 2: Metric Names (Entitlement, Submitted, Approved, etc.) -->
                <tr>
                    <?php
                    foreach ($displayMonths as $monthName) {
                        ?>
                        <?php if ($hasEntitlement): ?>
                            <th class="textcenter">Entitlement</th>
                            <th class="textcenter">Available Balance</th>
                        <?php endif; ?>
                        <th class="textcenter">Submitted Claim</th> 
                        <th class="textcenter">Approved Claim</th>
                        <th class="textcenter">Rejected Claim</th>
                        <th class="textcenter">Pending Payment</th>
                        <th class="textcenter">Paid</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // --- DATA ROWS ---
                foreach ($claimSummarys as $claimSummary) {
                    ?>
                    <tr>
                        <td class="textcenter"><?= htmlspecialchars($claimSummary["staffid"]) ?></td>
                        <td class="textleft"><?= htmlspecialchars($claimSummary["fullname"]) ?></td>
                        <?php
                        // Loop through all selected months to output data cells
                        foreach ($intMonth as $monthNum) {
                            $monthKey = sprintf('%02d', $monthNum);
                            $data = $claimSummary[$monthKey] ?? []; // Use null coalescing for safety
                            $isUnlimited = (isset($data['Entitlement']) && $data['Entitlement'] === 'No limit');
                            ?>
                            <?php if ($hasEntitlement): ?>
                                <!-- Entitlement -->
                                <td class="text-right">
                                    <?= $isUnlimited ? 'No limit' : number_format($data['Entitlement'] ?? 0, 2) ?>
                                </td>
                                <!-- Balance -->
                                <td class="text-right">
                                    <?= $isUnlimited ? 'No limit' : number_format($data['Balance'] ?? 0, 2) ?>
                                </td>
                            <?php endif; ?>
                            <!-- Submitted Claim Data -->
                            <td class="text-right"><?= number_format($data['ClaimSubmit'] ?? 0, 2) ?></td>
                            <!-- Remaining Columns -->
                            <td class="text-right"><?= number_format($data['ClaimApprove'] ?? 0, 2) ?></td>
                            <td class="text-right"><?= number_format($data['ClaimReject'] ?? 0, 2) ?></td>
                            <td class="text-right"><?= number_format($data['Pending'] ?? 0, 2) ?></td>
                            <td class="text-right"><?= number_format($data['Paid'] ?? 0, 2) ?></td>
                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                // --- TOTALS CALCULATION ---
                $monthTotals = [];
                $grandTotals = [
                    'Entitlement' => 0, 'Balance' => 0, 'ClaimSubmit' => 0, 'ClaimApprove' => 0, 
                    'ClaimReject' => 0, 'Pending' => 0, 'Paid' => 0, 'Unlimited' => false
                ];

                // Prepare and calculate monthly totals
                foreach ($intMonth as $monthNum) {
                    $monthKey = sprintf('%02d', $monthNum);
                    $monthTotals[$monthKey] = $grandTotals; // Initialize current month total
                    $monthTotals[$monthKey]['Unlimited'] = false; // Reset unlimited flag per month
                }

                // Sum the data
                foreach ($claimSummarys as $claimSummary) {
                    foreach ($intMonth as $monthNum) {
                        $monthKey = sprintf('%02d', $monthNum);
                        $data = $claimSummary[$monthKey] ?? [];

                        $metrics = ['Entitlement', 'Balance', 'ClaimSubmit', 'ClaimApprove', 'ClaimReject', 'Pending', 'Paid'];
                        
                        foreach($metrics as $metric) {
                            if (isset($data[$metric]) && is_numeric($data[$metric])) {
                                $monthTotals[$monthKey][$metric] += $data[$metric];
                                $grandTotals[$metric] += $data[$metric];
                            }
                        }

                        // Check for 'No limit' which means unlimited
                        if (isset($data['Entitlement']) && $data['Entitlement'] === 'No limit') {
                             $monthTotals[$monthKey]['Unlimited'] = true;
                             $grandTotals['Unlimited'] = true;
                        }
                    }
                }
                ?>

                <!-- Monthly Totals Row -->
                <tr class="bg-light">
                    <th colspan="2" class="text-right">Monthly Total (RM):</th>
                    <?php 
                    // Loop through each selected month to display its totals
                    foreach ($intMonth as $monthNum): 
                        $monthKey = sprintf('%02d', $monthNum);
                        $monthTotal = $monthTotals[$monthKey];
                        ?>
                        <?php if ($hasEntitlement): ?>
                            <!-- Entitlement Total -->
                            <th class="text-right">
                                <?= $monthTotal['Unlimited'] ? 'Unlimited' : number_format($monthTotal['Entitlement'], 2) ?>
                            </th>
                            <!-- Balance Total -->
                            <th class="text-right">
                                <?= $monthTotal['Unlimited'] ? 'Unlimited' : number_format($monthTotal['Balance'], 2) ?>
                            </th>
                        <?php endif; ?>
                        <!-- Claim Totals -->
                        <th class="text-right"><?= number_format($monthTotal['ClaimSubmit'], 2) ?></th> 
                        <th class="text-right"><?= number_format($monthTotal['ClaimApprove'], 2) ?></th>
                        <th class="text-right"><?= number_format($monthTotal['ClaimReject'], 2) ?></th>
                        <th class="text-right"><?= number_format($monthTotal['Pending'], 2) ?></th>
                        <th class="text-right"><?= number_format($monthTotal['Paid'], 2) ?></th>
                    <?php endforeach; ?>
                </tr>

                <!-- Grand Total Row -->
                <tr class="bg-light">
                    <th colspan="2" class="text-right">Grand Total <?= !empty($month) && isset($monthName) ? '(' . htmlspecialchars($monthName) . ')' : '(All Months)' ?> (RM):</th>
                    <!-- The colspan spans all the data columns -->
                    <th colspan="<?= $totalDataColumns ?>" class="textleft">
                        <?php if ($hasEntitlement): ?>
                            Entitlement: <?= $grandTotals['Unlimited'] ? 'Unlimited' : number_format($grandTotals['Entitlement'], 2) ?> |
                            Available Balance: <?= $grandTotals['Unlimited'] ? 'Unlimited' : number_format($grandTotals['Balance'], 2) ?> |
                        <?php endif; ?>
                        Submitted Claim: <?= number_format($grandTotals['ClaimSubmit'], 2) ?> |
                        Approved: <?= number_format($grandTotals['ClaimApprove'], 2) ?> |
                        Rejected: <?= number_format($grandTotals['ClaimReject'], 2) ?> |
                        Pending: <?= number_format($grandTotals['Pending'], 2) ?> |
                        Paid: <?= number_format($grandTotals['Paid'], 2) ?> 
                    </th>
                </tr>
            </tfoot>
        </table>
    </body>
</html>