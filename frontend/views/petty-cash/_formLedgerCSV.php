<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
?>

<div class="ledger-export-form">
    <?php $form = ActiveForm::begin(['id' => 'exportLedgerForm']); ?>

    <div class="row">
        <div class="col-lg-12">
            <?=
                    $form->field($model, 'startDate', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(DatePicker::className(), [
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                        'dateFormat' => 'dd/MM/yyyy',
                        'clientOptions' => [
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                        ],
            ]);
            ?>
            <?=
                    $form->field($model, 'endDate', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                    ->widget(DatePicker::className(), [
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                        'dateFormat' => 'dd/MM/yyyy',
                        'clientOptions' => [
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                        ],
            ]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::button('Export CSV', ['class' => 'btn btn-success float-right', 'id' => 'exportCsvButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function () {
        $('#exportCsvButton').on('click', function () {
            var form = $('#exportLedgerForm');
            var startDate = $('#pettycashledgermaster-startdate').val();
            var endDate = $('#pettycashledgermaster-enddate').val();

            if (!startDate || !endDate) {
                alert('Please select both start and end dates.');
                return;
            }

            $.ajax({
                url: form.attr('action') || '/office/petty-cash/ajax-export-ledger-csv?id=<?= $model->id ?>',
                type: 'POST',
                data: {startDate: startDate, endDate: endDate},
                xhrFields: {responseType: 'blob'},
                success: function (data) {
                    var blob = new Blob([data], {type: 'application/vnd.ms-excel'});
                    var filename = `Ledger_Report_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $model->createdBy->fullname) ?>_${startDate}_to_${endDate}.xls`;

                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    window.URL.revokeObjectURL(link.href);

                    setTimeout(() => window.location.reload(), 1000);
                },
                error: function () {
                    alert('Failed to export ledger report.');
                }
            });
        });
    });
</script>


