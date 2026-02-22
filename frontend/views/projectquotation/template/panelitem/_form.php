<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanelItemsTemplate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-qpanel-items-template-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'panel_template_id')->textInput() ?>

    <?= $form->field($model, 'item_description')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cost')->textInput() ?>

    <?= $form->field($model, 'markup')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <?= $form->field($model, 'product_id')->textInput() ?>

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
