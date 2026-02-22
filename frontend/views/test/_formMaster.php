<?php

use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use frontend\models\test\TestMain;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

$this->title = $panel->project_production_panel_code;
$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index']];
$this->params['breadcrumbs'][] = ['label' => $panel->panel_description];
$this->params['breadcrumbs'][] = ['label' => $model->test_type, 'url' => ['/test/testing/index-master', 'id' => $panel->id, 'type' => $model->test_type]];
$this->params['breadcrumbs'][] = ['label' => $testMaster->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $testMaster->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<link href="/css/summernote.css" rel="stylesheet">
<script src="/js/summernote.min.js" type="text/javascript"></script>

<div class="form-initiate-master">
    <div>
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0">Fill in Details:</legend>
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-xl-4 m-0 p-0">
                    <div class="col-12 row p-0 m-0">
                        <div class="col-7 pr-0">
                            <?php
                            if (!$testMaster->parent) {
                                echo $form->field($testMaster, 'panel_qty')->textInput([
                                    'maxlength' => true,
                                    'type' => 'number',
                                    'min' => 0,
                                    'max' => $panel->quantity,
                                ])->label('No. of Panel');
                            } else {
                                echo $form->field($testMaster, 'panel_qty')->textInput([
                                    'disabled' => true,
                                ])->label('No. of Panel');
                            }
                            ?>
                        </div>
                        <div class="col-1 p-0 pt-4 m-0 mt-3 text-center">
                            <span class="">/</span>
                        </div>
                        <div class="col-4 pl-0">
                            <?= $form->field($testMaster, 'testPlan')->textInput(['class' => 'form-control text-center mt-2', 'value' => "$panel->quantity", 'disabled' => true])->label('') ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <?=
                                $form->field($testMaster, 'date', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                                ->widget(\yii\jui\DatePicker::className(), [
                                    'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                                    'clientOptions' => [
                                        'dateFormat' => 'dd/mm/yy',
                                        'showButtonPanel' => true,
                                        'closeText' => 'Close',
                                        'beforeShow' => new \yii\web\JsExpression('function (input, instance) {
                                                    $(input).datepicker("option", "dateFormat", "dd/mm/yy");
                                                    }'),
                                    ],
                        ]);
                        ?>
                    </div>
                    <div class="col-12">
                        <?=
                        $form->field($testMaster, 'tested_by')->widget(AutoComplete::className(), [
                            'clientOptions' => [
                                'source' => $userList,
                            ],
                            'options' => ['class' => 'form-control', 'value' => $testMaster['testedBy']['fullname'] ?? null],
                            'clientEvents' => [
                                'open' => new JsExpression("function(event, ui) {
                                $('.ui-autocomplete').css('z-index', 1051);
                                }"),
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-12">
                        <?=
                        $form->field($model, 'client')->widget(AutoComplete::className(), [
                            'clientOptions' => [
                                'source' => \frontend\models\client\Clients::getAutocompleteList(),
                            ],
                            'options' => ['class' => 'form-control', 'value' => $model['client']['company_name'] ?? null],
                            'clientEvents' => [
                                'open' => new JsExpression("function(event, ui) {
                                $('.ui-autocomplete').css('z-index', 1051);
                                }"),
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-12">
                        <?= $form->field($model, 'elec_consultant')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-12">
                        <?=
                        $form->field($model, 'elec_contractor')->widget(AutoComplete::className(), [
                            'clientOptions' => [
                                'source' => \frontend\models\client\Clients::getAutocompleteList(),
                            ],
                            'options' => ['class' => 'form-control', 'value' => $model['client']['company_name'] ?? null],
                            'clientEvents' => [
                                'open' => new JsExpression("function(event, ui) {
                                $('.ui-autocomplete').css('z-index', 1051);
                                }"),
                            ],
                        ]);
                        ?>
                    </div>
                </div>
                <div class="col-xl-8">
                    <?= $form->field($testMaster, 'detail')->textarea(['rows' => 12, 'class' => 'form-control program']) ?>
                </div>
            </div>
            <div class="form-group col text-right pr-0">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?= $form->field($testMaster, 'test_main_id')->hiddenInput(['value' => $model->id])->label(false) ?>

            <?php ActiveForm::end(); ?>
        </fieldset>
    </div>
</div>

<?php $this->registerJs("$(document).ready(function() { $('form').attr('autocomplete', 'off'); });"); ?>
<script>
    $(function () {
        $('.program').summernote({
            height: 500,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['media', 'link', 'hr']],
                ['help', ['help']]
            ],
        });
    });
</script>
