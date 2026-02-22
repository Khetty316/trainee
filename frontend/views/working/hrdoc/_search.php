<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hrdoc\HrEmployeeDocumentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hr-employee-documents-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'hr_doctype') ?>

    <?= $form->field($model, 'employee_id') ?>

    <?= $form->field($model, 'filename') ?>

    <?= $form->field($model, 'active_sts') ?>

    <?php // echo $form->field($model, 'is_read') ?>

    <?php // echo $form->field($model, 'read_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
