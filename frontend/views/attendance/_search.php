<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\attendance\MonthlyAttendanceSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="monthly-attendance-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'month') ?>

    <?= $form->field($model, 'year') ?>

    <?= $form->field($model, 'total_days') ?>

    <?= $form->field($model, 'total_present') ?>

    <?php // echo $form->field($model, 'workday_present') ?>

    <?php // echo $form->field($model, 'unpaid_leave_present') ?>

    <?php // echo $form->field($model, 'rest_holiday_present') ?>

    <?php // echo $form->field($model, 'absent') ?>

    <?php // echo $form->field($model, 'leave_taken') ?>

    <?php // echo $form->field($model, 'late_in') ?>

    <?php // echo $form->field($model, 'early_out') ?>

    <?php // echo $form->field($model, 'miss_punch') ?>

    <?php // echo $form->field($model, 'short') ?>

    <?php // echo $form->field($model, 'sche') ?>

    <?php // echo $form->field($model, 'workday') ?>

    <?php // echo $form->field($model, 'workday_ot') ?>

    <?php // echo $form->field($model, 'holiday') ?>

    <?php // echo $form->field($model, 'holiday_ot') ?>

    <?php // echo $form->field($model, 'restday') ?>

    <?php // echo $form->field($model, 'restday_ot') ?>

    <?php // echo $form->field($model, 'unpaid_leave') ?>

    <?php // echo $form->field($model, 'unpaid_leave_ot') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
