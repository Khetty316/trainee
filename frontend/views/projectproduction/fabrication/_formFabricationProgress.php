<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */
/* @var $form yii\widgets\ActiveForm */
?>
<script src='/js/bootstrap-input-spinner.js'></script>
<div class="container-fluid">
    <div class="row">
        <?php
        $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'options' => ['autocomplete' => 'off', 'class' => 'col-12'],
                    'enableClientValidation' => false
        ]);
        ?>
        <div class="hidden">

            <?php
            $inputTypeOption = ['type' => 'number', 'step' => '1', 'class' => 'text-right form-control form-control-sm addSpinner'];
            $fieldOption = ['options' => ['class' => 'form-group row my-0']];
            $panelQuantity = $model->panel->quantity;
            ?>
        </div>

        <!--
        $cutnpunchAdd, $bendAdd, $weldngrindAdd, $powcoatAdd, $assemblingAdd,
        $dispatchAdd, $partialDispatchAdd;
        -->
        <table class='table table-borderless table-striped table-sm'>
            <tr>
                <th class='col-5'>Process</th>
                <th class='col-1 text-right'>Completed</th>
                <th class='col-5 text-center'>Add on</th>
            </tr>
            <tr>
                <td>Cut & Punch</td>
                <td class='text-right'><?= $model->cutnpunch ?></td>
                <td> <?= $form->field($model, 'cutnpunchAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->cutnpunch, 'max' => $panelQuantity - $model->cutnpunch]))->label(false) ?></td>
            </tr>
            <tr>
                <td>Bending</td>
                <td class='text-right'><?= $model->bend ?></td>
                <td> <?= $form->field($model, 'bendAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->bend, 'max' => $panelQuantity - $model->bend]))->label(false) ?></td>
            </tr>
            <tr>
                <td>Welding & Grinding</td>
                <td class='text-right'><?= $model->weldngrind ?></td>
                <td> <?= $form->field($model, 'weldngrindAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->weldngrind, 'max' => $panelQuantity - $model->weldngrind]))->label(false) ?></td>
            </tr>
            <tr>
                <td>Power Coating</td>
                <td class='text-right'><?= $model->powcoat ?></td>
                <td> <?= $form->field($model, 'powcoatAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->powcoat, 'max' => $panelQuantity - $model->powcoat]))->label(false) ?></td>
            </tr>
            <tr>
                <td>Assembling</td>
                <td class='text-right'><?= $model->assembling ?></td>
                <td> <?= $form->field($model, 'assemblingAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->assembling, 'max' => $panelQuantity - $model->assembling]))->label(false) ?></td>
            </tr>
            <tr>
                <td>Dispatch</td>
                <td class='text-right'><?= $model->dispatch ?></td>
                <td> <?= $form->field($model, 'dispatchAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->dispatch, 'max' => $panelQuantity - $model->dispatch]))->label(false) ?></td>
            </tr>
            <tr>
                <td>Partially Dispatch</td>
                <td class='text-right'><?= $model->partial_dispatch ?></td>
                <td> <?= $form->field($model, 'partialDispatchAdd', $fieldOption)->textInput(array_merge($inputTypeOption, ['min' => -$model->partial_dispatch, 'max' => $panelQuantity - $model->partial_dispatch]))->label(false) ?></td>
            </tr>
        </table>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
    $(function () {
        $(".addSpinner").inputSpinner();
    });
</script>
