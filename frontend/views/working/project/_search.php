<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\working\project\ProjectMasterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-master-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'proj_code') ?>

    <?= $form->field($model, 'title_short') ?>

    <?= $form->field($model, 'title_long') ?>

    <?= $form->field($model, 'project_status') ?>

    <?php // echo $form->field($model, 'location') ?>

    <?php // echo $form->field($model, 'client_id') ?>

    <?php // echo $form->field($model, 'service') ?>

    <?php // echo $form->field($model, 'contract_sum') ?>

    <?php // echo $form->field($model, 'client_pic_name') ?>

    <?php // echo $form->field($model, 'client_pic_contact') ?>

    <?php // echo $form->field($model, 'award_date') ?>

    <?php // echo $form->field($model, 'commencement_date') ?>

    <?php // echo $form->field($model, 'eot_date') ?>

    <?php // echo $form->field($model, 'handover_date') ?>

    <?php // echo $form->field($model, 'dlp_expiry_date') ?>

    <?php // echo $form->field($model, 'proj_director') ?>

    <?php // echo $form->field($model, 'proj_manager') ?>

    <?php // echo $form->field($model, 'site_manager') ?>

    <?php // echo $form->field($model, 'proj_coordinator') ?>

    <?php // echo $form->field($model, 'project_engineer') ?>

    <?php // echo $form->field($model, 'site_engineer') ?>

    <?php // echo $form->field($model, 'site_supervisor') ?>

    <?php // echo $form->field($model, 'project_qs') ?>

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
