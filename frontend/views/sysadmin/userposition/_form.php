<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefUserDesignation $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ref-user-designation-form">

    <?php
    $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-sm-8\">{input}\n{error}</div>",
                    'labelOptions' => ['class' => 'col-sm-4 control-label'],
                ],
    ]);
    ;
    ?>
    <div class="form-group">
        <div class="row">
            <div class="col-md-3 col-sm-12">
                <?= $form->field($model, 'design_name')->textInput(['maxlength' => true])->label("Position Name:") ?>
            </div>
            <div class="col-md-3 col-sm-12">
                <?= $form->field($model, 'staff_type')->dropDownList(frontend\models\common\RefUserDesignation::getDropDownListNoDirector(), ['prompt' => 'Select staff type'])->label("Position Type:") ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
