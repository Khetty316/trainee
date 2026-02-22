<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisionsTemplateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-qrevisions-template-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'revision_copy_master') ?>

    <?= $form->field($model, 'revision_description') ?>

    <?= $form->field($model, 'remark') ?>

    <?= $form->field($model, 'currency_id') ?>

    <?php // echo $form->field($model, 'amount') ?>

    <?php // echo $form->field($model, 'q_material_offered') ?>

    <?php // echo $form->field($model, 'q_switchboard_standard') ?>

    <?php // echo $form->field($model, 'q_quotation') ?>

    <?php // echo $form->field($model, 'q_delivery_ship_mode') ?>

    <?php // echo $form->field($model, 'q_delivery_destination') ?>

    <?php // echo $form->field($model, 'q_delivery') ?>

    <?php // echo $form->field($model, 'q_validity') ?>

    <?php // echo $form->field($model, 'q_payment') ?>

    <?php // echo $form->field($model, 'q_remark') ?>

    <?php // echo $form->field($model, 'with_sst') ?>

    <?php // echo $form->field($model, 'show_breakdown') ?>

    <?php // echo $form->field($model, 'show_breakdown_price') ?>

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
