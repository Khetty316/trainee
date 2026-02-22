<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h3><?= Html::encode($this->title) ?></h3>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup', 'options' => ['autocomplete' => 'off']]); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
            
            <?= $form->field($model, 'password')->passwordInput(["value" => "password"])->label("Password (Default to: <b>password</b>):") ?>

            <?= $form->field($model, 'fullname')->textInput()->label("Full Name") ?>
            
            <?= $form->field($model, 'staff_id')->textInput() ?>

            <?= $form->field($model, 'email') ?>

            <?= $form->field($model, 'company_name')->dropdownList($companyList, ['prompt' => 'Select...']) ?>

            <?= $form->field($model, 'employment_type')->dropdownList($employmentTypeList, ['prompt' => 'Select...']) ?>




            <div class="form-group">
                <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
