<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\VClaimMasterDetailsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="vclaim-master-details-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'claim_master_id') ?>

    <?= $form->field($model, 'claim_code') ?>

    <?= $form->field($model, 'claimant_id') ?>

    <?= $form->field($model, 'claimant_fullname') ?>

    <?= $form->field($model, 'claim_type') ?>

    <?php // echo $form->field($model, 'claim_type_name') ?>

    <?php // echo $form->field($model, 'superior_id') ?>

    <?php // echo $form->field($model, 'superior_fullname') ?>

    <?php // echo $form->field($model, 'claims_status') ?>

    <?php // echo $form->field($model, 'claims_status_name') ?>

    <?php // echo $form->field($model, 'master_created_date') ?>

    <?php // echo $form->field($model, 'master_updated_date') ?>

    <?php // echo $form->field($model, 'master_updated_by') ?>

    <?php // echo $form->field($model, 'master_updated_by_fullname') ?>

    <?php // echo $form->field($model, 'is_deleted') ?>

    <?php // echo $form->field($model, 'detail_id') ?>

    <?php // echo $form->field($model, 'ref_filename') ?>

    <?php // echo $form->field($model, 'ref_code') ?>

    <?php // echo $form->field($model, 'receipt_date') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'receipt_amount') ?>

    <?php // echo $form->field($model, 'amount_to_be_paid') ?>

    <?php // echo $form->field($model, 'detail_created_date') ?>

    <?php // echo $form->field($model, 'detail_updated_date') ?>

    <?php // echo $form->field($model, 'detail_updated_by') ?>

    <?php // echo $form->field($model, 'detail_updated_by_fullname') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
