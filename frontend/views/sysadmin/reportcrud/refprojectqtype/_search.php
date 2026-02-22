<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefProjectQTypeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ref-project-qtypes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'project_type_name') ?>

    <?= $form->field($model, 'fab_dept_percentage') ?>

    <?= $form->field($model, 'elec_dept_percentage') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
