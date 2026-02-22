<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\form\CovidStatusFormSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="covid-status-form-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'body_temperature') ?>

    <?= $form->field($model, 'self_vaccine_dose') ?>

    <?php // echo $form->field($model, 'self_symptom_list') ?>

    <?php // echo $form->field($model, 'self_symptom_other') ?>

    <?php // echo $form->field($model, 'self_place_list') ?>

    <?php // echo $form->field($model, 'self_place_other') ?>

    <?php // echo $form->field($model, 'self_test_is') ?>

    <?php // echo $form->field($model, 'self_test_date') ?>

    <?php // echo $form->field($model, 'self_test_reason') ?>

    <?php // echo $form->field($model, 'self_test_kit_type') ?>

    <?php // echo $form->field($model, 'self_test_kit_office_is') ?>

    <?php // echo $form->field($model, 'self_covid_kit_id') ?>

    <?php // echo $form->field($model, 'other_how_many') ?>

    <?php // echo $form->field($model, 'other_vaccine_two_dose') ?>

    <?php // echo $form->field($model, 'other_symptom_list') ?>

    <?php // echo $form->field($model, 'other_symptom_other') ?>

    <?php // echo $form->field($model, 'other_place_list') ?>

    <?php // echo $form->field($model, 'other_place_other') ?>

    <?php // echo $form->field($model, 'other_test_is') ?>

    <?php // echo $form->field($model, 'other_test_reason') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
