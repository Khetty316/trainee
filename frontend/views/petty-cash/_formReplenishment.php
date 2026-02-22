<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\pettyCash\PettyCashRequestMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="petty-cash-request-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?=
            $form->field($model, "amount_requested")
            ->input('number', [
                'class' => 'form-control text-right',
                'step' => 'any',
                'min' => '0.01',
                'value' => number_format($model->amount_requested, 2),
                'required' => true,
            ])
            ->label()
    ?>

    <?=
            $form->field($model, 'purpose')
            ->textArea(
                    [
                        'required' => true,
                    ]
            )
    ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
