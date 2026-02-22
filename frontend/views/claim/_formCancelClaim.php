<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimEntitlement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="claim-entitlement-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-2">
                <?=
                $form->field($claimMaster, 'delete_remark')->textArea([
                    'class' => 'form-control',
                    'required' => true,
                ])->label('Remark <span class="text-danger">*</span>')
                ?>       
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-success float-right mt-3']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
