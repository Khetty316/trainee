<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefContactType;
use frontend\models\common\RefArea;
use frontend\models\common\RefState;
use frontend\models\common\RefCountries;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\contact\ContactMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-master-form">

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

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'contact_type')->dropdownList(RefContactType::getDropDownList(), ['prompt' => '(Select...)', 'id' => 'contact_type'])->label("Contact Type") ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'contact_person')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'contact_position')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'contact_number')->textInput(['maxlength' => true]) ?>   
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>      
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>     
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-2">
            <?= $form->field($model, 'postcode')->textInput(['maxlength' => true]) ?>     
        </div>
        <div class="col-sm-12 col-md-2">
            <?= $form->field($model, 'area')->dropdownList(RefArea::getDropDownList(), ['prompt' => '(Select...)', 'id' => 'refArea']) ?>     
        </div>
        <div class="col-sm-12 col-md-2">
            <?= $form->field($model, 'state')->dropdownList(RefState::getDropDownList(), ['prompt' => '(Select...)', 'id' => 'refState']) ?>     
        </div>
        <div class="col-sm-12 col-md-2">
            <?= $form->field($model, 'country')->dropdownList(RefCountries::getDropDownList(), ['prompt' => '(Select...)', 'id' => 'refCountry', 'value' => $model ? $model->country : "MY"]) ?>    
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(function () {
        $("#refArea").on("change", function () {
            autoSelectState($(this).val());
        });
    });

    function autoSelectState(areaId) {
        $.ajax({
            url: "/list/get-ref-area-detail-by-id",
            dataType: "json",
            data: {
                term: areaId
            },
            success: function (data) {
                $("#refState").val(data[0].state_id);
            }
        });
    }

</script>