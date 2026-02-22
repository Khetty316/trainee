<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetail */
/* @var $form ActiveForm */
?>
<div class="view">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'claims_detail_id') ?>
        <?= $form->field($model, 'claim_type') ?>
        <?= $form->field($model, 'date1') ?>
        <?= $form->field($model, 'amount') ?>
        <?= $form->field($model, 'claim_master_id') ?>
        <?= $form->field($model, 'receipt_lost') ?>
        <?= $form->field($model, 'is_submitted') ?>
        <?= $form->field($model, 'is_deleted') ?>
        <?= $form->field($model, 'created_by') ?>
        <?= $form->field($model, 'update_by') ?>
        <?= $form->field($model, 'date2') ?>
        <?= $form->field($model, 'created_at') ?>
        <?= $form->field($model, 'update_at') ?>
        <?= $form->field($model, 'company_name') ?>
        <?= $form->field($model, 'receipt_no') ?>
        <?= $form->field($model, 'detail') ?>
        <?= $form->field($model, 'filename') ?>
        <?= $form->field($model, 'project_account') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- view -->
