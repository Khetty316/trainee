<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="inventory-po-item-receive-allocation-form">
    <?php $form = ActiveForm::begin(); ?>
    <h5>Total Received Qty: <span class="text-success"><?= Html::encode($poItem->received_qty) ?></span></h5>
    <?php
    $key = 0;
    foreach ($receives as $receive):
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Batch #<?= $key + 1 ?></strong>
                | Received Qty: <?= $receive->received_quantity ?>
                | Received Date: <?= common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($receive->received_at) ?>
                | By: <?= $receive->receivedBy->fullname ?>
            </div>

            <div class="panel-body">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>Reference Type</th>
                            <th>Reference ID</th>
                            <th class="text-center">Allocated Qty</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($receive->inventoryPoItemReceiveAllocations)): ?>
                            <tr>
                                <td colspan="4" class="text-muted">Quantity received and added to general stock inventory</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            foreach ($receive->inventoryPoItemReceiveAllocations as $allocation):
                                // Reference Type
                                $referenceType = ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bom_detail' || $allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bomstockoutbound') ? 'Project - Bill of Material' : '-';

                                // Reference ID
                                $referenceId = '-';
                                if ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bom_detail') {
                                    $ref = frontend\models\bom\BomDetails::findOne($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id);
                                    $referenceId = $ref->bomMaster->productionPanel->project_production_panel_code ?? '-';
                                } elseif ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === 'bomstockoutbound') {
                                    $ref = frontend\models\bom\StockOutboundDetails::findOne($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id);
                                    $referenceId = $ref->bomDetail->bomMaster->productionPanel->project_production_panel_code ?? '-';
                                } else if ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                                    $referenceType = 'Corrective Maintenance';
                                    $referenceId = $allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id;
                                } else if ($allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                                    $referenceType = 'Preventive Maintenance';
                                    $referenceId = $allocation->inventoryOrderRequestAllocation->inventoryOrderRequest->reference_id;
                                }
                                ?>
                                <tr>
                                    <td><?= $referenceType ?></td>
                                    <td><?= $referenceId ?? '-' ?></td>
                                    <td class="text-center"><?= $allocation->allocated_qty ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php $key++;
    endforeach;
    ?>

    <div class="form-group">
<?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>

