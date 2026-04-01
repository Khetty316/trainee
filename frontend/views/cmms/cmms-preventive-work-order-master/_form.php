<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

use frontend\models\cmms\CmmsAssetList;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsPreventiveWorkOrderMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cmms-preventive-work-order-master-form">

    <?php $form = ActiveForm::begin(); ?>
    <table class="table table-bordered mb-0" id="asset_table">
        <thead class="table-dark">
            <tr>
                <th class="text-center" width="10%">Asset ID</th>
                <th class="text-center" width="10%">Start Time</th>
                <th class="text-center" width="10%">Frequency</th>
                <th class="text-center" width="10%">Remarks</th>
<!--                <th class="text-center" width="10%">Start Time</th>
                <th class="text-center" width="10%">End Time</th>-->
                <th class="text-center" width="10%">Assigned Technicians</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <?php $assetCodes = frontend\models\cmms\CmmsPreventiveWorkOrderMaster::getCmmsAssetCodes_by_Id(); ?>
                    <?= 
                        $form->field($model, 'cmms_asset_list_id')->dropDownList(
                                $assetCodes,
                                ['prompt' => 'Select asset ID']
                        )->label(false)
                    ?>
                </td>
                <td>
                    <?= $form->field($model, 'commencement_date')->input('date', [
                        'required' => true,
                        'value' => $model->commencement_date
                            ? Yii::$app->formatter->asDate($model->commencement_date, 'php:Y-m-d') : null,
                        ])->label(false) ?>
                </td>
                <td>
                    <?php 
                        $frequencyList = \frontend\models\cmms\RefFrequency::getActiveDropdownlist_by_id();
                    ?>
                    <?= $form->field($model, 'frequency_id')->dropDownList(
                            $frequencyList,
                            ['prompt' => 'Select maintenance frequency']
                    )->label(false) ?>
                </td>
                <td>
                    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true])->label(false) ?>
                </td>
                <!--
                <td>
                    <? $form->field($model, 'end_time')->input('date')->label(false) ?>
                </td>-->
                <td>
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
                </td>
            </tr>
            <!--<? $form->field($model, 'assigned_by')->textInput() ?>-->
        </tbody>
        <tfoot>
            <tr>
                <td colspan="12">      
<!--                        <a class='btn btn-primary' href='javascript:addRow()'> 
                            <i class="fas fa-plus-circle"></i></a>-->
                    <?=
                    Html::submitButton('Save', [
                        'id' => 'save-btn',
                        'class' => 'float-right btn btn-success'
                    ])
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>
    <?php ActiveForm::end(); ?>

</div>
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
    
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-tech')) {
            e.target.closest('.tech-row').remove();
        }
    });
</script>