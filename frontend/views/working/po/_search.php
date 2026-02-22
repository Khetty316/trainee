<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\po\PurchaseOrderMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-order-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'po_id') ?>

    <?= $form->field($model, 'po_number') ?>

    <?= $form->field($model, 'po_date') ?>

    <?= $form->field($model, 'project_code') ?>

    <?= $form->field($model, 'po_material_desc') ?>

    <?php // echo $form->field($model, 'po_lead_time') ?>

    <?php // echo $form->field($model, 'po_etd') ?>

    <?php // echo $form->field($model, 'po_transporter') ?>

    <?php // echo $form->field($model, 'po_pic') ?>

    <?php // echo $form->field($model, 'po_address') ?>

    <?php // echo $form->field($model, 'po_receive_status') ?>

    <?php // echo $form->field($model, 'po_upload_file') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'update_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
