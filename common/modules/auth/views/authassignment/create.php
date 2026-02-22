<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\modules\auth\models\AuthAssignment */

$this->title = 'Create Auth Assignment';
$this->params['breadcrumbs'][] = ['label' => 'Auth Assignments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-assignment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="auth-assignment-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'item_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'user_id')->dropDownList($userList, ['prompt' => 'Select...']) ?>

        <?= $form->field($model, 'created_at')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
