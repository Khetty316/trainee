<?php

use yii\helpers\Html;
//use yii\widgets\ActiveForm;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\CmmsCorrectiveWorkRequest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="work-request-form">

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
                'options' => ['autocomplete' => 'off'],
                'action' => ['update-work-request',
                    'id' => $workRequest->id,
                    'method' => 'post',
                ],
            ]);
    ?>

    <div class="form-row">
            <div class="col-md-9">
                <div class="form-row">
                    <div class="col-sm-12 col-md-3">
                        <div class="form-group row field-projectqmasters-quotation_no">
                            <label class="col-sm-12" for="projectqmasters-quotation_no">Work Request No.</label> 
                            <div class="col-sm-12">
                                <input type="text" class="form-control" name="" value="" disabled />
                                <!--<div class="text-success" style="font-size: 8pt">* Quotation no. might varies upon save *</div>-->
                            </div>
                        </div>                   
                    </div>
                    <!--machine breakdown type dropdown list-->
                    <div class="col-sm-12 col-md-5">
                        <!--<? $form->field($model, 'company_group_code')->dropDownList($companyGroupList, ['prompt' => 'Select...']) ?>-->
                    </div>

<!--                    <div class="col-sm-12 col-md-4">
                        <php
                        $userList = common\models\User::getAutoCompleteListActiveOnly();
                        echo $form->field($model, "projCoordinatorFullname")->widget(yii\jui\AutoComplete::className(), [
                            'clientOptions' => [
                                'source' => $userList,
                                'minLength' => '1',
                                'autoFill' => true,
                                'delay' => 100,
                                'change' => new \yii\web\JsExpression("function( event, ui ) { 
                            $('#projectqmasters-project_coordinator').val((ui.item ? ui.item.id : ''));
                            $(this).val((ui.item ? ui.item.label : ''));
			     }"),
                            ],
                            'options' => ['class' => 'form-control', 'readonly' => true]
                        ])->label("Project Coordinator");
                        ?>
                        <? $form->field($model, 'project_coordinator', ['options' => ['class' => 'hidden']])->textInput() ?>
                    </div>-->
                </div>
<!--                <div class="form-row">
                    <div class="col-sm-12 col-md-12">
                        <? $form->field($model, 'project_name')->textInput(['id' => 'myform-project-name']) ?>
                        <div class="text-center" id="loadingSpinner" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>                        
                        <div id="similarProjectsList" style="display: none; margin-bottom: 20px;"></div>
                    </div>
                </div>-->

                <div class="form-row">
                    <!--photos and details-->
                    <div class="col-sm-12 col-md-12">
                        <!--<? $form->field($model, 'remark')->textarea(['rows' => 8]) ?>-->
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-12 col-md-12 text-right">
                        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-row">
                    <div class=" pl-sm-2 pl-md-3 col-sm-12">

                    </div>
                </div>
            </div>
        </div>
    
    <?php ActiveForm::end(); ?>

</div>