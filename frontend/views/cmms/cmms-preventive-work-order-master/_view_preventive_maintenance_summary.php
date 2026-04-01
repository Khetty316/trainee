<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use common\models\User;
use frontend\models\cmms\CmmsFaultList;
use frontend\models\cmms\CmmsAssetList;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    #item_table input,
    #item_table select,
    #item_table textarea {
        width: 100%;
        min-width: 0;
    }

    fieldset.form-group {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
    }
    
    a.disabled {
    pointer-events: none;
    opacity: 0.65;
}
</style>
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                Preventive Maintenance Work Order - <?= Html::encode($model->id ?? 'New') ?>
            </h6>
        </div>

        <div class="text-right mt-2 mt-md-0">
            <small class="text-muted d-block">Status</small>
            <span class="badge badge-info">
                <?= Html::encode($model->status ?? 'Draft') ?>
            </span>
        </div>
    </div>
    <div class="card-body p-2 table-responsive">
        <div class="cmms-fault-list-form table-responsive" id="fault-form-container">

            <?php $form = ActiveForm::begin([
                'id' => 'fault-form',
                'options' => [
                            'autocomplete' => 'off',
                            'enctype' => 'multipart/form-data'
                        ],
//                'action' => ['/cmms/cmms-fault-list/bulk-update', 'moduleIndex' => $moduleIndex],
//                'method' => 'post', 
            ]); ?>

            <table class="table table-bordered align-middle" id="item_table">
            <thead class="table-dark text-center">
                <tr>
                    <th class="text-center" width="15%">WO ID</th>
                    <th class="text-center" width="15%">Status</th>
                    <th class="text-center">Start Time</th>
                    <th class="text-center">End Time</th>
                    <!--<th width="15%">Machine Breakdown Type</th>-->
                    <th class="text-center" width="20%">Duration</th>
                    <th class="text-center" width="15%">Fault ID (if any)</th>
                    <th class="text-left" width="30%">Assigned PIC</th>
                    <!--<th>Status</th>-->
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <tr>
                    <td>
                        <?= Html::encode($model->id) ?>
                    </td>
                    <td>
                        <?= $model->progressStatus->name ?? '-' ?>
                    </td>
                    <td><?= $model->start_time ?></td>
                    <td><?= $model->end_time ?></td>
                    <td><?= $model->duration ?></td>
                    <td>
                        <div class="view-faults">
                            <?= Html::a(
                                    '<i class="bi bi-eye"></i>',
                                    'javascript:void(0);',
                                    [
                                        'class' => 'view-asset-btn btn btn-sm btn-success',
                                        'data-url' => Url::to([
                                            'view-reported-faults',
                                            'id' => $model->id,
                                            'moduleIndex' => $moduleIndex
                                            ]),
                                        'aria-disabled' => 'true',
                                    ]
                                ); 
                            ?>
                        </div>
                    </td>
                    <td>
                        <?= !empty($model->assignedPic)
                            ? implode(', ', array_map(
                                fn($pic) => $pic->name,
                                $model->assignedPic
                            ))
                            : '-' 
                        ?>
                    </td>
                    <td><?= $model->remarks ?></td>
                </tr>
            </tbody>
            </table>
            <?php ActiveForm::end(); ?>
        </div>
        
        <!--Asset Details container-->
        <div id="asset-details-container" style="display: none">
            <div class="d-flex justify-content-between mb-3">
                <button class="btn btn-secondary btn-sm" id="back-to-fault-list">
                    Back to PM WO Summary
                </button>
            </div>
            <div id="asset-details-content">
            </div>
        </div>
    </div>
</div>
<!--<php
    $this->registerJs(<<<JS
    $(document).on('click', 'a.modalButtonSingle', function (e) {
        e.preventDefault();

        const button = $(this);
        const url = button.attr('data-url');
        const title = button.data('modaltitle');

        if (!url || !url.includes('id=')) {
            return false;
        }

        $('#modalSingle').modal('show')
            .find('#modalSingleContent')
            .load(url);
    });
    JS);
?>-->
<script>
    $(document).on('click', '.view-asset-btn', function (e) {
        e.preventDefault();

        const assetId = $(this).data('asset-id');
        const modelId = $(this).data('model-id');

        $('#fault-form-container').fadeOut(150, function () {
            $('#asset-details-container').fadeIn(150);
            $('#asset-details-content').html('<div class="text-muted">Loading...</div>');

            $.get(
                '<?= Url::to(['view-reported-faults']) ?>',
                { id: <?= $model->id ?>, moduleIndex: '<?= $moduleIndex ?>' },
                function (html) {
                    $('#asset-details-content').html(html);
                }
            );
        });
    });

    $(document).on('click', '#back-to-fault-list', function () {
        $('#asset-details-container').fadeOut(150, function () {
            $('#asset-details-content').empty();
            $('#fault-form-container').fadeIn(150);
        });
    });
</script>