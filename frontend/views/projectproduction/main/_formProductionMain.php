<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\ProjectProduction\ProjectProductionMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-production-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_production_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'quotation_id')->textInput() ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'proj_prod_category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
