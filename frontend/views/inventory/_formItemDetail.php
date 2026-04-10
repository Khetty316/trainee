<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

if ($moduleIndex === 'execStock') {
    $pageName = 'Inventory Master - Executive';
    $url = 'inventory/inventory/item-list?type=execStock';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Inventory Master - Assistant';
    $url = 'inventory/inventory/item-list?type=assistStock';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Inventory Master - Project Coordinator';
    $url = 'inventory/inventory/item-list?type=projcoorStock';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Inventory Master - Head of Maintenance';
    $url = 'inventory/inventory/item-list?type=maintenanceHeadStock';
}

$this->title = 'Item Detail';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = $pageName;
$this->params['breadcrumbs'][] = ['label' => 'Item List', 'url' => [$url]];
$this->params['breadcrumbs'][] = $model->code;
//$this->params['breadcrumbs'][] = $this->title;
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
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?= $form->field($vmodel, 'group')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <?= $form->field($vmodel, 'unit_type')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div> 
    <div class="form-row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <?= $form->field($vmodel, 'stock_in')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <?= $form->field($vmodel, 'stock_on_hand')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <?= $form->field($vmodel, 'stock_reserved')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <?= $form->field($vmodel, 'stock_out')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <?= $form->field($vmodel, 'stock_available')->textInput(['maxlength' => true, 'disabled' => true]) ?>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-12">
            <?= $form->field($vmodel, 'qty_pending_receipt')->textInput(['maxlength' => true, 'disabled' => true]) ?>
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
    </div>
    <div class="form-row">
        <div class="col-lg-4 col-md-6 col-sm-12 mt-3">
            <?=
            $form->field($model, 'active_sts')->dropDownList([
                '1' => 'No',
                '2' => 'Yes'
            ])
            ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success mt-3']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
