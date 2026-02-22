<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;

/* @var $reorderMaster frontend\models\inventory\InventoryReorderMaster */
/* @var $itemList frontend\models\inventory\InventoryReorderItem[] */

$this->title = 'Reorder Item List';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Puchasing', 'url' => ['reorder-list']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="inventory-reorder-item-list">
    <p>
<?= Html::a(
    '<i class="fas fa-file-invoice-dollar"></i> Proceed to Purchase Order',
    ['create-purchase-order', 'reorder_id' => $reorderMaster->id],
    [
        'class' => 'btn btn-success',
        'data-confirm' => 'Proceed to create Purchase Order from this Reorder list?',
    ]
) ?>
</p>
    <h3 class="mb-3">
        Pre-Requisition Form:
        <strong><?= Html::encode($reorderMaster->prereqFormMaster->prf_no ?? '-') ?></strong>
    </h3>

    <h5 class="mb-3">
        Department:
        <strong><?= Html::encode($reorderMaster->department->department_name ?? '-') ?></strong>
    </h5>

    <table class="table table-bordered table-striped table-sm">
        <thead class="table-dark">
            <tr>
                <th class="text-center" width="3%">#</th>
                <th>Supplier</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Item Description</th>
                <th class="text-center">Currency</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total Price</th>
                <th>Purpose</th>
                <th class="text-center">Order Qty</th>
                <th class="text-center">Remaining Qty</th>
                <th class="text-center">Received Qty</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($itemList)): ?>
                <?php foreach ($itemList as $i => $item): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= Html::encode($item->inventoryDetail->supplier->name ?? '-') ?></td>
                        <td><?= Html::encode($item->inventoryDetail->brand->name ?? '-') ?></td>
                        <td><?= Html::encode($item->inventoryDetail->model->type ?? '-') ?></td>
                        <td><?= Html::encode($item->prereqFormItem->item_description) ?></td>
                        <td class="text-center"><?= $item->prereqFormItem->currency ?></td>
                        <td class="text-right"><?= MyFormatter::asDecimal2($item->prereqFormItem->unit_price) ?></td>
                        <td class="text-right"><?= MyFormatter::asDecimal2($item->prereqFormItem->total_price) ?></td>
                        <td><?= Html::encode($item->prereqFormItem->purpose_or_function) ?></td>
                        <td class="text-center"><?= $item->order_qty ?></td>
                        <td class="text-center"><?= $item->remaining_qty ?></td>
                        <td class="text-center"><?= $item->received_qty ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center text-muted">
                        No items found
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>
