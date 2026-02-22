<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hr\HrPayslipSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hr-payslip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'pay_year') ?>

    <?= $form->field($model, 'pay_month') ?>

    <?= $form->field($model, 'basic_salary') ?>

    <?php // echo $form->field($model, 'bonus') ?>

    <?php // echo $form->field($model, 'commission') ?>

    <?php // echo $form->field($model, 'director_fee') ?>

    <?php // echo $form->field($model, 'epf') ?>

    <?php // echo $form->field($model, 'socso') ?>

    <?php // echo $form->field($model, 'eis_sip') ?>

    <?php // echo $form->field($model, 'income_tax') ?>

    <?php // echo $form->field($model, 'unpaid_leave') ?>

    <?php // echo $form->field($model, 'annual_leave_pay') ?>

    <?php // echo $form->field($model, 'net_salary') ?>

    <?php // echo $form->field($model, 'employer_epf') ?>

    <?php // echo $form->field($model, 'employer_socso') ?>

    <?php // echo $form->field($model, 'employer_eis_sip') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
