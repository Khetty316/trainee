<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use common\models\myTools\MyCommonFunction;
?>
<div>

    <?php
    $form = ActiveForm::begin([
                'options' => ['autocomplete' => 'off'],
    ]);
    ?>
    <div class="col-12">

        <?= $form->field($model, 'equipment_code')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'equipment_description')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-12">
        <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
    </div>

    <div class="col-12">
        <?= MyCommonFunction::activeFormDateInput($form, $model, 'next_service_date', 'Next Service Date') ?>
    </div>

    <div class="text-right">
        <?php
        if (!$model->isNewRecord) {
            echo Html::a('Delete', ['maintenance/delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php
    ActiveForm::end();
    ?>
</div>