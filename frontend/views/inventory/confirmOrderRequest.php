<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;
\common\models\myTools\Mydebug::dumpFileW($moduleIndex);

if ($moduleIndex === 'execPending' || $moduleIndex === 'execAll') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
} else if ($moduleIndex === 'assistPending' || $moduleIndex === 'assistAll') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
} else if ($moduleIndex === 'projcoor') {
    $pageName = 'Purchasing - Project Coordinator';
    $module = 'projcoor';
} else if ($moduleIndex === 'maintenanceHeadPending' || $moduleIndex === 'maintenanceHeadAll') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
}

$this->title = 'Confirm Order Requests';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => 'Order Request List', 'url' => ['order-request-list', 'type' => $moduleIndex]];
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
                    if ($item->reference_type === 'bom_detail') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($item->reference_type === 'bomstockoutbound') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($item->reference_type === 'reserve') {
                        $referenceType = 'Reservation';
                    } else if ($item->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                        $referenceType = 'Corrective Maintenance';
                    }else if ($item->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                        $referenceType = 'Preventive Maintenance';
                    }

                    // Reference ID
                    $referenceId = $item->reference_id;
                    if ($item->reference_type === 'bom_detail') {
                        $ref = frontend\models\bom\BomDetails::findOne($item->reference_id);
                        $referenceId = $ref->bomMaster->productionPanel->project_production_panel_code ?? '-';
                    } elseif ($item->reference_type === 'bomstockoutbound') {
                        $ref = frontend\models\bom\StockOutboundDetails::findOne($item->reference_id);
                        $referenceId = $ref->bomDetail->bomMaster->productionPanel->project_production_panel_code ?? '-';
                    } else if ($item->reference_type === 'reserve') {
                        $id = common\models\User::findOne($item->reference_id);
                        $referenceId = $id->fullname ?? '-';
                    }
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= Html::encode($item->inventoryModel->type ?? '-') ?></td>
                        <td><?= Html::encode($item->inventoryModel->inventoryBrand->name ?? '-') ?></td>
                        <td><?= Html::encode($referenceType) ?></td>
                        <td><?= Html::encode($referenceId) ?></td>
                        <td class="text-center"><?= ($item->required_qty - $item->order_qty) ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <div class="mt-3">
        <?=
        Html::submitButton('<i class="fas fa-check mr-1"></i> Confirm & Create Purchase Orders', [
            'class' => 'btn btn-success',
            'onclick' => 'return confirm("Are you sure you want to create Purchase Orders for all listed suppliers?")'
        ])
        ?>
    <?= Html::a('<i class="fas fa-times mr-1"></i> Cancel', ['order-request-list', 'type' => $moduleIndex], ['class' => 'btn btn-secondary']) ?>
    </div>

<?= Html::endForm() ?>
</div>
