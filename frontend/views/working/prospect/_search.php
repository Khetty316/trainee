<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProspectMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prospect-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'proj_code') ?>

    <?= $form->field($model, 'title_short') ?>

    <?= $form->field($model, 'title_long') ?>

    <?= $form->field($model, 'due_date') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'staff_pic') ?>

    <?php // echo $form->field($model, 'other_pic') ?>

    <?php // echo $form->field($model, 'project_type') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
