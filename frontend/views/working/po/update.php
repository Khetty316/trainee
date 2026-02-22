<?php

use yii\helpers\Html;
use \yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\po\PurchaseOrderMaster */

$this->title = 'Edit P.O.: ' . $model->po_number;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->po_number, 'url' => ['view', 'id' => $model->po_id]];
$this->params['breadcrumbs'][] = 'Edit';
?>
<div class="purchase-order-master-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php
    $form = ActiveForm::begin([
                'id' => 'newpo-form',
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
//                'action' => '/project/newquotation',
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>

    <?= $form->field($model, 'updated_by')->hiddenInput(["value" => Yii::$app->user->id])->label(false) ?>
    <?= $form->field($model, 'po_upload_file')->hiddenInput()->label(false) ?>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            Attached File: <br/><?= Html::a($model->po_upload_file, "/working/po/get-file?filename=" . urlencode($model->po_upload_file), ['target' => "_blank"]) ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'quotation_master_id')->textInput(['disabled' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "po_number")->textInput(['disabled' => 'true']) ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?=
            $form->field($model, 'po_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])
            ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?php
            echo $form->field($model, "project_code")->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => $projectList,
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 500,
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                ],
                'options' => ['class' => 'form-control']
            ]);
            ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "po_material_desc")->textInput() ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "po_lead_time")->textInput() ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "po_etd")->textInput() ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "po_transporter")->textInput() ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'po_pic')->dropDownList($userList, ['prompt' => 'Select...']) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'po_address')->dropDownList($addressList, ['prompt' => 'Select...']) ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?php
            $currencyInput = $form->field($model, "currency")->dropdownList(frontend\models\common\RefCurrencies::getCurrencyActiveDDropdownlist())->label(false);

            echo $form->field($model, 'amount', [
                'inputTemplate' => '<div class="input-group"><span class="input-group-addon"></span>' . $currencyInput . '{input}</div>',
            ])->textInput(['style' => 'text-align:right', 'type' => 'number']);
            ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'po_receive_status')->dropdownList(array('1' => 'Yes', '0' => 'No')) ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-8">
            <?= $form->field($model, 'remarks')->textarea(['rows' => '6']) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-8">
            <div class="form-group">
                <div class="pull-right">
                    <?= Html::button('Submit', ['class' => 'btn btn-success', 'id' => 'submitBtn', 'onclick' => 'submit()']) ?>
                </div>
            </div>
        </div>
    </div>



    <?php ActiveForm::end(); ?>

</div>
<script>

    function submit() {

        $("#newpo-form").submit();
    }

</script>