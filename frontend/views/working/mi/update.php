<?php

use yii\helpers\Html;
use \yii\bootstrap4\ActiveForm;
use \yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\working\MasterIncomings */

$this->title = 'Create Master Incomings';
$this->params['breadcrumbs'][] = ['label' => 'Master Incomings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


$formCss4 = '{label} <div class="col-sm-12">{input}{error}{hint}</div>';
$formCssCheckbox = '{label} {input}';
?>
<style>
    .help-block-error {
        color: red;
    }
</style>
<div class="master-incomings-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php
    $form = ActiveForm::begin([
                'id' => 'newquotation-form',
                'layout' => 'horizontal',
                'fieldConfig' => [
//                    'template' => "{label}\n<div class=\"col-lg-8\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>\n\n",
                    'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
//                    'template' => '{label} <div class="col-sm-8">{input}</div>',
//                    'labelOptions' => ['class' => 'col-lg-2 control-label']
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-6',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                        'error' => '',
                        'hint' => '',
                    ],
                ],
//                'action' => '/project/newquotation',
                'options' => ['enctype' => 'multipart/form-data']
    ]);

    if (Yii::$app->session->hasFlash("success")) {
        echo Yii::$app->session->getFlash("success");
    }
    ?>
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'uploader[fullname]')->textInput([ 'readonly' => 'true'])->label("Uploaded By") ?>
        </div>       
    </div>


    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'scannedFile')->fileInput() ?>
        </div>       
    </div>
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'doc_type_id')->dropDownList($docTypeList, ['prompt' => 'Select...']) ?>
        </div>
<!--        <div class="col-4">
            <?php // = $form->field($model, 'sub_doc_type_id')->dropDownList($subDocTypeList, ['prompt' => 'Select...']) ?>
        </div>-->
    </div>

    <div class="form-row">
        <div class="col-4">
            <?=
            $form->field($model, 'doc_due_date')->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'yyyy-MM-dd'])
            ?>
        </div>
        <div class="col-2">
            <br/>
            <?= $form->field($model, "isUrgent")->checkbox() ?>
        </div>
        <div class="col-3">
            <br/>
            <?= $form->field($model, "isPerforma")->checkbox() ?>
        </div>
    </div>


    <div class="form-row">
        <div class="col-4">
        </div>
        <div class="col-4">
            <?= $form->field($model, "reference_no")->textInput() ?>
        </div>
    </div>   

    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, "particular")->textInput() ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, "amount")->textInput() ?>
        </div>
    </div>   

    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, "file_type_id")->dropDownList($fileTypeList, ['prompt' => 'Select...']) ?>

        </div>
        <div class="col-4">
            <?= $form->field($model, "received_from")->textInput() ?>
        </div>
    </div>   
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, "project_code")->textInput() ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, "requestor_id")->dropDownList($userList, ['prompt' => 'Select...']) ?>
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
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>
            </div>
        </div>
    </div>



    <?php ActiveForm::end(); ?>

</div>
