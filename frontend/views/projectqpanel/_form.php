<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanels */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-qpanels-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'revision_id')->textInput() ?>

    <?= $form->field($model, 'panel_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'sort')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
