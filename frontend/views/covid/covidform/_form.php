<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\form\CovidStatusForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="covid-status-form-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'body_temperature')->textInput() ?>

    <?= $form->field($model, 'self_vaccine_dose')->textInput() ?>

    <?= $form->field($model, 'self_symptom_list')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'self_symptom_other')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'self_place_list')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'self_place_other')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'self_test_is')->textInput() ?>

    <?= $form->field($model, 'self_test_date')->textInput() ?>

    <?= $form->field($model, 'self_test_reason')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'self_test_kit_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'self_test_kit_office_is')->textInput() ?>

    <?= $form->field($model, 'self_covid_kit_id')->textInput() ?>

    <?= $form->field($model, 'other_how_many')->textInput() ?>

    <?= $form->field($model, 'other_vaccine_two_dose')->textInput() ?>

    <?= $form->field($model, 'other_symptom_list')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_symptom_other')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_place_list')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_place_other')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_test_is')->textInput() ?>

    <?= $form->field($model, 'other_test_reason')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
