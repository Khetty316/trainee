<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\hrdoc\HrEmployeeDocuments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hr-employee-documents-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hr_doctype')->textInput() ?>

    <?= $form->field($model, 'employee_id')->textInput() ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'active_sts')->textInput() ?>

    <?= $form->field($model, 'is_read')->textInput() ?>

    <?= $form->field($model, 'read_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
