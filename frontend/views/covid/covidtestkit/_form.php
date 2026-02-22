<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\testkit\CovidTestkitInventory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="covid-testkit-inventory-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'record_date')->textInput() ?>

    <?= $form->field($model, 'total_movement')->textInput() ?>

    <?= $form->field($model, 'giving_to')->textInput() ?>

    <?= $form->field($model, 'confirm_status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
