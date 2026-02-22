<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\cmms\InventorySupplierCmmsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-supplier-cmms-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'address1') ?>

    <?= $form->field($model, 'address2') ?>

    <?php // echo $form->field($model, 'address3') ?>

    <?php // echo $form->field($model, 'address4') ?>

    <?php // echo $form->field($model, 'contact_name') ?>

    <?php // echo $form->field($model, 'contact_number') ?>

    <?php // echo $form->field($model, 'contact_email') ?>

    <?php // echo $form->field($model, 'contact_fax') ?>

    <?php // echo $form->field($model, 'agent_terms') ?>

    <?php // echo $form->field($model, 'active_sts') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
