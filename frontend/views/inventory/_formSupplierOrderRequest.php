<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-supplier-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($orderRequest, 'inventory_model_id')->textInput(['maxlength' => true, 'disabled' => true, 'value' => $orderRequest->inventoryModel->type])->label('Model') ?>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($orderRequest, 'inventory_brand_id')->textInput(['maxlength' => true, 'disabled' => true, 'value' => $orderRequest->inventoryModel->inventoryBrand->name])->label('Brand') ?>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <?=
                    $form->field($orderRequest, 'inventory_detail_id')
                    ->dropDownList($supplierList, ['prompt' => 'Select Supplier'])->label('Supplier')
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
