<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\MasterProjects */

$this->title = 'Update Master Projects: ' . $model->project_code;
$this->params['breadcrumbs'][] = ['label' => 'Master Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->project_code, 'url' => ['view', 'id' => $model->project_code]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="master-projects-update">

    <h1><?= Html::encode($this->title) ?></h1>



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
            <?= $form->field($model, 'project_code')->textInput(['maxlength' => true, 'disabled' => 'true']) ?>
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

    <div class="form-row">
        <div class="col-4">
            <?php
            // $form->field($model, 'project_name')->textInput(['maxlength' => true])
            echo $form->field($model, "person_in_charge")->dropDownList($userList, ['prompt' => 'Select...', 'id' => 'main_requestor'])->label('Requestor')
            ?>
        </div>
    </div>

    <?php //= $form->field($model, 'project_image')->textInput(['maxlength' => true])   ?>



    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
