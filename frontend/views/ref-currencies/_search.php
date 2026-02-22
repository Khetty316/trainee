<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\common\RefCurrenciesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ref-currencies-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'currency_id') ?>

    <?= $form->field($model, 'currency_name') ?>

    <?= $form->field($model, 'currency_code') ?>

    <?= $form->field($model, 'currency_sign') ?>

    <?= $form->field($model, 'exchange_rate') ?>

    <?php // echo $form->field($model, 'active') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
