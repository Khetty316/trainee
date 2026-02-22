<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\testkit\CovidTestkitInventorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="covid-testkit-inventory-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'brand') ?>

    <?= $form->field($model, 'record_date') ?>

    <?= $form->field($model, 'total_movement') ?>

    <?= $form->field($model, 'giving_to') ?>

    <?php // echo $form->field($model, 'confirm_status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
