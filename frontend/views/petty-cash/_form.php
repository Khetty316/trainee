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
            $form->field($preForm, "amount_requested")
            ->input('number', [
                'class' => 'form-control text-right',
                'step' => 'any',
                'min' => '0.01',
                'value' => number_format($preForm->amount_requested, 2),
                'required' => true,
            ])
            ->label()
    ?>

    <?=
            $form->field($preForm, 'purpose_of_advance')
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
