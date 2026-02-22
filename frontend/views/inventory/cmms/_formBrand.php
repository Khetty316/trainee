<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\cmms\InventorySupplierCmms */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-supplier-cmms-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?=
            $form->field($model, 'active_sts')->dropDownList([
                '0' => 'No',
                '1' => 'Yes'
            ])
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
