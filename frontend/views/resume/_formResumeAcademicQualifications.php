<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\resume\ResumeAcademicQualifications */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resume-academic-qualifications-form">

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
            <?= $form->field($model, 'academic_period')->textInput(['maxlength' => true,'placeholder'=>'E.g.: June 2015 - May 2020']) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'academic_level')->textInput(['maxlength' => true,'placeholder'=>'E.g.: Bachelor\'s degree / Diploma'])->label('Level of Education') ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'academic_institution')->textInput(['maxlength' => true,'placeholder'=>'E.g.: Universiti Malaysia Sarawak (UNIMAS)'])->label('Name of Institute') ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'academic_course')->textInput(['maxlength' => true,'placeholder'=>'E.g.: Bachelor of Electrical & Electronics Engineering (Hons)']) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-8">
            <?= $form->field($model, 'academic_honour')->textInput(['maxlength' => true,'placeholder'=>'E.g.: First-Class Honours / Upper Second-Class Honours'])->label('Honours') ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
