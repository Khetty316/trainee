<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsStockDispatchMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cmms-stock-dispatch-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dispatch_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wo_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'wo_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'received_by')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'status_updated_at')->textInput() ?>

    <?= $form->field($model, 'trial_status')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
