<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefAssetCategory;
use frontend\models\common\RefAssetSubCategory;
use frontend\models\common\RefAssetCondition;
use frontend\models\common\RefAssetOwnType;
use common\models\User;

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
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off'],
                'action' => $formAction,
                'id' => 'form_receiveAsset'
    ]);
    ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Receive Asset</legend>
        <div class="form-row">
            <div class="col-sm-12 pb-3">
                <?php
                $currentHolder = $currentTracking->receiveUser;
                echo 'Transfer From: <b>' . $currentHolder->fullname . '</b>';
                echo $form->field($modelTracking, 'asset_id', ['options' => ['class' => 'hidden']])->textInput(['value' => $model->id]);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12">
                <?php
                echo $form->field($modelTracking, 'receive_proj_code')->widget(\yii\jui\AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => \yii\helpers\Url::to(['/list/getprojectlist']),
                        'minLength' => '1',
                        'autoFill' => true,
                        'search' => new \yii\web\JsExpression("function( event, ui ) { 
			     }"),
                        'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                        'delay' => 100,
                        'appendTo' => '#form_receiveAsset',
                    ],
                    'options' => [
                        'class' => 'form-control',
                    ]
                ])->label('Project Code:');
                ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <?=
                        $form->field($modelTracking, 'receive_area')
                        ->dropdownList(frontend\models\common\RefArea::getDropDownList(), ['prompt' => '(Select...)'])->label("Area:")
                ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <?=
                        $form->field($modelTracking, 'receive_address')
                        ->dropdownList(\frontend\models\common\RefAddress::getActiveDropDownList(), ['prompt' => '(Select...)'])->label("Location:")
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-6">
                <?=
                        $form->field($modelTracking, 'receive_condition')
                        ->dropdownList(RefAssetCondition::getDropDownListInTransfer(), ['prompt' => '(Select...)'])->label("Condition:")
                ?>
            </div>
            <div class="col-sm-12 col-md-6">
                <?= $form->field($modelTracking, 'receive_date')
                        ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])->label("Receive date:") ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($modelTracking, 'receive_remark')->textarea(['rows' => 6]) ?>
            </div>
        </div>
    </fieldset>


    <div class="form-group text-right">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success','id'=>'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
