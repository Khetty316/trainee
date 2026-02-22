<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'myForm',
        'enableClientValidation' => true,
        'enableAjaxValidation' => false
    ]);
    ?>
    <div class="row">
        <div class="col-12">
            <?php
            $project_target_date = $model->projProdPanel->projProdMaster->current_target_date;

            if (!empty($project_target_date)) {
                echo "<div class='mb-3'><strong>Project Target Completion Date:</strong> " . date('d/m/Y', strtotime($project_target_date)) . '</div>';
                echo $form->field($model, 'new_target_date')->widget(yii\jui\DatePicker::className(), [
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'dd/mm/yyyy',
                        'id' => 'new-target-date',
                        'required' => true
                    ],
                    'dateFormat' => 'dd/MM/yyyy',
                    'clientOptions' => [
                        'minDate' => !empty($model->start_date) ? date('d/m/Y', strtotime($model->start_date)) : 0,
                        'maxDate' => !empty($project_target_date) ? date('d/m/Y', strtotime($project_target_date)) : 0,
                        'onSelect' => new yii\web\JsExpression("
                            function(dateText) {
                                validateTargetDate(dateText);
                            }
                        "),
                    ],
                ])->label('New Task Target Completion Date <span class="text-danger">*</span>', ['encode' => false]);
            } else {
                echo "<div class='mb-3'><strong>Project Target Completion Date:</strong> <i>Not Set</i></div>";
                echo $form->field($model, 'new_target_date')->widget(yii\jui\DatePicker::className(), [
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'dd/mm/yyyy',
                        'id' => 'new-target-date',
                        'required' => true
                    ],
                    'dateFormat' => 'dd/MM/yyyy',
                    'clientOptions' => [
                        'minDate' => !empty($model->start_date) ? date('d/m/Y', strtotime($model->start_date)) : 0,
                    ]
                ])->label('New Task Target Completion Date <span class="text-danger">*</span>', ['encode' => false]);
            }
            ?>
        </div>
    </div>
    <?= $form->field($model, 'remark_update_target_date')->textarea(['maxlength' => true, 'required' => true])->label('Remark <span class="text-danger">*</span>', ['encode' => false]) ?>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'onclick' => 'return submitForm()']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    function submitForm() {

<?php if (!empty($project_target_date)): ?>
            // Check target date validation
            var newTargetDate = $('#new-target-date').val();

            var errorDiv = $('#new-target-date-error');
            errorDiv.remove();
            $('#new-target-date').removeClass('is-invalid').closest('.form-group').removeClass('has-error');

            if (newTargetDate === '' || newTargetDate === null || newTargetDate === undefined) {
                $('#new-target-date').addClass('is-invalid').closest('.form-group').addClass('has-error');
                $('#new-target-date').after(
                        '<div id="new-target-date-error" class="invalid-feedback d-block">New Target Date cannot be blank.</div>'
                        );
                alert('Please select a target completion date.');
                return false;
            }

            if (newTargetDate) {
                var selectedDate = new Date(newTargetDate.split('/').reverse().join('-'));
                var projectTargetDate = new Date('<?php echo date('Y-m-d', strtotime($project_target_date)); ?>');

                if (selectedDate < projectTargetDate) {
                    $('#new-target-date').addClass('is-invalid').closest('.form-group').addClass('has-error');
                    $('#new-target-date').after(
                            '<div id="new-target-date-error" class="invalid-feedback d-block">Target completion date cannot be before project target completion date (<?php echo date('d/m/Y', strtotime($project_target_date)); ?>).</div>'
                            );
                    alert('Please correct the target completion date before submitting.');
                    return false;
                }
            }

            if ($('#new-target-date-error').length > 0) {
                alert('Please correct the target completion date before submitting.');
                return false;
            }
<?php else: ?>
            var newTargetDate = $('#new-target-date').val();

            var errorDiv = $('#new-target-date-error');
            errorDiv.remove();
            $('#new-target-date').removeClass('is-invalid').closest('.form-group').removeClass('has-error');

            if (newTargetDate === '' || newTargetDate === null || newTargetDate === undefined) {
                $('#new-target-date').addClass('is-invalid').closest('.form-group').addClass('has-error');
                $('#new-target-date').after(
                        '<div id="new-target-date-error" class="invalid-feedback d-block">New Target Date cannot be blank.</div>'
                        );
                alert('Please select a target completion date.');
                return false;
            }
<?php endif; ?>

        // Trigger Yii2 validation
        var $form = $("#myForm");

        if ($form.data("yiiActiveForm")) {
            $form.yiiActiveForm("validate");

            setTimeout(function () {
                var hasBootstrap4Errors = $form.find(".is-invalid").length > 0;
                var hasBootstrap3Errors = $form.find(".has-error").length > 0;
                var hasYiiErrors = $form.find(".field-error").length > 0;

                if (!hasBootstrap4Errors && !hasBootstrap3Errors && !hasYiiErrors) {
                    $form.submit();
                } else {
                    console.log('Form has errors, not submitting');
                }
            }, 300);
            return false;
        } else {
            console.log('No Yii ActiveForm detected, submitting normally');
            return true;
        }
    }

<?php if (!empty($project_target_date)): ?>
        function validateTargetDate(selectedDateText) {

            var selectedDate = new Date(selectedDateText.split('/').reverse().join('-'));
            var projectTargetDate = new Date('<?php echo date('Y-m-d', strtotime($project_target_date)); ?>');

            var errorDiv = $('#new-target-date-error');
            errorDiv.remove();
            $('#new-target-date').removeClass('is-invalid').closest('.form-group').removeClass('has-error');

            if (selectedDate < projectTargetDate) {
                $('#new-target-date').addClass('is-invalid').closest('.form-group').addClass('has-error');
                $('#new-target-date').after(
                        '<div id="new-target-date-error" class="invalid-feedback d-block">' +
                        'Target completion date cannot be before project target completion date (<?php echo date('d/m/Y', strtotime($project_target_date)); ?>)' +
                        '</div>'
                        );
                $('#new-target-date').val('');
                return false;
            }

            return true;
        }
<?php endif; ?>

    var remark = $('#tasksassign-remark_update_target_date').val().trim();

    $('#remark-error').remove();
    $('#tasksassign-remark_update_target_date').removeClass('is-invalid').closest('.form-group').removeClass('has-error');

    if (!remark || remark === '') {
        $('#tasksassign-remark_update_target_date').addClass('is-invalid').closest('.form-group').addClass('has-error');
        $('#tasksassign-remark_update_target_date').after(
                '<div id="remark-error" class="invalid-feedback d-block">Remark cannot be blank.</div>'
                );
        alert('Please enter a remark.');
        return false;
    }

    $(document).ready(function () {

        // Additional validation on date input blur
        $('#new-target-date').on('blur', function () {
            var dateValue = $(this).val();

            if (dateValue === '' || dateValue === null) {
                // Clear any existing errors first
                $('#new-target-date-error').remove();
                $('#new-target-date').removeClass('is-invalid').closest('.form-group').removeClass('has-error');

                // Add error styling and message
                $('#new-target-date').addClass('is-invalid').closest('.form-group').addClass('has-error');
                $('#new-target-date').after(
                        '<div id="new-target-date-error" class="invalid-feedback d-block">New Target Date cannot be blank.</div>'
                        );
            } else {
                // Clear errors if date is provided
                $('#new-target-date-error').remove();
                $('#new-target-date').removeClass('is-invalid').closest('.form-group').removeClass('has-error');
            }
        });
    });
</script>

<style>
    /* Ensure error messages are visible */
    .invalid-feedback.d-block {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    /* Additional styling for form groups with errors */
    .has-error .form-control {
        border-color: #dc3545;
    }

    .has-error .control-label {
        color: #dc3545;
    }
</style>