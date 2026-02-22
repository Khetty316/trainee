<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">


    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(); ?>

                <?php
                echo yii\bootstrap4\Html::dropDownList("id",'',$userList);
                ?>



                <div class="form-group">
                    <?= Html::submitButton('Switch', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
