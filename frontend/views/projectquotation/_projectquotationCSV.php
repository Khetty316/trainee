<?php

use common\models\myTools\MyFormatter;

// Output UTF-8 BOM for Excel
echo "\xEF\xBB\xBF";

// Calculate totals by currency
$totals = [];
$totalRows = 0;

// First pass: calculate totals and count rows
foreach ($models as $model) {
    $totalRows++;
    $currency = trim($model->currency_sign ?? '');
    $amount = floatval($model->total_amount ?? 0);

    // Skip if currency is empty
    if (empty($currency)) {
        continue;
    }

    if (!isset($totals[$currency])) {
        $totals[$currency] = 0;
    }
    $totals[$currency] += $amount;
}

// Sort currencies
ksort($totals);
?>
<table border="1">
    <thead>
        <tr style="font-weight: bold;">
            <th>No.</th>
            <th>Quotation No</th>                                                                                                      
            <th>Project Name</th>
            <th>Currency</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Clients</th>
            <th>Project Coordinator</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($models as $model): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($model->quotation_display_no ?? '') ?></td>
                <td><?= htmlspecialchars($model->project_name ?? '') ?></td>
                <td><?= htmlspecialchars($model->currency_sign ?? '') ?></td>
                <td style="text-align: right;">
                    <?= $model->total_amount > 0 ? number_format($model->total_amount, 2) : '0.00' ?>
                </td>
                <td><?= htmlspecialchars($model->status ?? '') ?></td>
                <td>
                    <?php
                    $clients = [];
                    if (!empty($model->clients)) {
                        $clientsData = explode("|||", trim($model->clients));
                        foreach ($clientsData as $client) {
                            if (!empty($client)) {
                                $clients[] = '- ' . trim($client);
                            }
                        }
                    }
                    echo implode('<br>', $clients);
                    ?>
                </td>
                <td><?= htmlspecialchars($model->project_coordinator_fullname ?? '') ?></td>
            </tr>
        <?php endforeach; ?>
        <!-- Total summary rows -->
        <tr style="font-weight: bold;">
            <td colspan="3" style="text-align: right; font-weight: bold;">TOTAL ITEMS:</td>
            <td colspan="5" style="text-align: left; font-weight: bold;"><?= $totalRows ?></td>
        </tr>

        <!-- Grand total if multiple currencies -->
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: right; font-weight: bold;">GRAND TOTAL (All Currencies):</td>
                <td style="text-align: left; font-weight: bold;">
                    <?php
                    foreach ($totals as $currency => $amount) {
                        echo htmlspecialchars($currency) . '<br>';
                    }
                    ?>
                </td>
                <td colspan="4" style="text-align: left; font-weight: bold;">
                    <?php
                    foreach ($totals as $currency => $amount) {
                        echo number_format($amount, 2) . '<br>';
                    }
                    ?>
                </td>
            </tr>
    </tbody>
</table>