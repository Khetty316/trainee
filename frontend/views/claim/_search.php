<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="claim-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'claim_code') ?>

    <?= $form->field($model, 'claimant_id') ?>

    <?= $form->field($model, 'claim_type') ?>

    <?= $form->field($model, 'ref_code') ?>

    <?php // echo $form->field($model, 'superior_id') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <?php // echo $form->field($model, 'claim_status') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
