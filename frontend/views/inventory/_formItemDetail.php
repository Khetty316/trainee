<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Item Detail';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Item List', 'url' => ['item-list']];
$this->params['breadcrumbs'][] = $model->code;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="inventory-item-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="form-row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-9 col-md-6 col-sm-12">
            <?= $form->field($vmodel, 'department_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-6">
            <?= $form->field($vmodel, 'supplier_display')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($vmodel, 'brand_display')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($vmodel, 'model_type')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($vmodel, 'model_description')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($vmodel, 'group')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($vmodel, 'unit_type')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?= $form->field($model, 'stock_on_hand')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="current-file mt-2">
                Model Image:
                <?php
                $extension = pathinfo($vmodel->image, PATHINFO_EXTENSION);
                // Use the serve-file action to display the file
                $imageUrl = yii\helpers\Url::to(['get-model-image', 'filename' => $vmodel->image]);
                if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])):
                    ?>
                    <br>
                    <img src="<?= $imageUrl ?>" 
                         alt="Current Image" 
                         style="max-width: 500px; max-height: 500px; margin-top: 10px;">
                <?php elseif (strtolower($extension) === 'pdf'): ?>
                    <a href="<?= $imageUrl ?>" target="_blank">
                        <?= Html::encode($vmodel->image) ?>
                    </a>
                <?php else: ?>
                    <a href="<?= $imageUrl ?>" target="_blank">
                        <?= Html::encode($vmodel->image) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <?=
            $form->field($model, 'active_sts')->dropDownList([
                '1' => 'No',
                '2' => 'Yes'
            ])
            ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
