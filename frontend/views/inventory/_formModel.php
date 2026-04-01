<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventorySupplier */
/* @var $form yii\widgets\ActiveForm */
$brandList = frontend\models\inventory\InventoryBrand::getAllDropDownBrandList();
?>

<div class="inventory-supplier-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($model, 'type')->textInput(['maxlength' => true, 'required' => true]) ?>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($model, 'group')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?= $form->field($model, 'unit_type')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <?=
                    $form->field($model, 'scannedFile')
                    ->fileInput([
                        'class' => 'form-control',
                        'accept' => '.png, .jpg, .jpeg, .pdf',
                    ])
            ?>

            <?php if (!$model->isNewRecord && $model->image): ?>
                <div class="current-file mt-2 mb-3">
                    <strong>Current file:</strong> 
                    <?php
                    $extension = pathinfo($model->image, PATHINFO_EXTENSION);
                    // Use the serve-file action to display the file
                    $imageUrl = yii\helpers\Url::to(['get-model-image', 'filename' => $model->image]);

                    if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])):
                        ?>
                        <br>
                        <img src="<?= $imageUrl ?>" 
                             alt="Current Image" 
                             style="max-width: 500px; max-height: 500px; margin-top: 10px;">
                    <?php elseif (strtolower($extension) === 'pdf'): ?>
                        <a href="<?= $imageUrl ?>" target="_blank">
                            <?= Html::encode($model->image) ?>
                        </a>
                    <?php else: ?>
                        <a href="<?= $imageUrl ?>" target="_blank">
                            <?= Html::encode($model->image) ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <?=
            $form->field($model, 'inventory_brand_id')->dropDownList($brandList)
            ?>
        </div>
        <div class="col-lg-12 col-md-6 col-sm-12">
            <?=
            $form->field($model, 'active_sts')->dropDownList([
                '2' => 'Yes',
                '1' => 'No',
            ])
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
