<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="petty-cash-request-master-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'ledger-credit-form',
        'action' => ['finance-confirm-receipt-completed', 'id' => $model->id],
        'options' => ['class' => 'ledger-form']
    ]);
    ?>

    <?=
    $form->field($detailLedger, 'voucher_no')->textInput([
        'class' => 'form-control',
        'readonly' => true,
    ])
    ?>

    <?= $form->field($detailLedger, 'ref_1')->textArea(['placeholder' => 'Enter reference 1 (optional)']) ?>

    <?= $form->field($detailLedger, 'ref_2')->textArea(['placeholder' => 'Enter reference 1 (optional)']) ?>

    <?= $form->field($detailLedger, 'description')->textArea(['placeholder' => 'Enter description (optional)']) ?>
    <?=
            $form->field($detailLedger, 'credit')
            ->textInput([
                'class' => 'form-control text-right',
                'value' => number_format($detailLedger->credit, 2),
                'readonly' => true,
            ])
    ?>

    <div class="form-group">
        <?= Html::submitButton('Confirm & Save', ['class' => 'btn btn-success float-right mt-2 mb-2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
