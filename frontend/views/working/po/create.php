<?php

use yii\helpers\Html;
use \yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\working\po\PurchaseOrderMaster */

$this->title = 'Purchase Order Registration';
$this->params['breadcrumbs'][] = ['label' => 'Purchase Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

//
//$formCss4 = '{label} <div class="col-sm-12">{input}{error}{hint}</div>';
//$formCssCheckbox = '{label} {input}';
?>

<div class="purchase-order-master-create">

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
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>

    <?= $form->field($model, 'created_by')->hiddenInput(["value" => Yii::$app->user->id])->label(false) ?>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'scannedFile')->fileInput() ?>
        </div>       
        <div class="col-sm-12 col-md-4">
            <?php
            echo $form->field($model, "quotation_master_id")->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => $quotationList,
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 200, 
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
                                    if(ui.item){
                                        $(this).val(ui.item.id);
                                         $('#purchaseordermaster-project_code').val(ui.item.project_code);
                                         $('#purchaseordermaster-po_pic').val(ui.item.user_id);
                                    }else{
                                        $(this).val('');
                                        $('#purchaseordermaster-project_code').val('');
                                   }
			     }"),
                ],
                'options' => ['class' => 'form-control']
            ]);
            ?>
        </div>       
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, "po_number")->textInput() ?>
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
            ])->textInput(['style' => 'text-align:right', 'type' => 'number', 'step' => ".01"]);
            ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'po_receive_status')->dropdownList(array('0' => 'No', '1' => 'Yes')) ?>
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
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-success', 'id' => 'submitButton']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
