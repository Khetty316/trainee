<?php
use yii\helpers\Html;

// Calculate totals - exclude null values (canceled entries)
$totalDebit = 0;
$totalCredit = 0;

foreach ($detailLedger as $key => $detail) {
    if ($detail->debit !== null) {
        $totalDebit += $detail->debit;
    }
    if ($detail->credit !== null) {
        $totalCredit += $detail->credit;
    }
}

// Total balance = Total Debit - Total Credit
$totalBalance = $totalDebit - $totalCredit;

// Get the last balance from ledger (running balance)
$lastBalance = !empty($detailLedger) ? end($detailLedger)->balance : 0;
reset($detailLedger); // Reset array pointer after using end()
?>

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                Ledger by: <?= Html::encode($masterLedger->createdBy->fullname) ?>
            </h6>
            <small class="text-muted">
                Period: <?= Yii::$app->formatter->asDate($startDate, 'php:d M Y') ?>
                – <?= Yii::$app->formatter->asDate($endDate, 'php:d M Y') ?>
            </small>
        </div>
        <div class="text-right mt-2 mt-md-0">
            <small class="text-muted d-block">Balance Amount</small>
            <h5 class="mb-0 <?= $lastBalance >= 0 ? 'text-success' : 'text-danger' ?> font-weight-bold">
                RM <?= \common\models\myTools\MyFormatter::asDecimal2($lastBalance ?? 0) ?>
            </h5>
        </div>
    </div>
    <div class="card-body p-2 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th width="12%">Voucher No</th>
                    <th width="15%">Reference 1</th>
                    <th width="15%">Reference 2</th>
                    <th>Description</th>
                    <th class="text-right">Debit Amount (RM)</th>
                    <th class="text-right">Credit Amount (RM)</th>
                    <th class="text-right">Balance Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detailLedger)): ?>
                    <?php foreach ($detailLedger as $key => $detail): ?>
                        <tr>
                            <td class="text-center"><?= $key + 1 ?></td>
                            <td class="text-center"><?= Yii::$app->formatter->asDate($detail->date, 'php:d/m/Y') ?></td>
                            <td class="text-center"><?= Html::encode($detail->voucher_no) ?></td>
                            <td class="text-center"><?= Html::encode($detail->ref_1) ?></td>
                            <td class="text-center"><?= Html::encode($detail->ref_2) ?></td>
                            <td class="text-left"><?= Html::encode($detail->description) ?></td>
                            <td class="text-right">
                                <?= \common\models\myTools\MyFormatter::asDecimal2($detail->debit ?? 0) ?>
                            </td>
                            <td class="text-right">
                                <?= \common\models\myTools\MyFormatter::asDecimal2($detail->credit ?? 0) ?>
                            </td>
                            <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($detail->balance ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    
                    <tr class="font-weight-bold">
                        <td colspan="6" class="text-right"><strong>Total (RM):</strong></td>
                        <td class="text-right"><strong><?= \common\models\myTools\MyFormatter::asDecimal2($totalDebit ?? 0) ?></strong></td>
                        <td class="text-right"><strong><?= \common\models\myTools\MyFormatter::asDecimal2($totalCredit ?? 0) ?></strong></td>
                        <td class="text-right"><strong><?= \common\models\myTools\MyFormatter::asDecimal2($lastBalance ?? 0) ?></strong></td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No ledger records found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>