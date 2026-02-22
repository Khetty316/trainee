<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

if ($moduleIndex === 'exec') {
    $pageName = 'Purchasing - Executive';
    $module = 'inventory';
    $key = 3;
    $url = 'pending-order-request-list?type=exec';
} else if ($moduleIndex === 'assist') {
    $pageName = 'Purchasing - Assistant';
    $module = 'inventory';
    $key = 3;
    $url = 'pending-order-request-list?type=assist';
}

$this->title = 'Confirm Order Requests';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => 'Pending Order Requests', 'url' => [$url]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mb-5">
    <?= Html::beginForm(['create-purchase-orders'], 'post') ?>
    <?= Html::hiddenInput('moduleIndex', $moduleIndex) ?>

    <?php foreach ($grouped as $supplierId => $data): ?>
        <h5 class="mt-4">
            <i class="fas fa-truck mr-2"></i>
            Supplier: <strong><?= Html::encode($data['supplier_name']) ?></strong>
        </h5>
        <table class="table table-bordered table-hover table-sm table-striped">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Model</th>
                    <th>Brand</th>
                    <th>Reference Type</th>
                    <th>Reference ID</th>
                    <th class="text-center">Order Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['items'] as $index => $item): ?>
                    <?php
                    // Pass selected IDs through hidden inputs
                    echo Html::hiddenInput('ids[]', $item->id);

                    // Reference Type
                    $referenceType = ($item->reference_type === 'bom_detail' || $item->reference_type === 'bomstockoutbound')
                        ? 'Project - Bill of Material'
                        : '-';

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
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= Html::encode($item->inventoryModel->type ?? '-') ?></td>
                        <td><?= Html::encode($item->inventoryModel->inventoryBrand->name ?? '-') ?></td>
                        <td><?= Html::encode($referenceType) ?></td>
                        <td><?= Html::encode($referenceId) ?></td>
                        <td class="text-center"><?= $item->required_qty ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <div class="mt-3">
        <?= Html::submitButton('<i class="fas fa-check mr-1"></i> Confirm & Create Purchase Orders', [
            'class' => 'btn btn-success',
            'onclick' => 'return confirm("Are you sure you want to create Purchase Orders for all listed suppliers?")'
        ]) ?>
        <?= Html::a('<i class="fas fa-times mr-1"></i> Cancel', [$url], ['class' => 'btn btn-secondary ml-2']) ?>
    </div>

    <?= Html::endForm() ?>
</div>
