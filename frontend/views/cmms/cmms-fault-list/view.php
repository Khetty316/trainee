<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\helpers\Url;
use yii\helpers\ArrayHelper;

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
</style>
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                Fault List - <?= Html::encode($model->id ?? 'New') ?>
            </h6>
            <small class="text-muted">
                Created on: <?= Yii::$app->formatter->asDate($model->reported_at, 'php:d M Y') ?>
            </small>
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

        <!--    <? Html::hiddenInput('area', $assetArea) ?>
            <? Html::hiddenInput('section', $assetSection) ?>
            <? Html::hiddenInput('fault_asset_id', $assetCode) ?>-->
        <!--    <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Asset Details:</legend>
            <table class="table table-bordered mb-0 w-100" id="item_table">
                <thead class="table-dark">
                    <tr>
                        <th>Asset Code</th>
                        <th>Asset Area</th>
                        <th>Asset Section</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><? $model->fault_asset_id; ?></td>
                        <td><? $model->fault_area; ?></td>
                        <td><? $model->fault_section; ?></td>
                    </tr>
                </tbody>
            </table>
            </fieldset>
            -->
        <!--    <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Fault Details:</legend>-->
        <!--<? $form->field($model, 'status')->hiddenInput()->label(false) ?>-->
            <table class="table table-bordered mb-0" id="item_table">
            <thead class="table-dark">
                <tr>
                    <!--<th class="text-center" width="5%">No.</th>-->
                    <th class="text-center">Asset ID</th>
                    <th class="text-center" width="15%">Fault Type</th>
                    <th class="text-center">Primary Description</th>
                    <th class="text-center">Secondary Description</th>
                    <!--<th width="15%">Machine Breakdown Type</th>-->
                    <th class="text-center" width="20%">Fault Priority</th>
                    <th class="text-center" width="15%">Photos</th>
                    <th class="text-left" width="30%">Remark</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <tr>
                    <td>
                        <?= $model->fault_asset_id; ?>
                        <?= Html::a(
                            '<i class="bi bi-eye"></i>',
                            'javascript:void(0);',
                            [
                                'class' => 'modalButtonSingle btn btn-sm btn-success',
                                'id' => 'view-asset-btn',
                                'data-url' => Url::to(['view-asset-details']), 
                                'data-back-url' => Url::to(['fault-form-modal', 'id' => $model->id ?? null]),
                                'aria-disabled' => 'false',
                            ]
                        ); ?>
                    </td>
                    <td><?= $model->fault_type; ?></td>
                    <td><?= $model->fault_primary_detail; ?></td>
                    <td><?= $model->fault_secondary_detail; ?></td>
                    <td><?= $model->machinePriority->name; ?></td>
                    <td>
                        <?php if (!empty($model->cmmsMachinePhotos)): ?>
                            <div class="existing-photos d-flex flex-wrap gap-2">
                            <?php foreach ($model->cmmsMachinePhotos as $photo): ?>
                                <?php if (!$photo->is_deleted): ?>
                                    <div
                                        class="photo-item d-inline-block me-2 mb-2"
                                        data-photo-id="<?= $photo->id ?>"
                                    >
                                        <a href="<?= $photo->getUrl() ?>" target="_blank">
                                            <img
                                                src="<?= $photo->getUrl() ?>"
                                                class="img-thumbnail"
                                                style="width:80px; height:80px; object-fit:cover;"
                                            >
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <?php endif; ?>
                        <!--</div>-->
                        <!--preview container for new uploads-->
                        <div class="new-photo-preview mt-2"></div>
                        <div class="delete-photo-inputs"></div>
                    </td>
                    <td><?= $model->additional_remarks;?></td>
                </tr>
            </tbody>
            </table>
            <!--</fieldset>-->
        </div>
        <!--Asset Details container-->
        <div id="asset-details-container" style="display: none">
            <div class="d-flex justify-content-between mb-3">
                <!--<h5>Asset Details</h5>-->
                <button class="btn btn-secondary btn-sm" id="back-to-fault-list">
                    Back to Fault List
                </button>
            </div>
            <div id="asset-details-content">
            </div>
        </div>
    </div>

<script>
    $(document).on('click', '#view-asset-btn', function (e) {
        e.preventDefault();

        const $btn = $(this);
        if ($btn.hasClass('disabled')) return;

        const assetId = <?= json_encode($model->fault_asset_id) ?>;
        if (!assetId) return;
        
        const modelId = <?= json_encode($model->id) ?>;
        
        const url = $btn.data('url');

        $('#fault-form-container').fadeOut(150, function () {
            $('#asset-details-container').fadeIn(150);
            $('#asset-details-content').html('<div class="text-muted">Loading...</div>');
        });
        
        $.get(url, { asset_id: assetId, model_id: modelId })
            .done(function (html) {
                $('#asset-details-content').fadeOut(100, function () {
                    $(this).html(html).fadeIn(100);
                });
            })
            .fail(function (xhr) {
                $('#asset-details-content').html(
                    '<div class="alert alert-danger">Failed to load asset details</div>'
                );
                console.error(xhr.responseText);
            });
    });
    
    $(document).on('click', '#back-to-fault-list', function () {
        $('#asset-details-container').fadeOut(150, function () {
            $('#asset-details-content').empty();
            $('#fault-form-container').fadeIn(150);
        });
    });
</script>