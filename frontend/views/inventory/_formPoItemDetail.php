<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-po-item-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-bordered table-hover table-sm table-striped">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Model</th>
                <th>Brand</th>
                <th>Reference Type</th>
                <th>Reference ID</th>
                <th>Requested By</th>
                <th class="text-center">Required Qty</th>
                <th class="text-center">Received Qty</th>
                <th class="text-center">Order Qty</th>
                <th class="text-center"></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allocation as $index => $item): ?>
                <?php
                // Pass selected IDs through hidden inputs
                // Reference Type
                $referenceType = ($item->inventoryOrderRequest->reference_type === 'bom_detail' || $item->inventoryOrderRequest->reference_type === 'bomstockoutbound') ? 'Project - Bill of Material' : '-';

                // Reference ID
                $referenceId = '-';
                if ($item->inventoryOrderRequest->reference_type === 'bom_detail') {
                    $ref = frontend\models\bom\BomDetails::findOne($item->inventoryOrderRequest->reference_id);
                    $referenceId = $ref->bomMaster->productionPanel->project_production_panel_code ?? '-';
                } elseif ($item->inventoryOrderRequest->reference_type === 'bomstockoutbound') {
                    $ref = frontend\models\bom\StockOutboundDetails::findOne($item->inventoryOrderRequest->reference_id);
                    $referenceId = $ref->bomDetail->bomMaster->productionPanel->project_production_panel_code ?? '-';
                } else if ($item->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                    $referenceType = 'Corrective Maintenance';
                    $referenceId = $item->inventoryOrderRequest->reference_id;
                } else if ($item->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                    $referenceType = 'Preventive Maintenance';
                    $referenceId = $item->inventoryOrderRequest->reference_id;
                }else if ($item->inventoryOrderRequest->reference_type === 'reserve') {
                    $referenceType = 'Reservation';
                    $user = common\models\User::findOne($item->inventoryOrderRequest->reference_id);
                    $referenceId = $user->fullname;
                }
                ?>
                <tr class="item-row" id="row-<?= $index ?>">
                    <?= Html::hiddenInput("InventoryOrderRequestAllocation[$index][id]", $item->id) ?>
                    <?=
                    Html::hiddenInput("InventoryOrderRequestAllocation[$index][removed]", 0, [
                        'class' => 'item-removed-flag',
                        'id' => "removed-flag-$index",
                    ])
                    ?>
                    <td><?= $index + 1 ?></td>
                    <td><?= Html::encode($item->inventoryOrderRequest->inventoryModel->type ?? '-') ?></td>
                    <td><?= Html::encode($item->inventoryOrderRequest->inventoryModel->inventoryBrand->name ?? '-') ?></td>
                    <td><?= Html::encode($referenceType) ?></td>
                    <td><?= Html::encode($referenceId) ?></td> 
                    <td class="text-center"><?= ($item->inventoryOrderRequest->requestedBy->fullname) . " @ " . common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($item->inventoryOrderRequest->requested_at) ?></td>
                    <td class="text-center"><?= $item->order_qty ?? 0 ?></td>
                    <td class="text-center"><?= $item->received_qty ?? 0 ?></td>
                    <td class="text-center"> 
                        <?=
                        Html::input('number', "InventoryOrderRequestAllocation[$index][order_qty]", $item->order_qty, array_merge([
                            'class' => 'form-control text-center item-qty',
                            'data-index' => $index,
                            'style' => 'height: auto; width: 100%;',
                            'max' => $item->order_qty,
                            'min' => max(1, $item->received_qty ?? 0),
                        ]))
                        ?>
                    </td>
                    <td class="text-center">
                        <?=
                        ($item->received_qty === null || $item->received_qty == 0) ? Html::a('<i class="fa fa-trash"></i>', '#', [
                                    'class' => 'btn btn-danger btn-sm btn-remove-item',
                                    'data-index' => $index,
                                    'title' => 'Remove Item',
                                ]) : '-'
                        ?>
                    </td>                   
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="7" class="text-right"><strong>Total :</strong></td>
                <td class="text-center"><?= $poItem->received_qty ?? '-' ?></td>
                <td class="text-center">
                    <?=
                    Html::input('number', 'total_order_qty', $poItem->order_qty, [
                        'id' => 'total-order-qty',
                        'class' => 'form-control text-center',
                        'style' => 'height: auto; width: 100%;',
                        'min' => 0,
                        'data-received-qty' => $poItem->received_qty ?? 0, // <-- add this
                    ])
                    ?>
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>

    function updateTotal() {
        let total = 0;
        let visibleItemCount = 0;

        $('.inventory-po-item-detail-form .item-qty').each(function () {
            let row = $(this).closest('tr');
            if (row.is(':visible')) {
                // Enforce min=1, max=required_qty per item
                let min = parseInt($(this).attr('min')) || 1;
                let max = parseInt($(this).attr('max')) || Infinity;
                let val = parseInt($(this).val()) || 0;

                if (val < min) {
                    $(this).val(min);
                    val = min;
                }
                if (val > max) {
                    $(this).val(max);
                    val = max;
                }

                total += val;
                visibleItemCount++;
            }
        });

        let totalInput = $('#total-order-qty');
        let receivedQty = parseInt(totalInput.data('received-qty')) || 0;
        let minVal;

        if (visibleItemCount > 0) {
            // Has allocated items: min = sum of all allocated order_qty
            minVal = total;
        } else if (receivedQty > 0) {
            // No allocated items but has received qty
            minVal = receivedQty;
        } else {
            // No allocated items, no received qty
            minVal = 1;
        }

        totalInput.attr('min', minVal);

        // Auto-correct if current value is below min
        if (parseInt(totalInput.val()) < minVal) {
            totalInput.val(minVal);
        }
    }

    $(document).on('input', '.item-qty', function () {
        updateTotal();
    });

    $(document).on('click', '.inventory-po-item-detail-form .btn-remove-item', function (e) {
        e.preventDefault();
        let row = $(this).closest('tr');
        let index = $(this).data('index');

        $('#removed-flag-' + index).val(1);
        row.hide();

        updateTotal();
    });

    updateTotal();

    $('form').on('beforeSubmit', function (e) {
        e.preventDefault();
        let form = this;
        if (confirm('Are you sure you want to save this update?')) {
            form.submit();
        }
        return false;
    });
</script>
