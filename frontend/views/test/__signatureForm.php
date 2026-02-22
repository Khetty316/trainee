<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

echo yii\jui\DatePicker::widget(['options' => ['class' => 'hidden']]);
?>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<style>
    .sign-block{
        position: relative;
        box-shadow: -0.5px -0.5px 0 0 #ccc,
            0.5px -0.5px 0 0 #ccc,
            -0.5px  0.5px 0 0 #ccc,
            0.5px  0.5px 0 0 #ccc;
    }
</style>
<div class="signature-form">
    <div class="form-row p-1 col-12">
        <?php
        ActiveForm::begin([
            'action' => ['save-witness', 'id' => $model->id],
            'options' => ['autocomplete' => 'off'],
        ]);
        ?>
        <table class="table table-sm table-bordered text-center">
            <thead>
                <tr>
                    <?php foreach ($witnessList as $key => $witness) { ?>
                        <th class="w-50">
                            <?= Html::textInput("testItemWitness[$key][witnessId]", $witness->id, ['class' => 'hidden']) ?>
                            <?= $witness->name ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php foreach ($witnessList as $key => $witness) { ?>
                        <td>
                            <?php
                            echo $this->render('__signatureFormItem', ['key' => $key, 'witness' => $witness]);
                            $this->registerJs("initializeSignaturePad($key);", \yii\web\View::POS_READY);
                            ?>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<?= count($witnessList) ?>">       
                        <?= Html::submitButton('Save Signature', ['class' => 'float-right btn btn-success mt-2 mb-2 able']) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <?php
        ActiveForm::end();
        ?>
    </div>
    <div>
        <br/><br/><br/>
    </div>
</div>
<script>
    var currentKey = <?= sizeof($witnessList) ?>;

    function initializeSignaturePad(key) {
        var canvas = document.getElementById('signature-pad-' + key);
        var signaturePad = new SignaturePad(canvas);

        if ($('#signature-data-' + key).val() === '') {
            $('#signature-pad-' + key).show();
        } else {
            $('#signature-pad-' + key).hide();
        }

        $('#clear-signature-' + key).on('click', function () {
            signaturePad.clear();
            $('#signature-image-' + key).attr('src', '').hide();
            $('#signature-data-' + key).val('');
            $('#signature-pad-' + key).show();
        });

        function onCanvasClick() {
            var signatureData = signaturePad.toDataURL();
            $('#signature-data-' + key).val(signatureData);
        }

        function onTouchEnd() {
            var signatureData = signaturePad.toDataURL();
            $('#signature-data-' + key).val(signatureData);
        }

        canvas.addEventListener('click', onCanvasClick);
        canvas.addEventListener('touchend', onTouchEnd);

    }
</script>