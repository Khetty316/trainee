<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefAssetCategory;
use frontend\models\common\RefAssetCondition;
use frontend\models\common\RefAssetOwnType;
use common\models\User;
use frontend\models\common\RefAssetSubCategory;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="asset-master-form">

    <?php
    $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>
    <?php //= $form->field($model, 'asset_idx_no')->textInput(['maxlength' => true, 'disabled' => true])  ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Asset Information</legend>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'asset_category')->dropdownList(RefAssetCategory::getDropDownList(), ['prompt' => '(Select...)']) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php
                if ($model->id) {
                    echo $form->field($model, 'asset_sub_category')->dropdownList(RefAssetSubCategory::getDropDownList());
                } else {
                    echo $form->field($model, 'asset_sub_category')->dropdownList(array('' => '(Select...)'));
                }
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'model')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-9">
                <?= $form->field($model, 'specification')->textarea(['rows' => 6]) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'fileImage')->fileInput()->label("Item Image");
                echo $model->file_image ? Html::a("<i class='fas fa-image fa-lg' ></i>", "#",
                                [
                                    'title' => "Click to view me",
                                    "value" => ("/asset/get-image?assetId=" . $model->id),
                                    "class" => "modalButtonPdf m-2"]) : '';
                ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'fileInvoiceImage')->fileInput()->label("Invoice");
                echo $model->file_invoice_image ? Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                                [
                                    'title' => "Click to view me",
                                    "value" => ("/asset/get-invoice?assetId=" . $model->id . '&filename=' . $model->file_invoice_image),
                                    "class" => "modalButtonPdf m-2"]) : '';
                ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'warranty_due_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'purchased_by')->dropdownList(User::getActiveDropDownList(), ['prompt' => '(Select...)', 'disabled' => $userType == 'normalUser' ? true : false]) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'own_type')->dropDownList(RefAssetOwnType::getDropDownList()) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'rental_fee')->textInput(['type' => 'number', 'class' => 'form-control text-right', 'step' => '0.01'])->label('Rental Fee (If any, RM)') ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'condition')->dropDownList(RefAssetCondition::getDropDownListInTransfer()) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'cost')->textInput(['type' => 'number', 'class' => 'form-control text-right', 'step' => '0.01'])->label('Cost (RM)') ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'idle_sts')->dropDownList(['1' => 'Yes', '0' => 'No']) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-9">
                <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>
            </div>
        </div>
    </fieldset>
    <?php
    if (!$model->id) {
        ?>
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2  m-0">Asset Tracking</legend>
            <?php
            if ($userType == 'superUser') {
                ?>
                <div class="form-row">
                    <div class="col-sm-12 col-md-3">
                        <?= $form->field($modelTracking, 'receive_user')->dropdownList(User::getActiveDropDownList(), ['prompt' => '(Select...)'])->label('Current Holder') ?>
                    </div>
                </div>
                <?php
            }
            ?>
            <div class="form-row">
                <div class="col-sm-12 col-md-3">
                    <?= $form->field($modelTracking, 'receive_area')->dropDownList(frontend\models\common\RefArea::getDropDownList(), ['prompt' => '(Select...)'])->label('Area') ?>
                </div>
                <div class="col-sm-12 col-md-3">
                    <?= $form->field($modelTracking, 'receive_address')->dropDownList(\frontend\models\common\RefAddress::getActiveDropDownList(), ['prompt' => '(Select...)'])->label('Address') ?>
                </div>
                <div class="col-sm-12 col-md-3">
                    <?= $form->field($modelTracking, 'receive_proj_code')->dropDownList(frontend\models\working\project\MasterProjects::getActiveDropDownList(), ['prompt' => '(Select...)'])->label('Project Code') ?>
                </div>
            </div>
            <div class="form-row">
                <div class="col-sm-12 col-md-3">
                    <?= $form->field($modelTracking, 'receive_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])->label("Date") ?>
                </div>
            </div>
        </fieldset>
        <?php
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(function () {
        $('#assetmaster-asset_category').change(function () {
            reloadSubCategoryDropdown($(this).val());
        });
    });

    function reloadSubCategoryDropdown(assetCategory) {
        let subCateOption = $('#assetmaster-asset_sub_category');

        var url = "/asset/get-sub-category-dropdown";
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            data: {
                categoryId: assetCategory
            },
            success: function (response) {
                subCateOption.empty();
                for (var key in response) {
                    subCateOption.append("<option value='" + key + "'>" + response[key] + "</option>");
                }
            }
        }).fail(function (xhr, textStatus, errorThrown) {
            alert("ERROR! Kindly contact IT Department.");
            return false;
        });
    }


</script>
