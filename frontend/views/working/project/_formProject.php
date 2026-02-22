<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProjectMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-master-form">

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
//                'action' => '/project/newquotation',
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']
    ]);
    ?>
    <fieldset class="border border-dark p-2">
        <legend class="w-auto pl-2 pr-2 text-primary">Project</legend>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'proj_code')->textInput(['maxlength' => true, 'disabled' => 'true']) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'project_status')->dropdownList((['Ongoing' => 'Ongoing', 'Completed' => 'Completed', 'Superseded' => 'Superseded'])) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-6">
                <?= $form->field($model, 'title_short')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-sm-12 col-md-6">
                <?= $form->field($model, 'title_long')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'drop_location')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => \frontend\models\common\RefArea::getAutocompleteList(),
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'location');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['location0']['area_name']]
                ])->label("Location");
                echo $form->field($model, 'location', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'service')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'contract_sum')->textInput(['type' => 'number', 'step' => .01, 'value' => \common\models\myTools\MyFormatter::asDecimal2NoSeparator($model->contract_sum)]) ?>
            </div>
        </div>
    </fieldset>

    <fieldset class="border border-dark p-2">
        <legend class="w-auto pl-2 pr-2 text-primary">Client</legend>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'drop_client')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $clientList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'client_id');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['client']['company_name']]
                ])->label("Client");
                echo $form->field($model, 'client_id', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'client_pic_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'client_pic_contact')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
    </fieldset>
    <fieldset class="border border-dark p-2">
        <legend class="w-auto pl-2 pr-2 text-primary">Dates</legend>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'award_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'commencement_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'eot_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'handover_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?= $form->field($model, 'dlp_expiry_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy']) ?>
            </div>
        </div>
    </fieldset>
    <fieldset class="border border-dark p-2">
        <legend class="w-auto pl-2 pr-2 text-primary">NPL Person In Charge</legend>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_proj_director')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'proj_director');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['projDirector']['fullname']]
                ])->label("Project Director");
                echo $form->field($model, 'proj_director', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_proj_manager')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'proj_manager');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['projManager']['fullname']]
                ])->label("Project Manager");
                echo $form->field($model, 'proj_manager', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_proj_coordinator')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'proj_coordinator');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['projCoordinator']['fullname']]
                ])->label("Project Coordinator");
                echo $form->field($model, 'proj_coordinator', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>

            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_project_engineer')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'project_engineer');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['projectEngineer']['fullname']]
                ])->label("Project Engineer");
                echo $form->field($model, 'project_engineer', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_site_engineer')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'site_engineer');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['siteEngineer']['fullname']]
                ])->label("Site Engineer");
                echo $form->field($model, 'site_engineer', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_site_manager')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'site_manager');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['siteManager']['fullname']]
                ])->label("Site Manager");
                echo $form->field($model, 'site_manager', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_site_supervisor')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'site_supervisor');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['siteSupervisor']['fullname']]
                ])->label("Site Supervisor");
                echo $form->field($model, 'site_supervisor', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12 col-md-3">
                <?php
                echo $form->field($model, 'user_project_qs')->widget(AutoComplete::className(), [
                    'clientOptions' => [
                        'source' => $userList, 'minLength' => '1', 'autoFill' => true, 'delay' => 1,
                        'change' => new JsExpression("function( event, ui ) { 
                                    handleAutoComplete($(this),ui,'project_qs');
			     }"),
                    ],
                    'options' => ['class' => 'form-control', 'value' => $model['projectQs']['fullname']]
                ])->label("QS Executive");

                echo $form->field($model, 'project_qs', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
                ?>
            </div>
        </div>
    </fieldset>
    <fieldset class="border border-dark p-2">
        <legend class="w-auto pl-2 pr-2 text-primary">Extra</legend>
        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($model, 'remarks')->textarea(['rows' => '6']) ?>
            </div>
        </div>
        <div class="form-row">
            <div class="col-sm-12">
                <?= $form->field($model, 'show_in_resume')->checkbox()->label('Show In Resume <span class="font-weight-lighter text-success">*Will show in Staffs\' resume*</span>') ?>
            </div>
        </div>
    </fieldset>

    <div class="form-row">
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'scannedFile[]')->fileInput(['multiple' => true])->label("Attachments") ?>    
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-3  m-0 p-0">
            <?php
            if ($model->files) {
                $displayStr = "<ul class='list-group border'>";
                foreach ($model->files as $key => $file) {


                    $displayStr .= Html::tag("li",
                                    Html::a($file, "/working/prospect/get-file-prospect?filename=" . urlencode($file) . "&projCode=" . $model->proj_code, ['target' => "_blank", 'title' => "Click to view"])
                                    . Html::a("x", "javascript:remove('" . Html::encode($file) . "','" . $model->proj_code . "','" . $key . "')", ["class" => "close", 'data-confirm' => 'Are you sure to remove the file?']),
                                    ["class" => "list-group-item", "id" => "file_" . $key]);
                }
                $displayStr .= "</ul>";
                echo $displayStr;
            }
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save <i class="far fa-save"></i>', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script>
    function handleAutoComplete(thisItem, ui, target) {

        if (ui.item) {
            thisItem.val(ui.item.label);
            $('#projectmaster-' + target).val(ui.item.id);
        } else {
            thisItem.val('');
            $('#projectmaster-' + target).val('');
        }
    }


    function remove(fileUrl, projCode, key) {
        $.ajax({
            type: "POST",
            url: "/working/prospect/delete-file-prospect",
            dataType: "json",
            data: {
                filename: fileUrl,
                projCode: projCode
            },
            success: function (data) {
                alert(data.msg);
                $("#file_" + key).hide();
            }
        });

    }


</script>