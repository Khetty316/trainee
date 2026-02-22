<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\resume\ResumeAcademicQualificationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="resume-academic-qualifications-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'academic_level') ?>

    <?= $form->field($model, 'academic_institution') ?>

    <?= $form->field($model, 'academic_course') ?>

    <?php // echo $form->field($model, 'academic_period') ?>

    <?php // echo $form->field($model, 'academic_honour') ?>

    <?php // echo $form->field($model, 'active_sts') ?>

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
