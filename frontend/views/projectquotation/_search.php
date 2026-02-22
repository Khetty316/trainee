<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQMastersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-qmasters-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'quotation_no') ?>

    <?= $form->field($model, 'project_code') ?>

    <?= $form->field($model, 'project_name') ?>

    <?= $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'project_coordinator') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
