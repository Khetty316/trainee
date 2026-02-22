<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\claim\ClaimsDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="claims-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'claims_detail_id') ?>

    <?= $form->field($model, 'claim_master_id') ?>

    <?= $form->field($model, 'claim_type') ?>

    <?= $form->field($model, 'date1') ?>

    <?= $form->field($model, 'date2') ?>

    <?php // echo $form->field($model, 'company_name') ?>

    <?php // echo $form->field($model, 'receipt_no') ?>

    <?php // echo $form->field($model, 'detail') ?>

    <?php // echo $form->field($model, 'project_account') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'receipt_lost') ?>

    <?php // echo $form->field($model, 'filename') ?>

    <?php // echo $form->field($model, 'is_submitted') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <?php // echo $form->field($model, 'update_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
