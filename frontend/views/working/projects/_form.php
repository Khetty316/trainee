<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm
/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\MasterProjects */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="master-projects-form">


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
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'project_code')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-4">
            <?= $form->field($model, 'project_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-8">
            <?= $form->field($model, 'project_description')->textInput(['maxlength' => true]) ?>
        </div>
    </div>



    <?php //= $form->field($model, 'project_image')->textInput(['maxlength' => true])  ?>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
