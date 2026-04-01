<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\bom\StockDispatchMaster;

$this->title = 'Confirm Stock Dispatch';
$this->params['breadcrumbs'][] = ['label' => 'Maintenance Material Request Master List', 'url' => ['/cmms/cmms-wo-material-request/pending-material-request-master-list']];
$this->params['breadcrumbs'][] = ['label' => 'Selected Part/Tool List', 'url' => ['/cmms/cmms-wo-material-request/view-selected-material-pm', 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex]];
$this->params['breadcrumbs'][] = ['label' => 'Work Order #' . $model->wo_id];
$this->params['breadcrumbs'][] = $this->title;

$receiverModel = \frontend\models\cmms\RefAssignedPic::findOne(['staff_id' => $postData['receiver']['id']]);
$statusLabel = StockDispatchMaster::pending_status[$postData['current_sts']] ?? $postData['current_sts'];
?>

<style>
    .badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="cmms-wo-material-request-view mb-5">

    <h4>Work Order #<?= Html::encode($model->wo_id) ?> - Confirm Stock Dispatch</h4>

    <!-- Summary -->
    <div class="row mt-3">
        <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
            <h5 for="receiver" class="mb-0 pr-3 text-nowrap">Received By: </h5>
            <div class="w-100 form-control form-control-sm" disabled="true">
                <?= Html::encode($receiverModel->name ?? $postData['receiver']['id']) ?>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
            <h5 class="mb-0 pr-3 text-nowrap">Status: </h5>
            <div class="w-100 form-control form-control-sm" disabled="true">
                <?= Html::encode($statusLabel) ?>
            </div>
        </div>
    </div>

    <table class="table table-sm table-bordered mb-0 mt-2">
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Part/Tool</th>
                <th rowspan="2">Model Type</th>
                <th rowspan="2">Brand</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Quantity</th>
                <th colspan="2" class="text-center">Dispatch</th>
            </tr>
            <tr>
                <th>Dispatch Quantity</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($postData['dispatch'])): ?>
                <?php
                $itemIndex = 0;
                foreach ($postData['dispatch'] as $group):
                    foreach ($group as $detailId => $item):
                        $itemIndex++;
                        $detail = $detailModels[$detailId] ?? null;
                        if (!$detail)
                            continue;
                        $isInactive = ($detail->active_sts == 0);
                        ?>
                        <tr style="<?= $isInactive ? 'text-decoration: line-through; color: red;' : '' ?>">
                            <td><?= $itemIndex ?></td>
                            <td><?= $detail->part_or_tool == 1 ? 'Part' : 'Tool' ?></td>
                            <td><?= Html::encode($detail->model_type) ?></td>
                            <td><?= Html::encode($detail->brand) ?></td>
                            <td><?= Html::encode($detail->descriptions) ?></td>
                            <td><?= Html::encode($detail->qty) ?></td>
                            <td><strong><?= Html::encode($item['dispatch_qty']) ?></strong></td>
                            <td><?= Html::encode($item['remark'] ?? '') ?></td>
                        </tr>
                        <?php
                    endforeach;
                endforeach;
                ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted" style="padding: 20px;">
                        No items to dispatch.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Action buttons -->
    <div class="mt-3 d-flex justify-content-between">
        <?=
        Html::a('Back', ['view-selected-material-cm', 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex],
                ['class' => 'btn btn-secondary'])
        ?>

        <?php
        $form = ActiveForm::begin([
            'action' => ['confirm-stock-dispatch-pm', 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex],
        ]);
        ?>
        <?= Html::hiddenInput('confirm', '1') ?>
        <?= Html::submitButton('Confirm & Save', ['class' => 'btn btn-primary']) ?>
        <?php ActiveForm::end(); ?>
    </div>

</div>