<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Confirm Stock Dispatch';
$this->params['breadcrumbs'][] = ['label' => 'Maintenance Material Request Master List', 'url' => ['/cmms/cmms-wo-material-request/pending-material-request-master-list']];
$this->params['breadcrumbs'][] = ['label' => 'Corrective Maintenance - Selected Part/Tool List', 'url' => ['/cmms/cmms-wo-material-request/view-selected-material-cm', 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex]];
$this->params['breadcrumbs'][] = ['label' => 'Work Order #' . $model->wo_id];
$this->params['breadcrumbs'][] = $this->title;

//$receiverModel = \frontend\models\cmms\RefAssignedPic::findOne($postData['receiver']['id']);
$receiverModel = common\models\User::findOne($postData['receiver']['id']);
$statusLabel = \frontend\models\bom\StockDispatchMaster::pending_status[$postData['current_sts']] ?? $postData['current_sts'];
?>

<style>
    .badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
</style>

<div class="cmms-wo-material-request-view mb-5">

    <h4>Confirm Stock Dispatch</h4>

    <div class="row mt-3">
        <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
            <h5 for="receiver" class="mb-0 pr-3 text-nowrap">Received By: </h5>
            <div class="w-100 form-control form-control-sm" disabled="true">
                <?= Html::encode($receiverModel->fullname ?? $postData['receiver']['id']) ?>
            </div>
        </div>
         <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
                <h5 class="mb-0 pr-3 text-nowrap">Status: </h5>
                <div class="w-100 form-control form-control-sm" disabled="true">
                    <?= Html::encode($statusLabel) ?>
                </div>
            </div>
    </div>
    <?php
    $faultCounter = 0;
    foreach ($postData['dispatch'] as $faultCounterKey => $faultItems):
        $faultCounter++;

        // Collect detail models for this fault group
        $detailIdsInGroup = array_keys($faultItems);
        $firstDetail = $detailModels[$detailIdsInGroup[0]] ?? null;
        $fault = $firstDetail ? \frontend\models\cmms\CmmsFaultList::findOne($firstDetail->fault_id) : null;
        $reportedBy = $fault ? \common\models\User::findOne($fault->reported_by) : null;

        $fid = $fault->id ?? $faultCounterKey;
        $headingId = 'heading_' . $faultCounter;
        $collapseId = 'collapse_' . $faultCounter;
        ?>

        <div class="table-responsive fault-panel" data-fault-id="<?= $fid ?>">
            <div class="card mt-2 bg-light">

                <div class="p-2 m-0 card-header hoverItem border-dark btn-header-link"
                     id="<?= $headingId ?>"
                     data-toggle="collapse"
                     data-target="#<?= $collapseId ?>"
                     aria-expanded="<?= $faultCounter === 1 ? 'true' : 'false' ?>"
                     aria-controls="<?= $collapseId ?>">

                    <span class="p-0 m-0 accordionHeader">
                        Fault #<?= $faultCounter ?>
                    </span>
                    &nbsp;
                    <span class="badge badge-secondary"><?= count($faultItems) ?> item(s)</span>
                    <?php if ($fault): ?>
                        <small class="text-muted ml-2">
                            ID: <?= Html::encode($fault->id ?? '-') ?>
                            | Fault Type: <?= Html::encode($fault->fault_type ?? 'No Type') ?>
                            | Reported By: <?= $reportedBy ? Html::encode($reportedBy->fullname) : '-' ?>
                            | Reported Date: <?= \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($fault->reported_at) ?? '-' ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div id="<?= $collapseId ?>"
                     class="collapse <?= $faultCounter === 1 ? 'show' : '' ?>"
                     aria-labelledby="<?= $headingId ?>">

                    <div class="card-body p-1" style="background-color: white;">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Part/Tool</th>
                                    <th rowspan="2">Model Type</th>
                                    <th rowspan="2">Brand</th>
                                    <th rowspan="2">Description</th>
                                    <th rowspan="2">Quantity</th>
                                    <!--<th rowspan="2">Remark</th>-->
                                    <th colspan="2" class="text-center">Dispatch</th>
                                </tr>
                                <tr>
                                    <th>Dispatch Quantity</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $itemIndex = 0;
                                foreach ($faultItems as $detailId => $item):
                                    $itemIndex++;
                                    $detail = $detailModels[$detailId] ?? null;
                                    if (!$detail)
                                        continue;
                                    ?>
                                    <tr>
                                        <td><?= $itemIndex ?></td>
                                        <td><?= $detail->part_or_tool == 1 ? 'Part' : 'Tool' ?></td>
                                        <td><?= Html::encode($detail->model_type) ?></td>
                                        <td><?= Html::encode($detail->brand) ?></td>
                                        <td><?= Html::encode($detail->descriptions) ?></td>
                                        <td><?= Html::encode($detail->qty) ?></td>
                                        <td><strong><?= Html::encode($item['dispatch_qty']) ?></strong></td>
                                        <td><?= Html::encode($item['remark'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    <?php endforeach; ?>

    <!-- Action buttons -->
    <div class="mt-3 d-flex justify-content-between">
        <?=
        Html::a('Back', ['view-selected-material-cm', 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex],
                ['class' => 'btn btn-secondary'])
        ?>

        <?php
        $form = ActiveForm::begin([
            'action' => ['confirm-stock-dispatch-cm', 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex],
        ]);
        ?>
        <?= Html::hiddenInput('confirm', '1') ?>
        <?= Html::submitButton('Confirm & Save', ['class' => 'btn btn-success']) ?>
        <?php ActiveForm::end(); ?>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-header-link').forEach(function (element) {
            element.addEventListener('click', function () {
                var target = this.getAttribute('data-target');
                var collapse = document.querySelector(target);
                if (collapse) {
                    $(collapse).collapse('toggle');
                }
            });
        });
    });
</script>