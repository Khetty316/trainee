<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\common\AuditTrailPageVisit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="audit-trail-page-visit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'page')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
