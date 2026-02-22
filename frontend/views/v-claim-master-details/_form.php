<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\models\office\leave\RefLeaveType;
use frontend\models\office\leave\RefLeaveStatus;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimsMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="claims-master-form">

    <?php
    $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{label} <div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
        ],
        'options' => ['enctype' => 'multipart/form-data', 'autocomplete' => 'on'],
    ]);
    ?>
    <div class='hidden'>
        <?php
        echo $form->field($model, 'claimant_id')->hiddenInput(["value" => Yii::$app->user->id])->label(false);
        if (!$model->isNewRecord) {
//            echo $form->field($model, 'claims_detail_id')->hiddenInput()->label(false);
        }
        ?>
    </div>
    <div class="form-row">
        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'claim_type')->dropdownList($claimTypeList, ['prompt' => 'Select...', 'id' => 'claimTypeDropdown']) ?>
            <p class="text-right"><span id="totalClaimBalance"></span></p>
        </div>

        <div class="col-sm-12 col-md-4">
            <?= $form->field($model, 'scannedFile')->fileInput()->label('Receipt') ?>
            <?= $model->ref_filename == "" ? "" : Html::a('<i class="far fa-file-alt" ></i>', '/working/claim/get-file?filename=' . urlencode($model->ref_filename), ['target' => '_blank', 'title' => "Receipt"]) ?>
        </div>
    </div>



    <div class="form-row">
        <div class="col-sm-12 col-md-3">
            <?=
                    $form->field($model, 'receipt_date')
                    ->widget(yii\jui\DatePicker::className(), ['options' => ['class' => 'form-control'], 'dateFormat' => 'dd/MM/yyyy', 'id' => 'dateFrom'])
            ?>
        </div>

        <div class="col-sm-12 col-md-3 forTravel">
            <?php
            $travelRefCodeList = [];
            $travelRefRecords = frontend\models\office\leave\LeaveMaster::find()
                    ->select('leave_code')
                    ->where([
                        'requestor_id' => Yii::$app->user->identity->id,
                        'leave_type_code' => RefLeaveType::codeTravel,
                        'leave_status' => RefLeaveStatus::STS_APPROVED
                    ])
                    ->all();
            if ($travelRefRecords) {
                $travelRefCodeList = ArrayHelper::map($travelRefRecords, 'leave_code', 'leave_code');
            }

            echo $form->field($model, 'ref_code')->textInput([
                'id' => 'travel-ref-code-input',
                'placeholder' => 'Type to search WTR code...',
                'readonly' => false
            ])->label('WTR Code <small class="text-muted">(Work Traveling Requisition)</small>');
            ?>
        </div>

        <div class="col-sm-12 col-md-3 forMedical">
            <?php
            $medicalRefCodeList = [];
            $medicalRefRecords = frontend\models\office\leave\LeaveMaster::find()
                    ->select('leave_code')
                    ->where([
                        'requestor_id' => Yii::$app->user->identity->id,
                        'leave_type_code' => RefLeaveType::codeSick,
                        'leave_status' => RefLeaveStatus::STS_APPROVED
                    ])
                    ->all();
            if ($medicalRefRecords) {
                $medicalRefCodeList = ArrayHelper::map($medicalRefRecords, 'leave_code', 'leave_code');
            }

            echo $form->field($model, 'ref_code')->textInput([
                'id' => 'medical-ref-code-input',
                'placeholder' => 'Type to search Sick Leave code...',
                'readonly' => false
            ])->label('Sick Leave Code');
            ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'description')->textInput() ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <?=
            $form->field($model, 'receipt_amount')->textInput([
                'id' => 'receipt-amount-input',
                'type' => 'number',
                'step' => '0.01',
                'min' => '0',
                'class' => 'form-control text-right'
            ])
            ?>
        </div>
        <div class="col-sm-12 col-md-3">
            <?=
            $form->field($model, 'amount_to_be_paid')->textInput([
                'id' => 'amount-to-be-paid-input',
                'readonly' => true,
                'placeholder' => 'Auto calculated',
                'class' => 'form-control text-right'
            ])
            ?>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'superior_id')->dropDownList($userList, ['prompt' => 'Select...', 'value' => Yii::$app->user->identity->superior_id, 'disabled' => true])->label('Superior') ?>
        </div>
    </div>

    <div class="form-group">
       <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>

    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
    function save() {
        // Optional: check if any fields have .has-error
        if ($('.has-error').length === 0) {
            $('#claimForm').submit();
        } else {
            alert('Please fix validation errors before submitting.');
        }
    }
    $(document).ready(function () {
        function toggleFields() {
            var selectedClaimType = $('#claimTypeDropdown').val();

            // Hide all conditional fields by default
            $('.forTravel').hide();
            $('.forMedical').hide();

            // Show fields based on selected claim type
            if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeTravel ?>' || selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeAccommodation ?>') {
                $('.forTravel').show();
            }

            if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMedical ?>') {
                $('.forMedical').show();
            }
        }

        // Initial call and change event
        toggleFields();
        $('#claimTypeDropdown').change(toggleFields);
    });

    $(document).ready(function () {
        // Travel codes
        var travelRefCodes = <?= json_encode(array_values($travelRefCodeList)) ?>;

        // Medical codes  
        var medicalRefCodes = <?= json_encode(array_values($medicalRefCodeList)) ?>;

        // Function to setup autocomplete with validation
        function setupStrictAutocomplete(inputId, codeList, fieldName) {
            $(inputId).autocomplete({
                source: codeList,
                minLength: 1,
                select: function (event, ui) {
                    $(this).val(ui.item.value);
                    return false;
                },
                change: function (event, ui) {
                    if (!ui.item) {
                        var value = $(this).val();
                        if ($.inArray(value, codeList) === -1) {
                            $(this).val('');
                            $(this).focus();
                            alert('Please select a valid ' + fieldName + ' code from the list.');
                        }
                    }
                },
                focus: function (event, ui) {
                    return false;
                }
            });

            $(inputId).on('blur', function () {
                var value = $(this).val();
                if (value && $.inArray(value, codeList) === -1) {
                    $(this).val('');
                    alert('Please select a valid ' + fieldName + ' code from the list.');
                }
            });
        }

        // Setup autocomplete for both fields
        setupStrictAutocomplete('#travel-ref-code-input', travelRefCodes, 'WTR');
        setupStrictAutocomplete('#medical-ref-code-input', medicalRefCodes, 'Sick Leave');
    });

    $(document).ready(function () {
        // Medical claim limit
        const MEDICAL_CLAIM_LIMIT = <?= frontend\models\office\claim\RefClaimType::medicalLimitPerReceipt ?>;

        // Function to calculate amount to be paid
        function calculateAmountToBePaid() {
            var receiptAmount = parseFloat($('#receipt-amount-input').val()) || 0;
            var amountToBePaid = 0;
            var selectedClaimType = $('#claimTypeDropdown').val();

            if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMedical ?>') {
                amountToBePaid = Math.min(receiptAmount, MEDICAL_CLAIM_LIMIT);
            } else {
                amountToBePaid = receiptAmount;
            }

            $('#amount-to-be-paid-input').val(amountToBePaid.toFixed(2));
        }

        // Trigger calculation when receipt amount changes
        $('#receipt-amount-input').on('input keyup blur', function () {
            calculateAmountToBePaid();
        });

        // Also trigger when claim type changes (if applicable)
        $('#claimTypeDropdown').on('change', function () {  // Changed this line
            calculateAmountToBePaid();
        });

        // Initial calculation on page load
        calculateAmountToBePaid();
    });
</script>