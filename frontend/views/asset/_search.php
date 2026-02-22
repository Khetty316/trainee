<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="asset-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'asset_idx_no') ?>

    <?= $form->field($model, 'asset_category') ?>

    <?= $form->field($model, 'asset_sub_category') ?>

    <?= $form->field($model, 'file_image') ?>

    <?php // echo $form->field($model, 'file_invoice_image') ?>

    <?php // echo $form->field($model, 'purchased_by') ?>

    <?php // echo $form->field($model, 'own_type') ?>

    <?php // echo $form->field($model, 'rental_fee') ?>

    <?php // echo $form->field($model, 'idle_sts') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'brand') ?>

    <?php // echo $form->field($model, 'model') ?>

    <?php // echo $form->field($model, 'specification') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'condition') ?>

    <?php // echo $form->field($model, 'cost') ?>

    <?php // echo $form->field($model, 'warranty_due_date') ?>

    <?php // echo $form->field($model, 'active_sts') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
