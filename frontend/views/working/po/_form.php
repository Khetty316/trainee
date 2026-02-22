<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\po\PurchaseOrderMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="purchase-order-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'po_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'po_date')->textInput() ?>

    <?= $form->field($model, 'project_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'po_material_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'po_lead_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'po_etd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'po_transporter')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'po_pic')->textInput() ?>

    <?= $form->field($model, 'po_address')->textInput() ?>

    <?= $form->field($model, 'po_receive_status')->textInput() ?>

    <?= $form->field($model, 'po_upload_file')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
