<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="claims-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'claims_detail_id')->textInput() ?>

    <?= $form->field($model, 'claim_master_id')->textInput() ?>

    <?= $form->field($model, 'claim_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date1')->textInput() ?>

    <?= $form->field($model, 'date2')->textInput() ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receipt_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'detail')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'receipt_lost')->textInput() ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'is_submitted')->textInput() ?>

    <?= $form->field($model, 'is_deleted')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <?= $form->field($model, 'update_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
