<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use frontend\models\office\leave\RefLeaveType;
use frontend\models\office\leave\RefLeaveStatus;
use frontend\models\office\claim\RefClaimType;
use frontend\models\office\claim\ClaimEntitlementDetails;

$disable = (!$model->isNewRecord);
?>

<div class="claim-master-form mt-3">

    <?php
    $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
    ]);
    ?>

    <div class='hidden'>
        <?php
        echo $form->field($model, 'claimant_id')->hiddenInput(["value" => Yii::$app->user->id])->label(false);
        ?>
    </div>
    <div class="form-row align-items-end">
        <div class="col-lg-4 col-sm-12 col-md-4">
            <div class="form-group mb-0">
                <?php
                echo $form->field($model, 'claim_type')->dropdownList(
                        $claimTypeList,
                        [
                            'prompt' => 'Select...',
                            'id' => 'claimTypeDropdown',
                            'required' => true,
                            'disabled' => $disable ? true : false,
                        ]
                );
                ?>
                <?php if (!$model->isNewRecord): ?>
                    <?=
                    Html::activeHiddenInput($model, 'claim_type', ['value' => $model->claim_type])
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div class="row">
                <div class="col-sm-12 col-md-4 forTravel">
                    <?php
                    $travelRefCodeList = [];
                    $travelRefRecords = frontend\models\office\leave\LeaveMaster::find()
                            ->select('leave_code')
                            ->where([
                                'requestor_id' => Yii::$app->user->identity->id,
                                'leave_type_code' => RefLeaveType::codeTravel,
                                'leave_status' => RefLeaveStatus::STS_APPROVED,
                                'claim_flag' => 0,
                            ])
                            ->all();
                    if ($travelRefRecords) {
                        $travelRefCodeList = ArrayHelper::map($travelRefRecords, 'leave_code', 'leave_code');
                    }

                    echo $form->field($model, 'wtf_code')->textInput([
                        'id' => 'travel-ref-code-input',
                        'placeholder' => 'Type to search WTR code...',
                        'disabled' => $disable ? true : false,
                        'value' => $model->ref_code
                    ])->label('WTR Code <small class="text-muted">(Work Traveling Requisition)</small> <span class="text-danger">*</span>');
                    ?>
                    <?php if (!$model->isNewRecord): ?>
                        <?=
                        Html::activeHiddenInput($model, 'wtf_code', ['value' => $model->ref_code])
                        ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mt-3 pt-3 forTravelDetail" id="travel-detail-container" class="mt-3" style="display: none;"></div>

                <?php
                $medicalRefCodeList = [];
                $medicalRefRecords = frontend\models\office\leave\LeaveMaster::find()
                        ->select('leave_code')
                        ->where([
                            'requestor_id' => Yii::$app->user->identity->id,
                            'leave_type_code' => RefLeaveType::codeSick,
                            'leave_status' => RefLeaveStatus::STS_APPROVED,
                            'claim_flag' => 0
                        ])
                        ->all();

                if ($medicalRefRecords) {
                    $medicalRefCodeList = ArrayHelper::map($medicalRefRecords, 'leave_code', 'leave_code');
                }

                $hasAvailableSickLeave = !empty($medicalRefCodeList);
                $shouldCheckByDefault = (($model->ref_code_sts == 1 || $model->ref_code_sts === null) ? true : false);
                ?>

                <div class="col-sm-12 col-md-3 col-lg-4 forMedical">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <?=
                                Html::activeCheckbox($model, 'ref_code_sts', [
                                    'id' => 'ref-code-checkbox',
                                    'disabled' => $disable ? true : false,
                                    'checked' => $shouldCheckByDefault,
                                    'uncheck' => '0',
                                    'value' => '1',
                                    'label' => false,
                                ])
                                ?>
                                Applied for Sick Leave
                            </label>
                        </div>
                    </div>
                    <div id="sick-leave-field" class="<?= ($shouldCheckByDefault) ? '' : 'hidden' ?>">
                        <?php
                        echo $form->field($model, 'sick_leave_code')->textInput([
                            'id' => 'medical-ref-code-input',
                            'placeholder' => 'Type to search Sick Leave code...',
                            'disabled' => $disable ? true : false,
                            'value' => $model->ref_code
                        ])->label('Sick Leave Code <span class="text-danger">*</span>');
                        ?>
                        <?php if (!$model->isNewRecord): ?>
                            <?= Html::activeHiddenInput($model, 'sick_leave_code', ['value' => $model->ref_code]) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                $this->registerJs("
    $('#ref-code-checkbox').on('change', function() {
        var isChecked = $(this).is(':checked');
        var sickLeaveField = $('#sick-leave-field');
        var sickLeaveInput = $('#medical-ref-code-input');
        
        if (isChecked) {
            sickLeaveField.removeClass('hidden').hide().slideDown();
            sickLeaveInput.attr('required', true); // Make it required
        } else {
            sickLeaveField.slideUp(function() {
                $(this).addClass('hidden');
                sickLeaveInput.val(''); // Clear the input
                sickLeaveInput.removeAttr('required'); // Remove required attribute
                sickLeaveInput.removeClass('is-invalid'); // Remove validation styling
            });
        }
    });
", \yii\web\View::POS_READY);
                ?>
                <div class="col-sm-12 col-md-12 col-lg-12 mt-3 pt-3 forMedicalDetail" id="medical-detail-container" class="mt-3" style="display: none;"></div>

                <div class="col-sm-12 col-md-3 col-lg-4 forMaterial">
                    <?php
                    $materialRefCodeList = [];
                    $materialRefRecords = frontend\models\office\preReqForm\PrereqFormMaster::find()->select('prf_no')->where(['created_by' => Yii::$app->user->identity->id, 'status' => \frontend\models\RefGeneralStatus::STATUS_Approved, 'claim_flag' => 0])->all();
                    if ($materialRefRecords) {
                        $materialRefCodeList = ArrayHelper::map($materialRefRecords, 'prf_no', 'prf_no');
                    }

                    echo $form->field($model, 'prf_code')->textInput([
                        'id' => 'material-ref-code-input',
                        'placeholder' => 'Type to search Pre-Requisition Form code...',
                        'disabled' => $disable ? true : false,
                        'value' => $model->ref_code
                    ])->label('Pre-Requisition Form Code <span class="text-danger">*</span>');
                    ?>
                    <?php if (!$model->isNewRecord): ?>
                        <?=
                        Html::activeHiddenInput($model, 'prf_code', ['value' => $model->ref_code])
                        ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mt-3 pt-3 forMaterialDetail" id="material-detail-container" class="mt-3" style="display: none;"></div>

                <div class="col-sm-12 col-md-3 col-lg-4 forProdotmeal">
                    <?php
                    $prodotmealRefCodeList = [];
                    $prodotmealRefRecords = frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::find()
                            ->select('ref_code')
                            ->where(['created_by' => Yii::$app->user->identity->id, 'status' => frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::STATUS_FINALIZE])
                            ->all();

                    if ($prodotmealRefRecords) {
                        $prodotmealRefCodeList = ArrayHelper::map($prodotmealRefRecords, 'ref_code', 'ref_code');
                    }

                    echo $form->field($model, 'prodotmeal_code')->textInput([
                        'id' => 'prodotmeal-ref-code-input',
                        'placeholder' => 'Type to search Production Overtime Meal Record code...',
                        'disabled' => $disable ? true : false,
                        'value' => $model->ref_code
                    ])->label('Production Overtime Meal Record Form Code <span class="text-danger">*</span>');
                    ?>
                    <?php if (!$model->isNewRecord): ?>
                        <?=
                        Html::activeHiddenInput($model, 'prodotmeal_code', ['value' => $model->ref_code])
                        ?>
                    <?php endif; ?>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-12 mt-3 pt-3 forProdotmealDetail" id="prodotmeal-detail-container" class="mt-3" style="display: none;"></div>

                <?=
                Html::activeHiddenInput($model, 'ref_code_sts', ['value' => $model->ref_code_sts])
                ?>
            </div>
        </div> 
        <div class="container col-sm-12 col-md-12 col-lg-12 mt-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Receipt Details</h6>
                </div>
                <div class="card-body p-2 table-responsive">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Receipt Upload</th>
                                <th>Receipt Date</th>
                                <th>Detail</th>
                                <th class="text-right">Receipt Amount (RM)</th>
                                <th class="text-right">Amount to be Paid (RM)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="receipt-table-body">
                            <?php foreach ($claimDetail as $index => $detail): ?>
                                <?php
                                $hasBeenPaid = ($detail->is_paid == 1);
                                if ($model->isNewRecord) {
                                    $isReadonly = false;
                                } else {
                                    $isReadonly = true;
                                }
                                ?>

                                <?php $gotReceipt = $detail->receipt_file; ?>
                                <?php $isTravelClaim = $model->claim_type === RefClaimType::codeTravel; ?>

                                <?php if (($detail->parent_id !== null && $isTravelClaim) || !$isTravelClaim): ?>
                                    <tr>
                                        <?php if (!$detail->isNewRecord): ?>
                                            <?= Html::hiddenInput("ClaimDetail[$index][id]", $detail->id) ?>
                                            <?= Html::hiddenInput("ClaimDetail[$index][is_deleted]", "0", ['class' => 'is-deleted-input']) ?>
                                        <?php endif; ?>
                                        <td>

                                            <div class="d-flex align-items-center">
                                                <div class="file-input-container <?= $gotReceipt ? 'd-none' : '' ?>" id="file-input-container-<?= $index ?>">
                                                    <?=
                                                            $form->field($detail, "[$index]scannedFile", ['options' => ['class' => 'mb-0 me-2']])
                                                            ->fileInput([
                                                                'class' => 'form-control',
                                                                'accept' => '.png, .jpg, .jpeg, .pdf',
                                                                'required' => !$gotReceipt,
                                                                'id' => "file-input-$index"
                                                            ])
                                                            ->label(false)
                                                    ?>
                                                    <small class="text-muted d-block mt-1" id="file-status-<?= $index ?>"></small>
                                                </div>

                                                <?php if ($gotReceipt): ?>
                                                    <div class="existing-file-controls ms-2" id="existing-file-controls-<?= $index ?>">
                                                        <?=
                                                        Html::a("<i class='far fa-file-alt fa-lg'></i>", "#",
                                                                [
                                                                    'title' => "View Current Receipt",
                                                                    "value" => ("/office/claim/get-file?filename=" . urlencode($detail->receipt_file)),
                                                                    "class" => "docModal"]);
                                                        ?>
                                                        <?=
                                                        $this->render('/_docModal')
                                                        ?> 
                                                        <?php if (!$isReadonly) { ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger remove-existing-file-btn" 
                                                                    data-index="<?= $index ?>"
                                                                    title="Remove existing receipt">
                                                                <i class='fas fa-trash'></i>
                                                            </button>
                                                        <?php } ?>

                                                        <?=
                                                        Html::hiddenInput("ClaimDetail[$index][remove_existing_file]", "0", [
                                                            'id' => "remove-existing-file-$index",
                                                            'class' => 'remove-existing-file-input'
                                                        ])
                                                        ?>

                                                        <small class="text-muted d-block mt-1" id="file-status-<?= $index ?>">
                                                            Current: <?= Html::encode($detail->receipt_file) ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td width="10%">
                                            <?=
                                                    $form->field($detail, "[$index]receipt_date", ['options' => ['class' => 'mb-0']])
                                                    ->input('date', [
                                                        'class' => 'form-control',
                                                        'readonly' => $isReadonly,
                                                        'value' => ($detail->receipt_date ? date('Y-m-d', strtotime($detail->receipt_date)) : null) ?? date('Y-m-d')
                                                    ])
                                                    ->label(false)
                                            ?>
                                        </td>

                                        <td width="23%">
                                            <?=
                                                    $form->field($detail, "[$index]detail", ['options' => ['class' => 'mb-0']])
                                                    ->textarea([
                                                        'class' => 'form-control',
                                                        'readonly' => $isReadonly,
                                                        'placeholder' => 'Enter detail',
                                                        'required' => true,
                                                        'rows' => 2
                                                    ])
                                                    ->label(false)
                                            ?>
                                        </td>

                                        <td>
                                            <?=
                                                    $form->field($detail, "[$index]receipt_amount", ['options' => ['class' => 'mb-0']])
                                                    ->input('number', [
                                                        'class' => 'form-control text-right receipt-amount',
                                                        'step' => 'any',
                                                        'min' => '0.01',
                                                        'value' => number_format($detail->receipt_amount, 2),
                                                        'required' => true,
                                                        'readonly' => $isReadonly,
                                                        'oninput' => 'updateAmountToBePaid()',
                                                    ])
                                                    ->label(false)
                                            ?>
                                        </td>

                                        <td>
                                            <?=
                                                    $form->field($detail, "[$index]amount_to_be_paid", ['options' => ['class' => 'mb-0']])
                                                    ->input('number', [
                                                        'class' => 'form-control text-right receipt-amount-to-be-paid',
                                                        'step' => 'any',
                                                        'readonly' => true,
                                                        'value' => number_format($detail->amount_to_be_paid, 2),
                                                    ])
                                                    ->label(false)
                                            ?>
                                        </td>

                                        <?php if (!$hasBeenPaid): ?>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="removeReceiptRow(this)">
                                                    <i class="fas fa-minus-circle"></i>
                                                </a>
                                            </td>
                                        <?php endif; ?>

                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>


                        <tr>
                            <th colspan="4" class="text-right">Total Amount To Be Paid (RM):</th>
                            <td>
                                <?=
                                $form->field($model, 'total_amount')->textInput([
                                    'id' => 'total-amount',
                                    'readonly' => true,
                                    'class' => 'form-control text-right'
                                ])->label(false)
                                ?>

                            </td>
                            <td></td>
                        </tr>
                    </table>
                    <button type="button" class="btn btn-primary mt-1" onclick="addReceiptRow()">Add Receipt <i class="fas fa-plus-circle"></i></button>

                </div>
            </div>

        </div>
        <div class="col-lg-8 col-sm-12 col-md-8" id="totalClaimBalance">
        </div>
    </div>
    <div class="form-row mt-5">
        <div class="col-sm-12 col-md-3">
            <?= $form->field($model, 'superior_id')->dropDownList($userList, ['value' => Yii::$app->user->identity->superior_id, 'disabled' => true])->label('Superior') ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <script>
        $('form').on('submit', function (e) {
            var isValid = true;

            $('tr').each(function () {
                const row = $(this);
                const fileInput = row.find('input[type="file"]');
                const removeInput = row.find('.remove-existing-file-input');

                if (fileInput.length && removeInput.length) {
                    const isRemoving = removeInput.val() === '1';
                    const hasNewFile = fileInput[0].files && fileInput[0].files.length > 0;
                    const isRequired = fileInput.attr('required');

                    if (isRemoving && !hasNewFile && isRequired) {
                        isValid = false;
                        fileInput.addClass('is-invalid');
                        alert('Please upload a new receipt file or keep the existing one.');
                        return false;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                return false;
            }
        });

        $(document).ready(function () {
            // Handle remove existing file button
            $(document).on('click', '.remove-existing-file-btn', function (e) {
                e.preventDefault();
                const index = $(this).data('index');
                const row = $(this).closest('tr');
                const fileInputContainer = $(`#file-input-container-${index}`);
                const fileInput = $(`#file-input-${index}`);
                const removeInput = $(`#remove-existing-file-${index}`);
                const statusText = $(`#file-status-${index}`);
                const existingControls = $(`#existing-file-controls-${index}`);

                if (confirm('Are you sure you want to remove the existing receipt? You will need to upload a new one.')) {
                    // Mark for removal
                    removeInput.val('1');

                    // Show file input and make it required
                    fileInputContainer.removeClass('d-none');
                    fileInput.attr('required', true);

                    // Update button to undo
                    $(this).removeClass('btn-outline-danger remove-existing-file-btn')
                            .addClass('btn-success undo-remove-file-btn');
                    $(this).html('<i class="fas fa-undo"></i>');
                    $(this).attr('title', 'Keep existing receipt');

                    // Update status
                    statusText.html('<span class="text-danger">Will be removed - Please upload new file</span>');
                }
            });

            // Handle undo remove existing file
            $(document).on('click', '.undo-remove-file-btn', function (e) {
                e.preventDefault();
                const index = $(this).data('index');
                const row = $(this).closest('tr');
                const fileInputContainer = $(`#file-input-container-${index}`);
                const fileInput = $(`#file-input-${index}`);
                const removeInput = $(`#remove-existing-file-${index}`);
                const statusText = $(`#file-status-${index}`);
                const originalFileName = fileInput.data('original-filename') || 'existing file';

                // Unmark for removal
                removeInput.val('0');

                // Hide file input and make it optional
                fileInputContainer.addClass('d-none');
                fileInput.attr('required', false);
                fileInput.val(''); // Clear any selected file

                // Restore button
                $(this).removeClass('btn-success undo-remove-file-btn').addClass('btn-outline-danger remove-existing-file-btn');
                $(this).html('<i class="fas fa-trash"></i>');
                $(this).attr('title', 'Remove existing receipt');

                // Restore status
                statusText.html(`Current: <?= Html::encode($detail->receipt_file) ?>`);
            });

            // Handle file selection feedback
            $(document).on('change', 'input[type="file"][name*="scannedFile"]', function () {
                const input = $(this);
                const index = input.attr('id').replace('file-input-', '');
                const statusText = $(`#file-status-${index}`);
                const removeInput = $(`#remove-existing-file-${index}`);

                if (input[0].files && input[0].files[0]) {
                    const fileName = input[0].files[0].name;

                    // If there's a remove input (meaning there was an existing file)
                    if (removeInput.length) {
                        statusText.html(`<span class="text-info">Will replace with: ${fileName}</span>`);
                    } else {
                        // For new records, just show the selected file name
                        statusText.html(`<span class="text-success">Selected: ${fileName}</span>`);
                    }
                } else {
                    // File was cleared
                    if (removeInput.length && removeInput.val() === '1') {
                        statusText.html('<span class="text-danger">Will be removed - Please upload new file</span>');
                    }
                }
            });
        });

// Function for dynamically added rows (when adding new receipts)
        window.addReceiptRow = function () {
            const tbody = document.getElementById('receipt-table-body');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
        <td>
            <div class="d-flex align-items-center">
                <div class="file-input-container" id="file-input-container-${receiptIndex}">
                    <input type="file" 
                           name="ClaimDetail[${receiptIndex}][scannedFile]" 
                           id="file-input-${receiptIndex}"
                           class="form-control" 
                           required 
                           accept=".png, .jpg, .jpeg, .pdf">
                    <small class="text-muted d-block mt-1" id="file-status-${receiptIndex}"></small>
                </div>
            </div>
        </td>
        <td>
            <input type="date" name="ClaimDetail[${receiptIndex}][receipt_date]" value="${today}" class="form-control">
        </td>
        <td>
            <textarea name="ClaimDetail[${receiptIndex}][detail]" class="form-control" placeholder="Enter detail" required rows="2"></textarea>
        </td>
        <td>
            <input type="number" step="any" min="0.01" value="0.00" name="ClaimDetail[${receiptIndex}][receipt_amount]" class="form-control text-right receipt-amount" required>
        </td>
        <td>
            <input type="number" step="0.01" value="0.00" name="ClaimDetail[${receiptIndex}][amount_to_be_paid]" class="form-control text-right receipt-amount-to-be-paid" readonly>
        </td>
        <td>
            <a href="javascript:void(0)" class="btn btn-danger btn-sm" onclick="removeReceiptRow(this)"><i class="fas fa-minus-circle"></i></a>
        </td>
    `;
            tbody.appendChild(newRow);
            receiptIndex++;
        };
    </script>
    <script>
        $(document).ready(function () {

            // Declare these variables at the top so they're available to all script blocks
            var travelRefCodes = <?= json_encode(array_values($travelRefCodeList)) ?>;
            var medicalRefCodes = <?= json_encode(array_values($medicalRefCodeList)) ?>;
            var materialRefCodes = <?= json_encode(array_values($materialRefCodeList)) ?>;
            var prodotmealRefCodes = <?= json_encode(array_values($prodotmealRefCodeList)) ?>;

            setTimeout(function () {
                const selectedClaimType = $('#claimTypeDropdown').val();
                const claimMasterId = '<?= $model->id ?>';
                // For prodotmeal claims - directly call the function
                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeProdOTMeal ?>') {
                    const prodotmealRefCode = $('#prodotmeal-ref-code-input').val().trim();

                    if (prodotmealRefCode !== '') {
                        loadProdotmealDetails(prodotmealRefCode);
                    }
                }

                // For travel claims - directly call the function
                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeTravel ?>') {
                    const travelRefCode = $('#travel-ref-code-input').val().trim();
                    if (travelRefCode !== '') {
                        loadTravelDetails(travelRefCode, claimMasterId);
                    }
                }

                // For medical claims
                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMedical ?>') {
                    var hasSickLeave = $('#ref-code-checkbox').is(':checked');

                    if (hasSickLeave) {
                        var medicalRefCode = $('#medical-ref-code-input').val().trim();
                        if (!medicalRefCode) {
                            $('#medical-ref-code-input').addClass('is-invalid');
                            errorMessages.push('Sick leave code is required when "Applied for Sick Leave" is checked.');
                            isValid = false;
                        }
                    }
                }

                // For material and repair claims
                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMaterial ?>' || selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeRepair ?>') {
                    const materialRefCode = $('#material-ref-code-input').val().trim();
                    if (materialRefCode !== '') {
                        loadPreReqFormDetails(materialRefCode);
                    }
                }
            }, 800); // Increased delay to ensure everything loads

            // Medical ref code change handler
            $('#medical-ref-code-input').on('change', function () {
                const leaveCode = $(this).val().trim();
                const selectedClaimType = $('#claimTypeDropdown').val();

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMedical ?>') {
                    if (leaveCode !== '' && $.inArray(leaveCode, medicalRefCodes) !== -1) {
                        loadSickLeaveDetails(leaveCode);
                    } else {
                        $('#medical-detail-container').hide().empty();
                    }
                } else {
                    $('#medical-detail-container').hide().empty();
                }
            });

            // Travel ref code change handler
            $('#travel-ref-code-input').on('change', function () {
                const leaveCode = $(this).val().trim();
                const selectedClaimType = $('#claimTypeDropdown').val();
                const claimMasterId = '<?= $model->id ?>';

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeTravel ?>') {
                    if (leaveCode !== '' && $.inArray(leaveCode, travelRefCodes) !== -1) {
                        // Load the details immediately
                        loadTravelDetails(leaveCode, claimMasterId);
                    } else {
                        $('#travel-detail-container').hide().empty();
                    }
                } else {
                    $('#travel-detail-container').hide().empty();
                }
            });

            // Material ref code change handler
            $('#material-ref-code-input').on('change', function () {
                const prfCode = $(this).val().trim();
                const selectedClaimType = $('#claimTypeDropdown').val();

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMaterial ?>' || selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeRepair ?>') {
                    if (prfCode !== '' && $.inArray(prfCode, materialRefCodes) !== -1) {
                        loadPreReqFormDetails(prfCode);
                    } else {
                        $('#material-detail-container').hide().empty();
                    }
                } else {
                    $('#material-detail-container').hide().empty();
                }
            });

            $('#prodotmeal-ref-code-input').on('change', function () {
                const prodotmealCode = $(this).val().trim();
                const selectedClaimType = $('#claimTypeDropdown').val();
                console.log(prodotmealCode);
                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeProdOTMeal ?>') {
                    if (prodotmealCode !== '' && $.inArray(prodotmealCode, prodotmealRefCodes) !== -1) {

                        loadProdotmealDetails(prodotmealCode);
                    } else {
                        $('#prodotmeal-detail-container').hide().empty();
                    }
                } else {
                    $('#prodotmeal-detail-container').hide().empty();
                }
            });

            function loadProdotmealDetails(prodotmealCode) {
                if (!prodotmealCode)
                    return;

                $.ajax({
                    url: '/office/claim/ajax-prodotmeal-detail',
                    type: 'GET',
                    data: {prodotmealCode: prodotmealCode},
                    beforeSend: function () {
                        $('#prodotmeal-detail-container').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                    },
                    success: function (response) {
                        $('#prodotmeal-detail-container').html(response).show();
                    },
                    error: function () {
                        $('#prodotmeal-detail-container').html('<div class="alert alert-danger">Error loading details</div>');
                    }
                });
            }

            function loadSickLeaveDetails(leaveCode) {
                if (!leaveCode)
                    return;

                $.ajax({
                    url: '/office/claim/ajax-sick-leave-detail',
                    type: 'GET',
                    data: {leaveCode: leaveCode},
                    beforeSend: function () {
                        $('#medical-detail-container').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                    },
                    success: function (response) {
                        $('#medical-detail-container').html(response).show();
                    },
                    error: function () {
                        $('#medical-detail-container').html('<div class="alert alert-danger">Error loading details</div>');
                    }
                });
            }

            function loadTravelDetails(leaveCode, claimMasterId) {
                if (!leaveCode)
                    return;

                $.ajax({
                    url: '/office/claim/ajax-travel-detail',
                    type: 'GET',
                    data: {
                        leaveCode: leaveCode,
                        claimMasterId: claimMasterId
                    },
                    beforeSend: function () {
                        $('#travel-detail-container').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                    },
                    success: function (response) {
                        $('#travel-detail-container').html(response).show();
                    },
                    error: function () {
                        $('#travel-detail-container').html('<div class="alert alert-danger">Error loading details</div>');
                    }
                });
            }

            function loadPreReqFormDetails(prfCode) {
                if (!prfCode)
                    return;

                $.ajax({
                    url: '/office/claim/ajax-prf-detail',
                    type: 'GET',
                    data: {prfCode: prfCode},
                    beforeSend: function () {
                        $('#material-detail-container').html('<div class="text-center p-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
                    },
                    success: function (response) {
                        $('#material-detail-container').html(response).show();
                    },
                    error: function () {
                        $('#material-detail-container').html('<div class="alert alert-danger">Error loading details</div>');
                    }
                });
            }

            function toggleFields() {
                var selectedClaimType = $('#claimTypeDropdown').val();

                // Hide all conditional fields by default and remove required attributes
                $('.forTravel').hide();
                $('.forMedical').hide();
                $('.forMaterial').hide();
                $('.forProdotmeal').hide();
                $('.forTravelDetail').hide();
                $('.forMedicalDetail').hide();
                $('.forMaterialDetail').hide();
                $('.forProdotmealDetail').hide();
                $('#prodotmeal-ref-code-input, #travel-ref-code-input, #medical-ref-code-input, #material-ref-code-input').removeAttr('required').removeClass('is-invalid');

                // Show fields and add required attributes based on selected claim type
                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeProdOTMeal ?>') {
                    $('.forProdotmeal').show();
                    $('#prodotmeal-ref-code-input').attr('required', true);
                    setTimeout(function () {
                        $('#prodotmeal-ref-code-input').trigger('change');
                    }, 50);
                }

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeTravel ?>') {
                    $('.forTravel').show();
                    $('#travel-ref-code-input').attr('required', true);
                    setTimeout(function () {
                        $('#travel-ref-code-input').trigger('change');
                    }, 50);
                }

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMedical ?>') {
                    $('.forMedical').show();
                    $('#medical-ref-code-input').attr('required', true);
                    setTimeout(function () {
                        $('#medical-ref-code-input').trigger('change');
                    }, 50);
                }

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMaterial ?>' || selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeRepair ?>') {
                    $('.forMaterial').show();
                    $('#material-ref-code-input').attr('required', true);
                    setTimeout(function () {
                        $('#material-ref-code-input').trigger('change');
                    }, 50);
                }
            }

            // Initial call and change event
            toggleFields();
            $('#claimTypeDropdown').change(toggleFields);

            // Form validation on submit
            $('form').on('submit', function (e) {
                var isValid = true;
                var selectedClaimType = $('#claimTypeDropdown').val();

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeProdOTMeal ?>') {
                    var prodotmealRefCode = $('#prodotmeal-ref-code-input').val().trim();
                    if (!prodotmealRefCode) {
                        $('#prodotmeal-ref-code-input').addClass('is-invalid');
                        isValid = false;
                    }
                }

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeTravel ?>') {
                    var travelRefCode = $('#travel-ref-code-input').val().trim();
                    if (!travelRefCode) {
                        $('#travel-ref-code-input').addClass('is-invalid');
                        isValid = false;
                    }
                }

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMedical ?>') {
                    var hasSickLeave = $('#has-sick-leave-checkbox').is(':checked');

                    if (hasSickLeave) {
                        var medicalRefCode = $('#medical-ref-code-input').val().trim();
                        if (!medicalRefCode) {
                            $('#medical-ref-code-input').addClass('is-invalid');
                            errorMessages.push('Sick leave code is required when "Applied for Sick Leave" is checked.');
                            isValid = false;
                        }
                    }
                }

                if (selectedClaimType === '<?= frontend\models\office\claim\RefClaimType::codeMaterial ?>') {
                    var materialRefCode = $('#material-ref-code-input').val().trim();
                    if (!materialRefCode) {
                        $('#material-ref-code-input').addClass('is-invalid');
                        isValid = false;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in the required reference code field.');
                    return false;
                }
            });

            $('#prodotmeal-ref-code-input, #travel-ref-code-input, #medical-ref-code-input, #material-ref-code-input').on('input', function () {
                $(this).removeClass('is-invalid');
            });

            // Function to setup autocomplete with validation
            function setupStrictAutocomplete(inputId, codeList, fieldName) {
                const $input = $(inputId);
                const $container = $input.parent();

                const dropdownId = inputId.replace('#', '') + '-dropdown';
                if (!$('#' + dropdownId).length) {
                    $container.append(`<div id="${dropdownId}" class="autocomplete-dropdown" style="display:none; position:absolute; background:white; border:1px solid #ccc; max-height:200px; overflow-y:auto; z-index:1000; width:100%;"></div>`);
                }

                const $dropdown = $('#' + dropdownId);

                $input.on('input', function () {
                    const value = $(this).val().toLowerCase();
                    $dropdown.empty();

                    if (value.length > 0) {
                        const matches = codeList.filter(code =>
                            code.toLowerCase().includes(value)
                        ).slice(0, 10);

                        if (matches.length > 0) {
                            matches.forEach(match => {
                                $dropdown.append(`<div class="autocomplete-item" style="padding:8px; cursor:pointer; border-bottom:1px solid #eee;" data-value="${match}">${match}</div>`);
                            });
                            $dropdown.show();
                        } else {
                            $dropdown.hide();
                        }
                    } else {
                        $dropdown.hide();
                    }
                });

                $dropdown.on('click', '.autocomplete-item', function () {
                    const value = $(this).data('value');
                    $input.val(value);
                    $input.removeClass('is-invalid');
                    $dropdown.hide();
                    $input.trigger('change');
                });

                const isNewRecord = <?= $model->isNewRecord ? 'true' : 'false' ?>;

                function validateCurrentValue() {
                    const value = $input.val();
                    if (value) {
                        if ($.inArray(value, codeList) === -1) {
                            // only add 'is-invalid' if it's a new record
                            if (isNewRecord) {
                                $input.addClass('is-invalid');
                            }
                        } else {
                            $input.removeClass('is-invalid');
                        }
                    }
                }

                validateCurrentValue();

                $input.on('blur', function () {
                    setTimeout(() => {
                        const value = $(this).val();
                        if (value && $.inArray(value, codeList) === -1) {
                            $(this).val('');
                            $(this).addClass('is-invalid');
                            alert('Please select a valid ' + fieldName + ' code from the list.');
                        } else if (value) {
                            $(this).removeClass('is-invalid');
                        }
                        $dropdown.hide();
                    }, 200);
                });

                $(document).on('click', function (e) {
                    if (!$(e.target).closest($container).length) {
                        $dropdown.hide();
                    }
                });

                $input.on('change', validateCurrentValue);
            }

            // Setup autocomplete for all thrdr fields
            setupStrictAutocomplete('#prodotmeal-ref-code-input', prodotmealRefCodes, 'Production Overtime Meal Record');
            setupStrictAutocomplete('#travel-ref-code-input', travelRefCodes, 'WTR');
            setupStrictAutocomplete('#medical-ref-code-input', medicalRefCodes, 'Sick Leave');
            setupStrictAutocomplete('#material-ref-code-input', materialRefCodes, 'Pre-Requisition Form');

        });

        $(document).ready(function () {
            updateTotalToBePaid();

            // Clear the balance display initially
            $('#totalClaimBalance').html('');

            // Handle claim type dropdown changes
            $('#claimTypeDropdown').on('change', function () {
                $('.receipt-amount').val('0.00');
                $('.receipt-amount-to-be-paid').val('0.00');
                updateAmountToBePaid();
//                updateMaterialAmountsFromElement();
                $('#totalClaimBalance').html('');
            });

            $(document).on('change', 'input[name*="[date]"]', function () {
                updateTotalToBePaid();
                updateAmountToBePaid();
//                updateMaterialAmountsFromElement();
            });

            $('#ref-code-checkbox').on('change', function () {
                $('#prodotmeal-ref-code-input, #travel-ref-code-input, #medical-ref-code-input, #material-ref-code-input').removeAttr('required').removeClass('is-invalid');

            });

        });

        let receiptIndex = <?= count($claimDetail) ?>; // Start from existing rows count

        // Get today's date in YYYY-MM-DD format for JavaScript
        const today = new Date().toISOString().split('T')[0];

        // Event delegation for amount input changes
        $(document).on('input', '.receipt-amount', function () {
            updateAmountToBePaid();
//            updateMaterialAmountsFromElement();
        });

        // Event delegation for remove buttons
        $(document).on('click', 'a[onclick*="removeReceiptRow"]', function (e) {
            e.preventDefault();
            removeReceiptRow(this);
        });

        function updateAmountToBePaid() {
            $('#receipt-table-body').removeClass('disabled').css({
                'opacity': '1',
                'pointer-events': 'auto'
            });
            var selectedClaimType = $('#claimTypeDropdown').val();

            if (selectedClaimType === '<?= RefClaimType::codeProdOTMeal ?>') {
                const prodotmealCode = $('#prodotmeal-ref-code-input').val().trim();
                // Clear other claim type displays
                $('#totalClaimBalance').html('');

                // Copy receipt_amount → amount_to_be_paid for each row
                document.querySelectorAll('.receipt-amount').forEach(function (input) {
                    const amountValue = parseFloat(input.value) || 0;
                    const amountPaidInput = input.closest('tr').querySelector('input[name*="[amount_to_be_paid]"]');
                    if (amountPaidInput) {
                        amountPaidInput.value = amountValue.toFixed(2);
                    }
                });

                // Fetch the total amount limit and apply limit
                getTotalAmountLimitProdotmeal(prodotmealCode);
            }

            if (selectedClaimType === '<?= RefClaimType::codeTravel ?>' || selectedClaimType === '<?= RefClaimType::codeDirector ?>' || selectedClaimType === '<?= RefClaimType::codeMeal ?>' || selectedClaimType === '<?= RefClaimType::codeMaterial ?>') {
                // Clear the display for other claim types
                $('#totalClaimBalance').html('');

                // copy amount to amount_paid
                document.querySelectorAll('.receipt-amount').forEach(function (input, index) {
                    const amountValue = parseFloat(input.value) || 0;
                    const amountPaidInput = input.closest('tr').querySelector('input[name*="[amount_to_be_paid]"]');
                    if (amountPaidInput) {
                        amountPaidInput.value = amountValue.toFixed(2);
                    }
                });
            }
            var receiptDates = [];
            $('input[name*="[receipt_date]"]').each(function () {
                if ($(this).val()) {
                    receiptDates.push($(this).val());
                }
            });

            if (selectedClaimType === '<?= RefClaimType::codeMedical ?>' || selectedClaimType === '<?= RefClaimType::codePetrol ?>' || selectedClaimType === '<?= RefClaimType::codeTelephone ?>' || selectedClaimType === '<?= RefClaimType::codeRepair ?>') {
                // Get all unique months from receipt dates
                const uniqueMonths = [...new Set(receiptDates.map(date => date.slice(0, 7)))]; // Get YYYY-MM format

                if (uniqueMonths.length === 0) {
                    $('#receipt-table-body').addClass('disabled').css({
                        'opacity': '0.5',
                        'pointer-events': 'none'
                    });
                    $('#totalClaimBalance').html('<span class="text-danger">No receipt dates found</span>');
                    return;
                }

                // Fetch limits for all unique months
                fetchMonthlyLimitsForAllMonths(uniqueMonths, selectedClaimType);
            }

            if (selectedClaimType === '<?= RefClaimType::codeExecOTMeal ?>') {
                if (receiptDates.length === 0) {
                    $('#receipt-table-body').addClass('disabled').css({
                        'opacity': '0.5',
                        'pointer-events': 'none'
                    });
                    $('#totalClaimBalance').html('<span class="text-danger">No receipt dates found</span>');
                    return;
                }

                // Just use the first receipt date — daily limit only
                fetchPerdayLimits(receiptDates, selectedClaimType);
            }

            updateTotalToBePaid();
        }

        async function getTotalAmountLimitProdotmeal(prodotmealCode) {
            try {
                const response = await $.getJSON('ajax-get-prod-ot-meal-limit', {
                    prodotmealCode: prodotmealCode // match backend parameter
                });

                if (response.success && response.limit) {
                    updateProdOtMealAmountsToBePaid(response.limit);
                } else {
//                    console.warn('Failed to get OT meal limit:', response);
                }
            } catch (err) {
//                console.error('Error fetching OT meal limit:', err);
            }
        }

        function updateProdOtMealAmountsToBePaid(limit) {
            let totalPaid = 0;
            const rows = document.querySelectorAll('#receipt-table-body tr');

            rows.forEach(function (row) {
                const amountInput = row.querySelector('input[name*="[receipt_amount]"]');
                const paidInput = row.querySelector('input[name*="[amount_to_be_paid]"]');
                const idInput = row.querySelector('input[name*="[id]"]');

                if (idInput && idInput.value)
                    return;

                const receiptAmount = parseFloat(amountInput?.value || 0);

                if (totalPaid >= limit) {
                    if (paidInput)
                        paidInput.value = '0.00';
                    return;
                }

                if (totalPaid + receiptAmount > limit) {
                    const remaining = limit - totalPaid;
                    if (paidInput)
                        paidInput.value = remaining.toFixed(2);
                    totalPaid += remaining;
                } else {
                    if (paidInput)
                        paidInput.value = receiptAmount.toFixed(2);
                    totalPaid += receiptAmount;
                }
            });

            updateTotalToBePaid();
        }

        async function fetchPerdayLimits(receiptDates, claimTypeCode) {
            const limits = {};

            for (const date of receiptDates) {
                const response = await $.getJSON('ajax-get-exec-ot-meal-limit', {
                    receiptDate: date,
                    claimTypeCode: claimTypeCode
                });
                if (response.success) {
                    limits[date] = {
                        perdayLimit: response.perdayLimit || 0,
                        claimedAmount: response.claimedAmount || 0,
                        perdayBalance: response.perdayBalance || 0
                    };

                    // Auto-update the "amount to be paid" field in the table
                    updateExecOtMealAmountsToBePaid(limits);
                } else {
                    limits[date] = {
                        perdayLimit: 0,
                        claimedAmount: 0,
                        perdayBalance: 0
                    };
                }
            }

            // Show summary
            displayExecOtMealSummary(limits);
        }

        function updateExecOtMealAmountsToBePaid(limits) {
            const rowsByDate = {};

            document.querySelectorAll('#receipt-table-body tr').forEach(function (row) {
                const dateInput = row.querySelector('input[name*="[receipt_date]"]');
                const amountInput = row.querySelector('input[name*="[receipt_amount]"]');
                const paidInput = row.querySelector('input[name*="[amount_to_be_paid]"]');
                const idInput = row.querySelector('input[name*="[id]"]');

                if (idInput && idInput.value)
                    return;
                if (!dateInput || !amountInput || !paidInput)
                    return;

                const dateVal = dateInput.value;
                const amountVal = parseFloat(amountInput.value || 0);
                if (!dateVal) {
                    paidInput.value = '0.00';
                    return;
                }

                // Group rows by date (not period)
                if (!rowsByDate[dateVal])
                    rowsByDate[dateVal] = [];
                rowsByDate[dateVal].push({row, amountVal, paidInput});
            });

            // Loop through each date group
            for (const [dateVal, rows] of Object.entries(rowsByDate)) {
                const dayData = limits[dateVal];
                if (!dayData) {
                    rows.forEach(item => item.paidInput.value = '0.00');
                    continue;
                }

                const perdayLimit = parseFloat(dayData.perdayLimit) || 0;
                const claimedAmount = parseFloat(dayData.claimedAmount) || 0;
                let remainingBalance = Math.max(perdayLimit - claimedAmount, 0);

                // Sort by row order or amount if needed
                for (const item of rows) {
                    if (remainingBalance <= 0) {
                        item.paidInput.value = '0.00';
                        continue;
                    }

                    const allowed = Math.min(item.amountVal, remainingBalance);
                    item.paidInput.value = allowed.toFixed(2);
                    remainingBalance -= allowed;
                }
            }

            updateTotalToBePaid();
        }

        function displayExecOtMealSummary(receiptLimits) {
            let summaryHtml = "";

            if (Object.keys(receiptLimits).length === 0) {
                $('#totalClaimBalance').html("<em>No receipt dates found.</em>");
                return;
            }

            summaryHtml += `<strong>Executive Overtime Meal Summary (Per Day):</strong><br>`;

            for (const [receiptDate, data] of Object.entries(receiptLimits)) {
                summaryHtml += `
        <div style="margin-top: 5px;">
            <strong>${formatDate(receiptDate)}</strong> - 
            Limit: RM ${data.perdayLimit.toFixed(2)} | 
            Claimed: RM ${data.claimedAmount.toFixed(2)} | 
            <span class="text-success">Balance: RM ${data.perdayBalance.toFixed(2)}</span>
        </div>`;
            }

            $('#totalClaimBalance').html(summaryHtml);
        }

        function fetchMonthlyLimitsForAllMonths(uniqueMonths, claimTypeCode) {
            const monthlyLimits = {};
            let completedRequests = 0;
            let hasError = false;


            // ✅ For medical claims, fetch limits for each unique month
            if (claimTypeCode === '<?= RefClaimType::codeMedical ?>') {
                const medicalPeriods = {};

                uniqueMonths.forEach(function (month) {
                    const representativeDate = month + '-01';

                    $.ajax({
                        url: '<?= \yii\helpers\Url::to(['/office/claim/ajax-get-medical-limit']) ?>',
                        data: {
                            receiptDate: representativeDate,
                            claimTypeCode: claimTypeCode
                        },
                        dataType: 'json',
                        success: function (response) {
                            completedRequests++;

                            if (response.success) {
                                const periodKey = response.periodYear + '-' + String(response.periodMonth).padStart(2, '0');

                                // Store the period info (avoid duplicates)
                                if (!medicalPeriods[periodKey]) {
                                    medicalPeriods[periodKey] = {
                                        perReceiptLimit: response.perReceiptLimit,
                                        monthlyLimit: response.monthlyLimit,
                                        monthlyBalance: response.monthlyBalance,
                                        claimedAmount: response.claimedAmount,
                                        periodMonth: response.period_month,
                                        periodYear: response.period_year,
                                    };
                                }
                            } else {
                                hasError = true;
                            }

                            // Check if all requests completed
                            if (completedRequests === uniqueMonths.length) {
                                if (hasError) {
                                    $('#receipt-table-body').addClass('disabled').css({
                                        'opacity': '0.5',
                                        'pointer-events': 'none'
                                    });
                                    $('#totalClaimBalance').html('<span class="text-danger">Failed to fetch medical limits</span>');
                                } else {
                                    $('#receipt-table-body').removeClass('disabled').css({
                                        'opacity': '1',
                                        'pointer-events': 'auto'
                                    });
                                    updateMedicalAmountsToBePaidWithDualLimits(medicalPeriods);
                                    displayMedicalSummary(medicalPeriods);
                                }
                            }
                        },
                        error: function () {
                            completedRequests++;
                            hasError = true;

                            if (completedRequests === uniqueMonths.length) {
                                $('#receipt-table-body').addClass('disabled').css({
                                    'opacity': '0.5',
                                    'pointer-events': 'none'
                                });
                                $('#totalClaimBalance').html('<span class="text-danger">Failed to fetch medical limits</span>');
                            }
                        }
                    });
                });

                return;
            }

            // ✅ For monthly claims (petrol, telephone, etc.)
            uniqueMonths.forEach(function (month) {
                const representativeDate = month + '-01';

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['/office/claim/ajax-get-monthly-limit']) ?>',
                    data: {
                        receiptDate: representativeDate,
                        claimTypeCode: claimTypeCode
                    },
                    dataType: 'json',
                    success: function (response) {
                        completedRequests++;

                        if (response.success) {
                            monthlyLimits[month] = {
                                limitSts: response.no_limit_sts,
                                limit: response.claimLimit,
                                entitlementAmount: response.entitlementAmount,
                                periodMonth: response.period_month,
                                periodYear: response.period_year,
                            };
                        } else {
                            hasError = true;
                            monthlyLimits[month] = {
                                error: response.message,
                                limit: 0
                            };
                        }

                        if (completedRequests === uniqueMonths.length) {
                            processAllMonthlyLimits(monthlyLimits, hasError);
                        }
                    },
                    error: function () {
                        completedRequests++;
                        hasError = true;
                        monthlyLimits[month] = {
                            error: 'Failed to fetch limit',
                            limit: 0
                        };

                        if (completedRequests === uniqueMonths.length) {
                            processAllMonthlyLimits(monthlyLimits, hasError);
                        }
                    }
                });
            });
        }

        function updateMedicalAmountsToBePaidWithDualLimits(medicalPeriods) {
            function getPeriodKeyForDate(dateStr) {
                const date = new Date(dateStr);
                const month = date.getMonth() + 1;
                const year = date.getFullYear();
                return `${year}-${String(month).padStart(2, '0')}`;
            }

            const rowsByPeriod = {};

            document.querySelectorAll('#receipt-table-body tr').forEach(function (row) {
                const dateInput = row.querySelector('input[name*="[receipt_date]"]');
                const amountInput = row.querySelector('input[name*="[receipt_amount]"]');
                const paidInput = row.querySelector('input[name*="[amount_to_be_paid]"]');
                const idInput = row.querySelector('input[name*="[id]"]');

                if (idInput && idInput.value)
                    return;
                if (!dateInput || !amountInput || !paidInput)
                    return;

                const dateVal = dateInput.value;
                const amountVal = parseFloat(amountInput.value || 0);
                if (!dateVal) {
                    paidInput.value = '0.00';
                    return;
                }

                const periodKey = getPeriodKeyForDate(dateVal);
                if (!rowsByPeriod[periodKey])
                    rowsByPeriod[periodKey] = [];
                rowsByPeriod[periodKey].push({row, dateVal, amountVal, paidInput});
            });

            for (const [periodKey, rows] of Object.entries(rowsByPeriod)) {
                const periodData = medicalPeriods[periodKey];
                if (!periodData) {
                    rows.forEach(item => item.paidInput.value = '0.00');
                    continue;
                }

                const perReceiptLimit = parseFloat(periodData.perReceiptLimit);
                let remainingMonthlyBalance = parseFloat(periodData.monthlyBalance);

                // Sort by date (oldest first)
                rows.sort((a, b) => a.dateVal.localeCompare(b.dateVal));

                for (const item of rows) {
                    if (remainingMonthlyBalance <= 0) {
                        item.paidInput.value = '0.00';
                        continue;
                    }

                    const allowed = Math.min(item.amountVal, perReceiptLimit, remainingMonthlyBalance);
                    item.paidInput.value = allowed.toFixed(2);
                    remainingMonthlyBalance -= allowed;
                }
            }

            updateTotalToBePaid();
        }

        function displayMedicalSummary(medicalPeriods) {
            let summaryHtml = "";

            const firstPeriod = Object.values(medicalPeriods)[0];
            if (firstPeriod) {
                summaryHtml += `<strong>Medical Claim Summary (Max RM ${firstPeriod.perReceiptLimit.toFixed(2)}/receipt, RM ${firstPeriod.monthlyLimit.toFixed(2)}/month):</strong><br>`;
            }

            for (const [periodKey, data] of Object.entries(medicalPeriods)) {
                summaryHtml += `
        <div style="margin-top: 5px;">
            <strong>${periodKey}</strong>: Monthly Limit RM ${data.monthlyLimit.toFixed(2)} | 
            Claimed: RM ${data.claimedAmount.toFixed(2)} | 
            <span class="text-success">Balance: RM ${data.monthlyBalance.toFixed(2)}</span>
        </div>`;
            }

            $('#totalClaimBalance').html(summaryHtml);
        }

        function processAllMonthlyLimits(monthlyLimits, hasError) {
            if (hasError) {
                // Check if any months have errors
                const errorMessages = [];
                for (const [month, data] of Object.entries(monthlyLimits)) {
                    if (data.error) {
                        errorMessages.push(`${month}: ${data.error}`);
                    }
                }

                if (errorMessages.length > 0) {
                    $('#receipt-table-body').addClass('disabled').css({
                        'opacity': '0.5',
                        'pointer-events': 'none'
                    });
                    $('#totalClaimBalance').html('<span class="text-danger">Errors: ' + errorMessages.join('; ') + '</span>');
                    return;
                }
            }

            // Enable the tbody
            $('#receipt-table-body').removeClass('disabled').css({
                'opacity': '1',
                'pointer-events': 'auto'
            });

            // Update amounts with monthly limits
            updateMonthlyAmountsToBePaidWithLimits(monthlyLimits);

            // Display summary
            displayMonthlySummary(monthlyLimits);
        }

        function updateMonthlyAmountsToBePaidWithLimits(monthlyLimits) {
            const rowsByMonth = {};
            const noLimitStatus = <?= ClaimEntitlementDetails::noLimitAmountSts ?>;

            // Group receipts by month (only new or editable receipts)
            document.querySelectorAll('.receipt-amount').forEach(function (input) {
                const row = input.closest('tr');
                const dateInput = row.querySelector('input[name*="[receipt_date]"]');
                const idInput = row.querySelector('input[name*="[id]"]'); // Existing record if present
                const dateValue = dateInput?.value;
                const amount = parseFloat(input.value) || 0;

                // Skip if this is an already saved record (so we don’t reapply limit)
                if (idInput && idInput.value) {
                    return;
                }

                if (dateValue) {
                    const month = dateValue.slice(0, 7); // YYYY-MM
                    if (!rowsByMonth[month]) {
                        rowsByMonth[month] = [];
                    }
                    rowsByMonth[month].push({input, row, amount});
                }
            });

            // Process each month group
            for (const [month, entries] of Object.entries(rowsByMonth)) {
                const monthData = monthlyLimits[month];
                const hasNoLimit = monthData && monthData.limitSts == noLimitStatus;
                const limit = hasNoLimit ? Infinity : (monthData ? monthData.limit : 0);

                let monthlyTotal = entries.reduce((sum, e) => sum + e.amount, 0);

                // If no limit or within limit
                if (hasNoLimit || monthlyTotal <= limit) {
                    entries.forEach(e => {
                        const paidInput = e.row.querySelector('input[name*="[amount_to_be_paid]"]');
                        if (paidInput)
                            paidInput.value = e.amount.toFixed(2);
                    });
                } else {
                    // Apply limit restriction
                    let remainingLimit = limit;

                    // Sort entries by date (earliest first)
                    entries.sort((a, b) => {
                        const dateA = a.row.querySelector('input[name*="[receipt_date]"]')?.value || '';
                        const dateB = b.row.querySelector('input[name*="[receipt_date]"]')?.value || '';
                        return dateA.localeCompare(dateB);
                    });

                    // Distribute remaining limit
                    for (const entry of entries) {
                        const paidInput = entry.row.querySelector('input[name*="[amount_to_be_paid]"]');
                        if (!paidInput)
                            continue;

                        if (entry.amount <= remainingLimit) {
                            paidInput.value = entry.amount.toFixed(2);
                            remainingLimit -= entry.amount;
                        } else if (remainingLimit > 0) {
                            paidInput.value = remainingLimit.toFixed(2);
                            remainingLimit = 0;
                        } else {
                            paidInput.value = '0.00';
                        }
                    }
                }
            }

            updateTotalToBePaid();
        }

        function displayMonthlySummary(monthlyLimits) {
            let summaryHtml = '<div class="monthly-summary">';
            const noLimitStatus = <?= ClaimEntitlementDetails::noLimitAmountSts ?>;

            for (const [month, data] of Object.entries(monthlyLimits)) {
                const monthName = new Date(month + '-01').toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long'
                });
                if (data.error) {
                    summaryHtml += `<div class="alert alert-danger">${monthName}: ${data.error}</div>`;
                } else {
                    const hasNoLimit = data.limitSts == noLimitStatus;

                    if (hasNoLimit) {
                        summaryHtml += `
                    <div class="month-summary">
                        <strong>${monthName}</strong>: 
                        Entitlement amount: RM ${data.entitlementAmount.toFixed(2)} |
                        Claimed: RM ${data.claimedAmount.toFixed(2)} | 
                        <span class="text-success bold"><i>No limit</i></span>
                    </div>
                `;
                    } else {
                        summaryHtml += `
                    <div class="month-summary">
                        <strong>${monthName}</strong>: 
                        Entitlement amount: RM ${data.entitlementAmount.toFixed(2)} |
                        <span class="text-success">Balance: RM ${data.limit.toFixed(2)}</span>
                    </div>
                `;
                    }
                }
            }

            summaryHtml += '</div>';
            $('#totalClaimBalance').html(summaryHtml);
        }

        function updateMaterialAmountsFromElement() {
            const selectedClaimType = $('#claimTypeDropdown').val();
            if (selectedClaimType === '<?= RefClaimType::codeMaterial ?>') {
                const totalAmountText = $('#totalAmountPrf').text();

                const totalAmount = parseFloat(totalAmountText.replace(/,/g, '')) || 0;

                if (totalAmount > 0) {
                    updateMaterialAmountsToBePaid(totalAmount);
                }
            }
        }

        function updateMaterialAmountsToBePaid(totalAmount) {
            let runningTotal = 0;
            const receiptInputs = document.querySelectorAll('.receipt-amount');

            receiptInputs.forEach(function (input) {
                const amountValue = parseFloat(input.value) || 0;
                const amountPaidInput = input.closest('tr').querySelector('input[name*="[amount_to_be_paid]"]');

                if (amountPaidInput) {
                    // Check if adding this amount would exceed the total limit
                    if (runningTotal + amountValue <= totalAmount) {
                        // Safe to add the full amount
                        amountPaidInput.value = amountValue.toFixed(2);
                        runningTotal += amountValue;
                    } else {
                        // Calculate the remaining amount that can be allocated
                        const remainingAmount = totalAmount - runningTotal;
                        if (remainingAmount > 0) {
                            amountPaidInput.value = remainingAmount.toFixed(2);
                            runningTotal = totalAmount; // We've reached the limit
                        } else {
                            amountPaidInput.value = '0.00';
                        }
                    }
                }
            });

            // Optional: Show a warning if total receipt amounts exceed the limit
            const totalReceiptAmount = Array.from(receiptInputs).reduce((sum, input) => {
                return sum + (parseFloat(input.value) || 0);
            }, 0);

            if (totalReceiptAmount > totalAmount) {
                console.warn(`Total receipt amount (${totalReceiptAmount.toFixed(2)}) exceeds limit (${totalAmount.toFixed(2)})`);
                // $('#warning-message').show().text('Total receipt amount exceeds the allowed limit');
            }

            updateTotalToBePaid();
        }

        function updateTotalToBePaid() {
            let total = 0;
            document.querySelectorAll('input[name*="[amount_to_be_paid]"]').forEach(function (input) {
                const val = parseFloat(input.value) || 0;
                total += val;
            });
            document.getElementById('total-amount').value = total.toFixed(2);
        }


        window.removeReceiptRow = function (element) {
            const row = element.closest('tr');
            row.remove();
            updateTotalToBePaid();
            updateAmountToBePaid();
//            updateMaterialAmountsFromElement();
        };

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-GB'); // → dd/mm/yyyy
        }

    </script>
