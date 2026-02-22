<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\MasterIncomingsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="master-incomings-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'index_no') ?>

    <?= $form->field($model, 'uploader_id') ?>

    <?= $form->field($model, 'doc_type_id') ?>

    <?= $form->field($model, 'sub_doc_type_id') ?>

    <?php // echo $form->field($model, 'doc_due_date') ?>


    <?php // echo $form->field($model, 'reference_no') ?>

    <?php // echo $form->field($model, 'particular') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'isUrgent') ?>

    <?php // echo $form->field($model, 'isPerforma') ?>

    <?php // echo $form->field($model, 'file_type_id') ?>

    <?php // echo $form->field($model, 'received_from') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'filename') ?>

    <?php // echo $form->field($model, 'project_code') ?>

    <?php // echo $form->field($model, 'requestor_id') ?>

    <?php // echo $form->field($model, 'current_step') ?>

    <?php // echo $form->field($model, 'current_step_task_id') ?>

    <?php // echo $form->field($model, 'mi_status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
