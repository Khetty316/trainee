<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\contact\ContactMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'contact_type') ?>

    <?= $form->field($model, 'company_name') ?>

    <?= $form->field($model, 'contact_person') ?>

    <?= $form->field($model, 'contact_position') ?>

    <?php // echo $form->field($model, 'contact_number') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'postcode') ?>

    <?php // echo $form->field($model, 'area') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
