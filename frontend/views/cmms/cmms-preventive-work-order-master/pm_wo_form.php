<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\models\cmms\CmmsAssetList;
use frontend\models\cmms\RefProgressStatus;
use common\models\User;

$this->title = 'PM Schedule';
$view = 'view-assigned-tasks';
if ($moduleIndex === 'superior') {
    $view = 'view-superior';
}
$this->params['breadcrumbs'][] = ['label' => 'Preventive Work Order Form', 'url' => [$view]];
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsFaultList */
/* @var $form yii\widgets\ActiveForm */
?>
<!--<php
//    use yii\bootstrap4\Modal;
//
//    Modal::begin([
//        'id' => 'modalSingle',
//        'size' => Modal::SIZE_LARGE,
//    ]);
//    echo '<h5 id="modalSingleTitle"></h5>';
//    echo '<div id="modalSingleContent"></div>';
//    Modal::end();
?>-->
<!--<php $key = $model->id ?? 'new'; ?>-->
<?php
$key = $model->id !== null ? $model->id : uniqid('new_', true);
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
<!--<div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
        <div class="modal-body">-->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                PM Work Order - <?= Html::encode($model->id ?? 'New') ?>
            </h6>
            <p>
                Asset ID: <?= $model->cmmsAssetList->asset_id; ?>
                <br>
                Asset Name: <?= $model->cmmsAssetList->name; ?>
            </p>
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

            <?php
            $form = ActiveForm::begin([
                'id' => 'fault-form',
                'action' => ['pm-wo-form', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                'options' => [
                    'autocomplete' => 'off',
                    'enctype' => 'multipart/form-data'
                ],
            ]);
            ?>
            <table class="table table-bordered align-middle" id="item_table">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Date</th>
                        <th class="text-center" width="15%">Start Time</th>
                        <th class="text-center" width="15%">End Time</th>
                        <th class="text-center">Details</th>
    <!--                    <th>Part List</th>
                        <th>Tool List</th>-->
                        <th>Part/Tool List</th>
                        <th>Fault ID (if any)</th>
                        <th>Work Progress Status</th>
                        <th>Reviewed By</th>
                    </tr>
                </thead>
                <tbody id="listTBody">
    <!--                <? Html::activeHiddenInput(
                        $model,
                        "id"
                    ) ?>-->
                    <tr>
                        <td></td>
                        <td>
                            <?= Html::encode($model->start_time) ?>
                            <!--<? $model->start_time ? Yii::$app->formatter->asDatetime($model->start_time, 'php:Y-m-d H:i:s') : '' ?>-->
                            <?= $form->field($model, 'start_time')->hiddenInput(['id' => 'start-time'])->label(false) ?>
                        </td>
                        <td><?= $model->end_time ?></td>
                        <td>
                            <div class="report-fault">
                                <?=
                                Html::a(
                                        'Add/Edit Details',
                                        'javascript:void(0);',
                                        [
                                            'class' => 'modalButtonSingle btn btn-sm btn-success',
                                            'data-url' => Url::to([
                                                'create-update-maintenance-details',
                                                'id' => $model->id,
                                                'moduleIndex' => $moduleIndex
                                                    //                                    'id' => $model->cmmsFaultListDetails[0]->cmms_asset_list_id ?? null
                                            ]),
                                            'data-modaltitle' => 'Maintenance Procedure Details Form',
                                        ]
                                );
                                ?>
                        </td>
                        <td class="text-center">
                            <?=
                            Html::a(
                                    '<i class="fas fa-external-link-alt"></i>',
                                    ['view-selected-material', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                                    ['class' => 'text-primary mx-1']);
                            ?>

                        </td>

                        <td>
                            <div class="report-fault">
                                <?=
                                Html::a(
                                        'Report Fault',
                                        'javascript:void(0);',
                                        [
                                            'class' => 'modalButtonSingle btn btn-sm btn-success',
                                            'data-url' => Url::to([
                                                'report-fault',
                                                'id' => $model->id,
                                                'assetCode' => $model->cmmsAssetList->asset_id,
                                                'moduleIndex' => $moduleIndex
                                                    //                                    'id' => $model->cmmsFaultListDetails[0]->cmms_asset_list_id ?? null
                                            ]),
                                            'data-modaltitle' => 'Fault Details Form',
                                        ]
                                );
                                ?>
                            </div>
                        </td>
                        <td>
                            <?php
                            $progressStatusList = ArrayHelper::map(
                                    RefProgressStatus::find()
                                            ->where(['active_sts' => 1])
//                                    ->orderBy('name')
                                            ->all(),
                                    'id', 'name'
                            );
                            ?>
                            <?=
                            $form->field($model, 'progress_status_id')->dropDownList(
                                    $progressStatusList,
                                    [
                                        'prompt' => 'Select status',
                                        'id' => 'progress-status'
                                    ]
                            )->label(false)
                            ?>
                        </td>
                        <td>
                            <?php if ($moduleIndex === 'superior'): ?>
                                <?php
                                $users = User::find()
                                        ->select(['id', 'fullname'])
                                        //                                    ->where(['<>', 'id', Yii::$app->user->id])
                                        ->where(['<>', 'id', '<>'])
                                        ->asArray()
                                        ->all();
                                ?>
                                <?php
                                $placeholder = User::find()
                                        ->select('fullname')
                                        ->where(['id' => $model->assigned_by])
                                        ->scalar();
                                ?>
                                <?=
                                $form->field($model, "reviewed_by_name")->textInput([
                                    'value' => $placeholder ?? Yii::$app->user->identity->fullname,
                                    'list' => 'users',
                                    'class' => 'reviewed-by-name',
                                    'data-row' => $model->id,
                                    'style' => 'width:100%; min-width:150px;'
                                ])->label(false);
                                ?>

                                <?=
                                        $form->field($model, "assigned_by")
                                        ->hiddenInput([
                                            'class' => 'reviewed-by-id',
                                            'data-row' => $model->id,
                                        ])
                                        ->label(false);
                                ?>
                                <datalist id="users">
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= Html::encode($user['fullname']) ?>"
                                                data-id="<?= $user['id'] ?>">
                                                <?php endforeach; ?>
                                </datalist>
                                <?php
                                $jsUsers = json_encode($users);
                                $this->registerJs(<<<JS
                                    (function () {
                                      const users = $jsUsers;

                                      // Build lookup map: fullname -> id
                                      const nameToId = new Map();
                                      users.forEach(u => {
                                        if (u.fullname != null) nameToId.set(String(u.fullname), String(u.id));
                                      });

                                      function syncForRow(rowId) {
                                        const nameInput = document.querySelector('.reviewed-by-name[data-row="' + rowId + '"]');
                                        const idInput   = document.querySelector('.reviewed-by-id[data-row="' + rowId + '"]');
                                        if (!nameInput || !idInput) return;

                                        const id = nameToId.get(nameInput.value) || '';
                                        idInput.value = id;
                                      }

                                      // Sync on change/blur for ANY row (event delegation)
                                      document.addEventListener('change', function(e) {
                                        if (!e.target.classList.contains('reviewed-by-name')) return;
                                        const rowId = e.target.dataset.row;
                                        if (rowId) syncForRow(rowId);
                                      });

                                      document.addEventListener('blur', function(e) {
                                        if (!e.target.classList.contains('reviewed-by-name')) return;
                                        const rowId = e.target.dataset.row;
                                        if (rowId) syncForRow(rowId);
                                      }, true);

                                      // IMPORTANT: initial sync on page load (because you set default fullname)
                                      document.querySelectorAll('.reviewed-by-name[data-row]').forEach(input => {
                                        syncForRow(input.dataset.row);
                                      });
                                    })();
                                    JS);
                                ?>
                            <?php else: ?>
                                <?php $reviewer = User::findOne(['id' => $model->assigned_by]); ?>
                                <?= Html::encode($reviewer->fullname) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?=
            Html::submitButton('Save', [
                'id' => 'save-btn',
                'class' => 'float-right btn btn-success'
            ])
            ?>
            <?php ActiveForm::end(); ?>
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
</div>
<?php
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
?>
<script>
    $(document).on('click', 'a.modalButtonSingle', function (e) {
        e.preventDefault();

        const url = $(this).attr('data-url');
        const title = $(this).data('modaltitle');

        $('#myModal').modal('show')             // <-- point to existing modal
                .find('#myModalContent')
                .load(url);
    });

    $('#progress-status').on('change', function () {

        const selectedText = $("#progress-status option:selected").text();

        if (selectedText === 'Acknowledged') {

            const now = new Date();

            const formatted =
                    now.getFullYear() + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    String(now.getDate()).padStart(2, '0') + ' ' +
                    String(now.getHours()).padStart(2, '0') + ':' +
                    String(now.getMinutes()).padStart(2, '0') + ':' +
                    String(now.getSeconds()).padStart(2, '0');

            $('#start-time').val(formatted);
        }

    });
//    window.techIndex = window.techIndex ?? <? (int)count($assignedPICs) ?>; // start index from existing assigned PICs
//    
//    $(document).off('click', '#add-tech').on('click', '#add-tech', function () {
//       const techIndex = window.techIndex;
//       
//       $('#tech-wrapper').append(`
//            <div class="tech-row mb-1">
//                <input type="text"
//                        name="RefAssignedPic[${techIndex}][name]"
//                        class="form-control pic-name"
//                        list="users">
//                
//                <input type="hidden"
//                       name="RefAssignedPic[${techIndex}][id]"
//                       class="pic-id">
//                
//                <button type="button"
//                        class="btn btn-danger btn-sm remove-tech">X</button>
//            </div>
//        `);
//         window.techIndex++;
//    });
//    
//    document.addEventListener('click', function (e) {
//        if (e.target.classList.contains('remove-tech')) {
//            e.target.closest('.tech-row').remove();
//        }
//    });
</script>
