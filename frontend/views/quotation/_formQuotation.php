<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\quotation\QuotationMasters */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quotation-masters-form">

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
        <div class="col-sm-12 col-md-3">
            <?php
            $data2 = frontend\models\working\project\MasterProjects::find()
                    ->select(['project_code as value', 'project_code as id', 'CONCAT(project_code," - ",project_name) as label'])
                    ->asArray()
                    ->all();
            echo $form->field($model, "project_code")->widget(yii\jui\AutoComplete::className(), [
                'clientOptions' => [
                    'source' => $data2,
                    'minLength' => '1',
                    'autoFill' => true,
                    'delay' => 500,
                    'change' => new \yii\web\JsExpression("function( event, ui ) { 
			            $(this).val((ui.item ? ui.item.id : ''));
			     }"),
                ],
                'options' => ['class' => 'form-control']
            ]);
            ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'scannedFile')->fileInput()->label("Reference Doc (if any)") ?>
        </div>
    </div>

    <div class="form-row">

        <div class="col-sm-12 col-md-6">
            <?= $form->field($model, 'description')->textarea(['rows' => 12]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
