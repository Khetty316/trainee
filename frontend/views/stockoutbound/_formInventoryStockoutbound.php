<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */
/* @var $form yii\widgets\ActiveForm */

$max_total_return = $stockDetail->qty;
$total_reserved_qty = 0;
$total_dispatched = 0;
foreach ($inventoryStockoutbound as $data) {
    $total_dispatched += $data->dispatched_qty;
    $total_reserved_qty += $data->qty;
}

$disableReturn = ($total_reserved_qty <= $stockDetail->qty);
?>

<div class="inventory-stockoutbound-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="panel-heading mb-2">
        <strong>Model Type: </strong><?= $stockDetail->model_type ?? '-' ?>
        | <strong>Brand: </strong><?= $stockDetail->brand ?? '-' ?>
        | <strong>Description: </strong><?= $stockDetail->descriptions ?? '-' ?>
        | <strong>Total Qty: </strong><?= $stockDetail->qty ?? '-' ?>
        | <strong>Dispatched Qty: </strong><?= $stockDetail->dispatched_qty ?? '-' ?>
        | <strong>Unacknowledged Qty: </strong><?= $stockDetail->unacknowledged_qty ?? '-' ?>
        | <strong>Available Qty: </strong><?= $stockDetail->qty_stock_available ?? '-' ?>
    </div>
    <table class="table table-bordered table-hover table-sm table-striped" data-max-return="<?= $max_total_return ?>">        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Supplier</th>
                <th class="text-center">Reserved Qty</th>
                <th class="text-center">Dispatched Qty</th>
                <th class="text-center">Return Reserved Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($inventoryStockoutbound as $index => $data):
                ?>
                <tr class="item-row" id="row-<?= $index ?>">
                    <?= Html::hiddenInput("InventoryStockoutbound[$index][inventory_detail_id]", $data->inventory_detail_id) ?>
                    <?= Html::hiddenInput("InventoryStockoutbound[$index][id]", $data->id) ?>
                    <td><?= $index + 1 ?></td>
                    <td><?= ($data->inventoryDetail->supplier->name) ?></td>
                    <td class="text-center"><?= $data->qty ?? 0 ?></td>
                    <td class="text-center"><?= $data->dispatched_qty ?? 0 ?></td>
                    <td class="text-center"> 
                        <?=
                        Html::input('number', "InventoryStockoutbound[$index][return_reserved_qty]", 0, array_merge([
                            'class' => 'form-control text-center item-qty',
                            'data-index' => $index,
                            'style' => 'height: auto; width: 100%;',
                            'max' => max(0, ($data->qty - $data->dispatched_qty)),
                            'min' => 0,
                            'readonly' => $disableReturn
                        ]))
                        ?>
                    </td>
                </tr>
            <?php endforeach;
            ?>
            <tr>
                <td colspan="2" class="text-right"><strong>Total :</strong></td>
                <td class="text-center"><?= $total_reserved_qty ?? 0 ?></td>
                <td class="text-center">
                    <?= $total_dispatched ?? 0 ?>
                </td>
                <td class="text-center total_return">0</td>
            </tr>
        </tbody>
    </table>

    <div class="form-group">
        <?php if (!$disableReturn) { ?>
            <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
        <?php } ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(document).on('input', '.item-qty', function () {

        let total = 0;
        let maxTotal = $('table').data('max-return');

        $('.item-qty').each(function () {
            let val = parseFloat($(this).val()) || 0;
            total += val;
        });

        if (total > maxTotal) {
            alert('Total return qty cannot exceed ' + maxTotal);
            $(this).val(0);

            total = 0;
            $('.item-qty').each(function () {
                total += parseFloat($(this).val()) || 0;
            });
        }

        $('.total_return').text(total);
    });
</script>