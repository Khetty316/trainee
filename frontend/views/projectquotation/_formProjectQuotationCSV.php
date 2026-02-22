<?php
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\jui\DatePicker;
?>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Export Selected Quotations</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="quotation-export-form">
                <?php $form = ActiveForm::begin(['id' => 'exportSelectedQuotationForm']); ?>
                <div class="row">
                    <div class="col-lg-12">
                        <p class="text-info">
                            <i class="fas fa-info-circle"></i> 
                            You have selected <strong id="selected-count-display">0</strong> quotation(s). 
                            Please select the date range to filter the export.
                        </p>
                        <?= $form->field($model, 'startDate', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                            ->widget(DatePicker::className(), [
                                'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                                'dateFormat' => 'dd/MM/yyyy',
                                'clientOptions' => [
                                    'showButtonPanel' => true,
                                    'closeText' => 'Close',
                                ],
                            ])->label('Start Date');
                        ?>
                        <?= $form->field($model, 'endDate', ['errorOptions' => ['class' => 'invalid-feedback-show']])
                            ->widget(DatePicker::className(), [
                                'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy'],
                                'dateFormat' => 'dd/MM/yyyy',
                                'clientOptions' => [
                                    'showButtonPanel' => true,
                                    'closeText' => 'Close',
                                ],
                            ])->label('End Date');
                        ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <?= Html::button('Export Excel', ['class' => 'btn btn-success', 'id' => 'exportSelectedQuotationButton']) ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    console.log('Modal loaded. Selected IDs:', window.selectedExportIds);
    
    // Display selected count
    if (window.selectedExportIds && window.selectedExportIds.length > 0) {
        $('#selected-count-display').text(window.selectedExportIds.length);
    } else {
        $('#selected-count-display').text('0');
        console.warn('No IDs found in window.selectedExportIds');
    }
    
    $('#exportSelectedQuotationButton').on('click', function () {
        var startDate = $('#dynamicmodel-startdate').val();
        var endDate = $('#dynamicmodel-enddate').val();
        
        console.log('Export button clicked. Start:', startDate, 'End:', endDate);
        
        if (!startDate || !endDate) {
            alert('Please select both start and end dates.');
            return;
        }
        
        // Verify IDs exist
        if (!window.selectedExportIds || window.selectedExportIds.length === 0) {
            alert('No quotations selected. Please select quotations first.');
            return;
        }
        
        console.log('Calling performExportWithDates with IDs:', window.selectedExportIds);
        
        // Call the global export function
        if (typeof window.performExportWithDates === 'function') {
            window.performExportWithDates(startDate, endDate);
        } else {
            console.error('performExportWithDates function not found!');
            alert('Export function not available. Please refresh the page.');
        }
    });
});
</script>