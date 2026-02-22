<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="petty-cash-request-master-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($detailLedger, 'ref_1')->textArea(['placeholder' => 'Enter reference 1 (optional)']) ?>
    
    <?= $form->field($detailLedger, 'ref_2')->textArea(['placeholder' => 'Enter reference 1 (optional)']) ?>
    
    <?= $form->field($detailLedger, 'description')->textArea(['placeholder' => 'Enter description (optional)']) ?>
    <?= 
    $form->field($detailLedger, 'debit')
        ->input('number', [
            'class' => 'form-control text-right',
            'value' => number_format($detailLedger->debit, 2, '.', ''), // ensure valid numeric format
            'step' => '0.01', // allows decimals
            'readonly' => true,
        ])
?>
    <div class="form-group">
        <?= Html::submitButton('Confirm & Save', ['class' => 'btn btn-success float-right mt-2 mb-2']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
