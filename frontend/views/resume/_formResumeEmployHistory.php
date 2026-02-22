<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\resume\ResumeEmployHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resume-employ-history-form">

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
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'employ_period')->textInput(['maxlength' => true, 'placeholder' => 'E.g.: June 2015 - May 2020'])->label("Employment Period") ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'employ_role')->textInput(['maxlength' => true, 'placeholder' => 'E.g.: Executive Engineer'])->label("Role") ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'employ_company')->textInput(['maxlength' => true, 'placeholder' => 'E.g.: Google SDN BHD'])->label("Company Name")?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
               <?= $form->field($model, 'employ_detail')->textarea(['rows' => 6])->label("Job Scope / Descriptions") ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
