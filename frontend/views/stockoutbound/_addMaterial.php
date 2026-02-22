<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="bomdetails-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => 'off']]); ?>

    <?= $form->field($model, 'model_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descriptions')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qty')->textInput(['type' => 'number', 'step' => '1', 'min' => 1]) ?>

    <?= $form->field($model, 'engineer_remark')->textInput(['maxlength' => true])->label('Remark') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
