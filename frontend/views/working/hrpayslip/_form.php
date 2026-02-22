<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hr\HrPayslip */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hr-payslip-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'pay_year')->textInput() ?>

    <?= $form->field($model, 'pay_month')->textInput() ?>

    <?= $form->field($model, 'basic_salary')->textInput() ?>

    <?= $form->field($model, 'bonus')->textInput() ?>

    <?= $form->field($model, 'commission')->textInput() ?>

    <?= $form->field($model, 'director_fee')->textInput() ?>

    <?= $form->field($model, 'epf')->textInput() ?>

    <?= $form->field($model, 'socso')->textInput() ?>

    <?= $form->field($model, 'eis_sip')->textInput() ?>

    <?= $form->field($model, 'income_tax')->textInput() ?>

    <?= $form->field($model, 'unpaid_leave')->textInput() ?>

    <?= $form->field($model, 'annual_leave_pay')->textInput() ?>

    <?= $form->field($model, 'net_salary')->textInput() ?>

    <?= $form->field($model, 'employer_epf')->textInput() ?>

    <?= $form->field($model, 'employer_socso')->textInput() ?>

    <?= $form->field($model, 'employer_eis_sip')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
