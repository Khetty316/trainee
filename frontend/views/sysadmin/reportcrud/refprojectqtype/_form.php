<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefProjectQTypes $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="ref-project-qtypes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'project_type_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fab_dept_percentage')->textInput() ?>

    <?= $form->field($model, 'elec_dept_percentage')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
