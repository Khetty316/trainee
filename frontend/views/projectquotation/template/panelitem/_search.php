<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQPanelItemsTemplateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-qpanel-items-template-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'panel_template_id') ?>

    <?= $form->field($model, 'item_description') ?>

    <?= $form->field($model, 'cost') ?>

    <?= $form->field($model, 'markup') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'product_id') ?>

    <?php // echo $form->field($model, 'sort') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
