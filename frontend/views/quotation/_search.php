<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quotation-masters-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'requestor_id') ?>

    <?= $form->field($model, 'project_code') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'proc_approval') ?>

    <?php // echo $form->field($model, 'proc_remark') ?>

    <?php // echo $form->field($model, 'proc_approve_by') ?>

    <?php // echo $form->field($model, 'requestor_approval') ?>

    <?php // echo $form->field($model, 'requestor_remark') ?>

    <?php // echo $form->field($model, 'requestor_approve_by') ?>

    <?php // echo $form->field($model, 'manager_approval') ?>

    <?php // echo $form->field($model, 'manager_remark') ?>

    <?php // echo $form->field($model, 'manager_approve_by') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
