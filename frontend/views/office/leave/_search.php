<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\leave\LeaveMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="leave-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'requestor_id') ?>

    <?= $form->field($model, 'leave_type') ?>

    <?= $form->field($model, 'superior_id') ?>

    <?= $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'start_date') ?>

    <?php // echo $form->field($model, 'start_section') ?>

    <?php // echo $form->field($model, 'end_date') ?>

    <?php // echo $form->field($model, 'end_section') ?>

    <?php // echo $form->field($model, 'total_days') ?>

    <?php // echo $form->field($model, 'leave_status') ?>

    <?php // echo $form->field($model, 'leave_confirm_year') ?>

    <?php // echo $form->field($model, 'leave_confirm_month') ?>

    <?php // echo $form->field($model, 'days_annual') ?>

    <?php // echo $form->field($model, 'days_unpaid') ?>

    <?php // echo $form->field($model, 'days_sick') ?>

    <?php // echo $form->field($model, 'days_others') ?>

    <?php // echo $form->field($model, 'confirm_flag') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
