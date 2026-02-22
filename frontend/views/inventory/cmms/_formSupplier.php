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
        <div class="col-lg-4 col-md-6 col-sm-12">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12">
            <?= $form->field($model, 'address1')->textInput(['maxlength' => true, 'placeholder' => 'Address line 1'])->label('Address') ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'address2')->textInput(['maxlength' => true, 'placeholder' => 'Address line 2'])->label(false) ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'address3')->textInput(['maxlength' => true, 'placeholder' => 'Address line 3'])->label(false) ?>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'address4')->textInput(['maxlength' => true, 'placeholder' => 'Address line 4'])->label(false) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'contact_name')->textInput(['maxlength' => true]) ?>
        </div><div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'contact_number')->textInput(['maxlength' => true]) ?>
        </div> <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'contact_email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'contact_fax')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'agent_terms')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
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
