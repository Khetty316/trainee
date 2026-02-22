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
            <?php foreach ($orderRequests as $index => $item): ?>
                <?php
                // Pass selected IDs through hidden inputs
                // Reference Type
                $referenceType = ($item->reference_type === 'bom_detail' || $item->reference_type === 'bomstockoutbound') ? 'Project - Bill of Material' : '-';

                // Reference ID
                $referenceId = '-';
                if ($item->reference_type === 'bom_detail') {
                    $ref = frontend\models\bom\BomDetails::findOne($item->reference_id);
                    $referenceId = $ref->bomMaster->productionPanel->project_production_panel_code ?? '-';
                } elseif ($item->reference_type === 'bomstockoutbound') {
                    $ref = frontend\models\bom\StockOutboundDetails::findOne($item->reference_id);
                    $referenceId = $ref->bomDetail->bomMaster->productionPanel->project_production_panel_code ?? '-';
                }
                ?>
                <tr class="item-row" id="row-<?= $index ?>">
                    <?= Html::hiddenInput("InventoryOrderRequests[$index][id]", $item->id) ?>
                    <?=
                    Html::hiddenInput("InventoryOrderRequests[$index][removed]", 0, [
                        'class' => 'item-removed-flag',
                        'id' => "removed-flag-$index",
                    ])
                    ?>
                    <td><?= $index + 1 ?></td>
                    <td><?= Html::encode($item->inventoryModel->type ?? '-') ?></td>
                    <td><?= Html::encode($item->inventoryModel->inventoryBrand->name ?? '-') ?></td>
                    <td><?= Html::encode($referenceType) ?></td>
                    <td><?= Html::encode($referenceId) ?></td> 
                    <td class="text-center"><?= ($item->requestedBy->fullname) . " @ " . common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($item->requested_at) ?></td>
                    <td class="text-center"><?= $item->required_qty ?? 0 ?></td>
                    <td class="text-center"><?= $item->received_qty ?? 0 ?></td>
                    <td class="text-center"> 
                        <?=
                        Html::input('number', "InventoryOrderRequests[$index][order_qty]", $item->order_qty, array_merge([
                            'class' => 'form-control text-center item-qty',
                            'data-index' => $index,
                            'style' => 'height: auto; width: 100%;',
                            'max' => $item->required_qty, // can't exceed what's required
                            'min' => max(0, $item->required_qty - $item->received_qty),
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
                        'min' => 0, // should not below than total order_qty
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

        $('.inventory-po-item-detail-form .item-qty').each(function () {
            total += parseInt($(this).val()) || 0;
        });

        let totalInput = $('#total-order-qty');

        // If user hasn't manually increased it yet, update value
        if (parseInt(totalInput.val()) < total) {
            totalInput.val(total);
        }

        // Always set minimum to item sum
        totalInput.attr('min', total);
    }

    // Live update total when any item qty changes
    $(document).on('input', '.item-qty', function () {
        updateTotal();
    });

    // Remove row and recalculate
    $(document).on('click', '.inventory-po-item-detail-form .btn-remove-item', function (e) {
        e.preventDefault();
        let row = $(this).closest('tr');
        let index = $(this).data('index');

        // Mark as removed
        $('#removed-flag-' + index).val(1);

        // Hide the row visually
        row.hide();

        updateTotal();
    });

    // Initialize on load
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