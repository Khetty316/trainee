<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use frontend\models\cmms\CmmsAssetList;
use frontend\models\cmms\RefProgressStatus;
use common\models\User;

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
    $key = $model->id !== null
        ? $model->id
        : uniqid('new_', true);
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
                Corrective Work Order - <?= Html::encode($model->id ?? 'New') ?>
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
                'action' => ['update', 'id' => $model->id, 'moduleStatus' => $moduleStatus],
                'options' => [
                            'autocomplete' => 'off',
                            'enctype' => 'multipart/form-data'
                        ],
            ]); ?>
            <table class="table table-bordered align-middle" id="item_table">
            <thead class="table-dark text-center">
                <tr>
                    <th>Progress Status</th>
                    <th class="text-center" width="15%">Start Date</th>
                    <th class="text-center" width="15%">End Date</th>
                    <th class="text-center">Duration</th>
                    <th>Assigned Technician(s)</th>
                    <th class="text-left" width="30%">Remarks</th>
                </tr>
            </thead>
            <tbody id="listTBody">
                <?= Html::activeHiddenInput(
                    $model,
                    "id"
                ) ?>
                <tr>
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
                                    ['prompt' => 'Select status']
                            )
                        ?>
                    </td>
                    <td><?= $form->field($model, 'start_date')->input('date', [
                        'required' => true,
                        'value' => $model->start_date
                            ? Yii::$app->formatter->asDate($model->start_date, 'php:Y-m-d') : null,
                        ])->label(false); ?></td>
                    <td><?= $form->field($model, 'end_date')->input('date', [
                        'value' => $model->start_date
                            ? Yii::$app->formatter->asDate($model->end_date, 'php:Y-m-d') : null,
                    ])->label(false); ?></td>
                    <td><?= $model->duration ?></td>
                    <td>
                        <?php if ($moduleStatus === 'superior'): ?>
                            <div id="tech-wrapper">
                                <?php 
                                    $users = User::find()
                                            ->select(['id', 'fullname'])
        //                                    ->where(['<>', 'id', Yii::$app->user->id])
                                            ->where(['<>', 'id', '<>'])
                                            ->asArray()
                                            ->all();
                                ?>
                                <?php foreach ($assignedPICs as $a => $assigned_PIC): ?>
                                    <div class="tech-row mb-1">
                                        <?=
                                            $form->field($assigned_PIC, "[$a]name")->textInput([
                                                'class' => 'form-control pic-name',
                                                'list' => 'users'
                                            ])->label(false)
                                        ?>

                                        <?= yii\helpers\Html::activeHiddenInput($assigned_PIC, "[$a]id", [
                                            'class' => 'pic-id',
                                        ]) ?>

                                        <button type="button" class="btn btn-danger btn-sm remove-tech">
                                            x
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary" id="add-tech">
                                + Add Technician
                            </button>
                            <datalist id="users">
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= Html::encode($user['fullname']) ?>"
                                            data-id="<?= $user['id'] ?>">
                                <?php endforeach; ?>
                            </datalist>
                        <?php else: ?>
                            <?php foreach ($assignedPICs as $a => $assigned_PIC): ?>
                                <div class="tech-row mb-1">
                                    <?= $assigned_PIC->name;?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $form->field($model, 'remarks')->label(false); ?></td>
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
    window.techIndex = window.techIndex ?? <?= (int)count($assignedPICs) ?>; // start index from existing assigned PICs
    
    $(document).off('click', '#add-tech').on('click', '#add-tech', function () {
       const techIndex = window.techIndex;
       
       $('#tech-wrapper').append(`
            <div class="tech-row mb-1">
                <input type="text"
                        name="RefAssignedPic[${techIndex}][name]"
                        class="form-control pic-name"
                        list="users">
                
                <input type="hidden"
                       name="RefAssignedPic[${techIndex}][id]"
                       class="pic-id">
                
                <button type="button"
                        class="btn btn-danger btn-sm remove-tech">X</button>
            </div>
        `);
         window.techIndex++;
    });
//    document.getElementById('add-tech').addEventListener('click', function () { 
//        document.getElementById('tech-wrapper').insertAdjacentHTML(
//            'beforeend',
//            `
//            <div class="tech-row mb-1">
//                <input type="text"
//                       name="RefAssignedPic[${techIndex}][name]"
//                       class="form-control"
//                       list="users"> <!-- add this to connect to your datalist -->
//
//                <input type="hidden"
//                       name="RefAssignedPic[${techIndex}][id]">
//
//                <button type="button"
//                        class="btn btn-danger btn-sm remove-tech">x</button>
//            </div>
//            `
//        );
//
//        techIndex++;
//    });
    
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-tech')) {
            e.target.closest('.tech-row').remove();
        }
    });
//    function getCsrfToken() {
//        return $('meta[name="csrf-token"]').attr('content');
//    }
//
//    $(document).on('click', '.remove-tech', function () {
//        const row = $(this).closest('.tech-row');
//        const nameVal = row.find('input.pic-name').val().trim();
//        const picId = row.find('input.pic-id').val();
//        
//        if (nameVal === '') {
//            row.remove();
//            return;
//        }
//        
//        if (!picId) {
//            row.remove();
//            return;
//        }
//        
//        if (!confirm('Remove this technician?'))    return;
//        
//        $.ajax({
//            url: "<? Url::to(['remove-pic']) ?>",
//            type: "POST",
//            data: { picID: picId, _csrf: getCsrfToken() },
//            success: function () { row.remove(); },
//            error: function () { alert('Failed to remove technician.'); },
//        });
//    });
//    
//    $(document).on('change', '.pic-name', function () {
//        const row = $(this).closest('.tech-row');
//        const chosen = $(this).val();
//        const opt = $('#users option').filter(function() { return $(this).val() === chosen; });
//        row.find('.pic-id').val(opt.data('id') || '');
//    });
</script>
