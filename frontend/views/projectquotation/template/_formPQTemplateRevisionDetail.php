<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\common\RefProjectQShippingMode;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-qrevisions-form">

    <?php
    $form = ActiveForm::begin([
        'options' => ['autocomplete' => 'off'],
    ]);
    ?>

    <div class="form-row">
        <div class="col-sm-12 col-md-5">
            <?= $form->field($model, 'revision_description')->textInput(['maxlength' => true, 'readonly' => ($model->created_by == Yii::$app->user->identity->id ? false : true)]) ?>
        </div>
        <div class="col-sm-12 col-md-5">
            <?= $form->field($model, 'currency_id')->dropDownList($currencyList)->label("Currency") ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-10">
            <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-5">
            <?= $form->field($model, 'q_material_offered')->textarea(['rows' => 6]) ?>
        </div>
        <div class="col-sm-12 col-md-5">
            <?= $form->field($model, 'q_switchboard_standard')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-10">
            <?= $form->field($model, 'q_quotation')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row col-sm-12 col-md-10 m-0 p-0">
        <div class="col-sm-12 col-md-3 pl-0">
            <?php
            echo $form->field($model, "q_delivery_ship_mode")->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => RefProjectQShippingMode::getAutocompleteList(),
                    'minLength' => '0',
                    'autoFill' => true,
                    'delay' => 10,
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                ],
                'options' => ['class' => 'form-control', 'placeholder' => 'Shipping Mode']
            ])->label("Delivery");
            ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'q_delivery_destination')->textInput(['maxlength' => true, 'placeholder' => 'Destination'])->label("&nbsp;") ?>
        </div>
        <div class="col-sm-12 col-md-6">
            <?= $form->field($model, 'q_delivery')->textInput(['maxlength' => true, 'placeholder' => 'Duration'])->label("&nbsp;") ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-10">
            <?= $form->field($model, 'q_validity')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'q_payment')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-10">
            <?= $form->field($model, 'q_remark')->textarea(['rows' => 6]) ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    $(function () {
        $("#projectqrevisions-q_delivery_ship_mode").focus(function () {
            $(this).autocomplete("search", "");
        });
    });
</script>