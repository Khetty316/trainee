<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefProjectType;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProspectMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prospect-master-form">

    <?php
    $form = ActiveForm::begin([
//                'id' => 'new_claim_form',
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

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'proj_code')->textInput(['maxlength' => true, 'disabled' => 'true', 'placeholder' => "(Auto Generate)"]) ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'project_type')->dropdownList(RefProjectType::getDropDownList(), ['prompt' => '(Select...)', 'value' => 'proj']) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'title_short')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'title_long')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?=
                    $form->field($model, 'due_date')
                    ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy'])
            ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?php
            echo $form->field($model, 'locationDropdown')->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => \frontend\models\common\RefArea::getAutocompleteList(),
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 1,
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.value : ''));
                                    if(ui.item){
                                        $(this).val(ui.item.label);
                                        $('#prospectmaster-area').val(ui.item.id);
                                    }else{
                                        $(this).val('');
                                        $('#prospectmaster-area').val('')

                                    }
			     }"),
                ],
                'options' => ['class' => 'form-control', 'value' => $model['area0']['area_name']]
            ])->label("Location");

            echo $form->field($model, 'area', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
            ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?php
            echo $form->field($model, 'staffPicDropdown')->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => \common\models\User::getActiveAutocompleteList(),
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 1,
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.value : ''));
                                    if(ui.item){
                                        $(this).val(ui.item.label);
                                        $('#prospectmaster-staff_pic').val(ui.item.id);
                                    }else{
                                        $(this).val('');
                                        $('#prospectmaster-staff_pic').val('')

                                    }
			     }"),
                ],
                'options' => ['class' => 'form-control', 'value' => $model['staffPic']['fullname']]
            ])->label("NPL Person In Charge");

            echo $form->field($model, 'staff_pic', ['options' => ['class' => 'm-0 p-0']])->hiddenInput()->label(false);
            ?>
        </div>

        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'other_pic')->textInput(['maxlength' => true])->label("Other Person In Charge") ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'scannedFile[]')->fileInput(['multiple' => true])->label("Attachments") ?>    
        </div>
    </div>
    <div class="form-row">
        
        <div class="col-sm-12 col-md-4  m-0 p-0">
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