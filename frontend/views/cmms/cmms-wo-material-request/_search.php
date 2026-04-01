<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsWoMaterialRequestMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cmms-wo-material-request-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'wo_type') ?>

    <?= $form->field($model, 'wo_id') ?>

    <?= $form->field($model, 'finalized_status') ?>

    <?= $form->field($model, 'fully_dispatched_status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
