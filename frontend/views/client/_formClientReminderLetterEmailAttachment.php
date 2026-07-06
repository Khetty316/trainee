<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterEmailAttachment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-reminder-letter-email-attachment-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'email_id')->textInput() ?>
    <?= $form->field($model, 'file_name')->textInput(['maxlength' => true]) ?>
    <div class="form-group"><?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?> </div>
    <?php ActiveForm::end(); ?>
</div>
