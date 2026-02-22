<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\documentreminder\DocumentReminderMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-reminder-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'category') ?>

    <?= $form->field($model, 'active_sts') ?>

    <?= $form->field($model, 'description') ?>

    <?= $form->field($model, 'filename') ?>

    <?php // echo $form->field($model, 'expiry_date') ?>

    <?php // echo $form->field($model, 'remind_date') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
