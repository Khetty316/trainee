<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\leave\LeaveMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="leave-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'requestor_id')->textInput() ?>
    <?= $form->field($model, 'leave_type')->textInput() ?>
    <?= $form->field($model, 'superior_id')->textInput() ?>
    <?= $form->field($model, 'reason')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'start_date')->textInput() ?>
    <?= $form->field($model, 'start_section')->textInput() ?>
    <?= $form->field($model, 'end_date')->textInput() ?>
    <?= $form->field($model, 'end_section')->textInput() ?>
    <?= $form->field($model, 'total_days')->textInput() ?>
    <?= $form->field($model, 'leave_status')->textInput() ?>
    <?= $form->field($model, 'leave_confirm_year')->textInput() ?>
    <?= $form->field($model, 'leave_confirm_month')->textInput() ?>
    <?= $form->field($model, 'days_annual')->textInput() ?>
    <?= $form->field($model, 'days_unpaid')->textInput() ?>
    <?= $form->field($model, 'days_sick')->textInput() ?>
    <?= $form->field($model, 'days_others')->textInput() ?>
    <?= $form->field($model, 'confirm_flag')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
