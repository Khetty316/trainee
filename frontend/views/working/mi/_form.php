<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\MasterIncomings */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="master-incomings-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'index_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uploader_id')->textInput() ?>

    <?= $form->field($model, 'doc_type_id')->textInput() ?>

    <?= $form->field($model, 'sub_doc_type_id')->textInput() ?>

    <?= $form->field($model, 'doc_due_date')->textInput() ?>


    <?= $form->field($model, 'reference_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'particular')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isUrgent')->textInput() ?>

    <?= $form->field($model, 'isPerforma')->textInput() ?>

    <?= $form->field($model, 'file_type_id')->textInput() ?>

    <?= $form->field($model, 'received_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remarks')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'requestor_id')->textInput() ?>

    <?= $form->field($model, 'current_step')->textInput() ?>

    <?= $form->field($model, 'current_step_task_id')->textInput() ?>

    <?= $form->field($model, 'mi_status')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
