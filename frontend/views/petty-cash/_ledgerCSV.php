<?php
use yii\helpers\Html;

// UTF-8 BOM for Excel compatibility
echo "\xEF\xBB\xBF";

// Initialize totals
$totalDebit = 0;
$totalCredit = 0;
$totalBalance = 0;

echo "<table border='1' style='font-size:14px; border-collapse:collapse;'>";
echo "<tr><th colspan='9' style='text-align:center;'>Ledger Report from " . Yii::$app->formatter->asDate($masterLedger->startDate, 'php:d/m/Y') . " to " . Yii::$app->formatter->asDate($masterLedger->endDate, 'php:d/m/Y') . "</th></tr>";
echo "<tr>
        <th>No.</th>
        <th>Date</th>
        <th>Voucher No</th>
        <th>Reference 1</th>
        <th>Reference 2</th>
        <th>Description</th>
        <th align='right'>Debit Amount (RM)</th>
        <th align='right'>Credit Amount (RM)</th>
        <th align='right'>Balance Amount (RM)</th>
      </tr>";

foreach ($detailLedger as $key => $detail) {
    // Sum up totals (exclude null values from canceled entries)
    if ($detail->debit !== null) {
        $totalDebit += $detail->debit;
    }
    if ($detail->credit !== null) {
        $totalCredit += $detail->credit;
    }
    
    $totalBalance += $detail->balance;
    
    echo "<tr>";
    echo "<td align='center'>" . ($key + 1) . "</td>";
    echo "<td align='center'>" . Yii::$app->formatter->asDate($detail->date, 'php:d/m/Y') . "</td>";
    echo "<td align='center'>" . Html::encode($detail->voucher_no) . "</td>";
    echo "<td align='center'>" . Html::encode($detail->ref_1) . "</td>";
    echo "<td align='center'>" . Html::encode($detail->ref_2) . "</td>";
    echo "<td align='left'>" . Html::encode($detail->description) . "</td>";
    echo "<td align='right'>" . ($detail->debit !== null ? \common\models\myTools\MyFormatter::asDecimal2($detail->debit) : '0.00') . "</td>";
    echo "<td align='right'>" . ($detail->credit !== null ? \common\models\myTools\MyFormatter::asDecimal2($detail->credit) : '0.00') . "</td>";
    echo "<td align='right'>" . \common\models\myTools\MyFormatter::asDecimal2($detail->balance ?? 0.00) . "</td>";
    echo "</tr>";
}

// Get the last balance from the ledger
$lastBalance = !empty($detailLedger) ? end($detailLedger)->balance : 0.00;

echo "<tr>
        <td colspan='6' align='right'><strong>Total (RM):</strong></td>
        <td align='right'><strong>" . \common\models\myTools\MyFormatter::asDecimal2($totalDebit) . "</strong></td>
        <td align='right'><strong>" . \common\models\myTools\MyFormatter::asDecimal2($totalCredit) . "</strong></td>
        <td align='right'><strong>" . \common\models\myTools\MyFormatter::asDecimal2($totalBalance) . "</strong></td>
      </tr>";

echo "<tr>
        <td colspan='6' align='right'><strong>Balance (RM):</strong></td>
        <td colspan='3' align='right'><strong>" . \common\models\myTools\MyFormatter::asDecimal2($lastBalance) . "</strong></td>
      </tr>";

echo "</table>";
?>