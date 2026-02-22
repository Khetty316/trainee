<?php

use yii\helpers\Html;
use common\models\User;
use common\models\myTools\MyFormatter;
use frontend\models\RefGeneralStatus;

$totalsByCurrency = [];

foreach ($items as $item) {
    // Requested (left side)
    $reqCurrency = $item->currency ?? 'N/A';
    if (!isset($totalsByCurrency[$reqCurrency])) {
        $totalsByCurrency[$reqCurrency] = ['requested' => 0, 'approved' => 0];
    }
    $totalsByCurrency[$reqCurrency]['requested'] += $item->total_price;

    // Approved (right side, if available)
    $approvedCurrency = $item->currency_approved ?? $reqCurrency;
    if (!isset($totalsByCurrency[$approvedCurrency])) {
        $totalsByCurrency[$approvedCurrency] = ['requested' => 0, 'approved' => 0];
    }

    // Only count approved total if status is approved
    if ($item->status == 0) {
        $totalsByCurrency[$approvedCurrency]['approved'] += $item->total_price_approved;
    }
}
?>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid black;
        padding: 5px;
    }
    .text-right {
        text-align: right;
    }
    .text-center {
        text-align: center;
    }
</style>

<div style="font-family: Arial;">
    <h2 style="text-align: center;">PRE-REQUISITION FORM</h2>

    <p><strong>NAME:</strong> <?= isset($master->createdBy) ? $master->createdBy->fullname : 'N/A' ?></p>
    <p><strong>PRF NO:</strong> <?= $master->prf_no ?></p>
    <p><strong>DATE OF MATERIAL REQUIRED:</strong> <?= Yii::$app->formatter->asDate($master->date_of_material_required, 'php:d/m/Y') ?></p>

    <table class="table table-bordered mb-0" id="item_table">
        <thead class="table-dark">
            <tr>
                <th rowspan="2" class="text-center" width="4%">No.</th>
                <th rowspan="2" width="20%">Item Description</th>
                <th rowspan="2" class="text-center" width="7%">Qty</th>
                <th rowspan="2" class="text-center" width="6%">Currency</th>
                <th rowspan="2" class="text-right" width="8%">Unit Price</th>
                <th rowspan="2" class="text-right" width="8%">Total Price</th>
                <th rowspan="2" width="12%">Purpose</th>
                <th colspan="5" class="text-center" width="35%">Superior's Response</th>
            </tr>
            <tr>
                <th class="text-center" width="6%">Qty</th>
                <th class="text-center" width="6%">Currency</th>
                <th class="text-right" width="8%">Unit Price</th>
                <th class="text-right" width="8%">Total Price</th>
                <th class="text-left" width="7%">Remark</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td class="text-center"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($item->item_description) ?></td>
                    <td class="text-center"><?= $item->quantity ?></td>
                    <td class="text-center"><?= $item->currency ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2($item->unit_price) ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2($item->total_price) ?></td>
                    <td><?= htmlspecialchars($item->purpose_or_function) ?></td>
                    <td class="text-center"><?= $item->quantity_approved ?: '-' ?></td>
                    <td class="text-center"><?= $item->currency_approved ?: $item->currency ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2($item->unit_price_approved ?? 0) ?></td>
                    <td class="text-right"><?= MyFormatter::asDecimal2($item->total_price_approved ?? 0) ?></td>
                    <td>
                        <div class="decision-result">
                            <?php if ($worklist[$item->id]->status == RefGeneralStatus::STATUS_Approved): ?>
                                <span class="text-success">Approved</span><br>
                                <?php
                                $responder = User::findOne($worklist[$item->id]->responded_by);
                                if ($responder):
                                    ?>
                                    by <?= Html::encode($responder->fullname) ?>
                                <?php endif; ?>
                                @ <?= MyFormatter::asDateTime_ReaddmYHi($worklist[$item->id]->created_at) ?>

                            <?php elseif ($worklist[$item->id]->status == RefGeneralStatus::STATUS_SuperiorRejected): ?>
                                <!--Rejected Display--> 
                                <span class="text-danger">Rejected</span><br>
                                <?php
                                $responder = User::findOne($worklist[$item->id]->responded_by);
                                if ($responder):
                                    ?>
                                    by <?= Html::encode($responder->fullname) ?>
                                    @ <?= MyFormatter::asDateTime_ReaddmYHi($worklist[$item->id]->created_at) ?>
                                    <br>
                                    <small class="text-danger">
                                        <strong>Reject Reason:</strong><br>
                                        <?= Html::encode($worklist[$item->id]->remark) ?>
                                    </small>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <?php foreach ($totalsByCurrency as $currency => $total): ?>
                <tr>
                    <td colspan="7" class="text-right">
                        <strong>Total Amount (<?= $currency ?>):</strong>
                    </td>
                    <td colspan="2" class="text-left">
                        <span><strong>Requested:</strong> <?= MyFormatter::asDecimal2($total['requested']) ?></span>
                    </td>
                    <td colspan="3" class="text-left">
                        <span><strong>Approved:</strong> <?= MyFormatter::asDecimal2($total['approved']) ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tfoot>
    </table>
</div>
