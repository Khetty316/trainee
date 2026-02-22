<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
/* @var $this yii\web\View */
/* @var $model frontend\models\working\appraisal\ShortAppraisalMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="short-appraisal-master-form">

    <?php
    $form = ActiveForm::begin(['layout' => 'horizontal',
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
                'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'off']]);
    ?>


    <div class="form-row">
        <div class="col-sm-12 col-md-3">
            <?php
            $model->date = date('Y-m-d');
            echo $form->field($model, 'date')->widget(yii\jui\DatePicker::className(), 
                    [
                        'options' => ['class' => 'form-control'],
                        'dateFormat' => 'dd/MM/yyyy',
                        ]);
            ?>
        </div>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-6">
            <?= $form->field($model, 'content')->textarea(['rows' => 6]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
