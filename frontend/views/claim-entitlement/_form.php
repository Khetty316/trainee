<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use frontend\models\RefGeneralStatus;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\claim\ClaimEntitlement */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="claim-entitlement-form">
    <?php $form = ActiveForm::begin(['id' => 'form-edit']); ?>
    <?php if (!$model->isNewRecord): ?>
        <div class="alert alert-info border-left-primary mb-4">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-2">
                            <strong class="text-muted">Staff No:</strong>
                            <span class="ml-1"><?= Html::encode($model->user->staff_id) ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">Name:</strong>
                            <span class="ml-2 text-dark font-weight-bold"><?= Html::encode($model->user->fullname) ?></span>
                        </div>
                        <div class="col-md-2">
                            <strong class="text-muted">Year:</strong>
                            <span class="ml-2 badge badge-success"><?= Html::encode($selectYear) ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong class="text-muted">Status:</strong>
                            <span class="ml-2 text-dark font-weight-bold">
                                <?= Html::encode($model->status0->status_name) ?>
                            </span>                      
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="form-section bg-light p-3 rounded">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-user-cog mr-2"></i>
                        Staff & Year Selection
                    </h6>
                    <div class="row">
                        <div class="col-lg-7 col-md-12 mb-3">
                            <label class="font-weight-bold text-muted">Staff Name</label>
                            <?=
                            Html::dropDownList(
                                    'user_id',
                                    array_key_first($staffList),
                                    $staffList,
                                    ['class' => 'form-control']
                            )
                            ?>
                        </div>
                        <div class="col-lg-5 col-md-12 mb-3">
                            <label class="font-weight-bold text-muted">Year</label>
                            <div class="input-group">
                                <?=
                                Html::dropDownList('selectYear', $selectYear, $yearsList, [
                                    'class' => 'form-control'
                                ])
                                ?>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row mt-3">
        <?php
        $month = [
            '1' => 'January', '2' => 'February', '3' => 'March',
            '4' => 'April', '5' => 'May', '6' => 'June',
            '7' => 'July', '8' => 'August', '9' => 'September',
            '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        ?>

        <div class="container-fluid">
            <div class="row">
                <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_HR]) && $hr) { ?>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Claim Types</h5>
                            <button type="button" class="btn btn-success btn-sm" id="addClaimType">
                                <i class="fas fa-plus mr-1"></i> Add Claim Type
                            </button>
                        </div>

                        <div id="claimTypesContainer">
                            <?php foreach ($claimDetails as $key => $value): ?>
                                <div class="card mb-3 shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 font-weight-bold text-primary">
                                            <i class="fas fa-file-invoice-dollar mr-2"></i>
                                            <?= Html::encode($value->claimTypeCode->claim_name) ?>
                                        </h6>
                                        <button type="button" class="btn btn-danger btn-sm remove-claim-type" title="Remove this claim type">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <?= Html::hiddenInput("ClaimEntitlementDetails[$key][claim_type_code]", $value->claim_type_code) ?>
                                        <div class="row align-items-center">
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <label class="form-label small text-muted font-weight-bold">Amount Per Month</label>
                                                <?=
                                                Html::textInput("ClaimEntitlementDetails[$key][amount]",
                                                        number_format((float) ($value->amount ?: 0), 2, '.', ''),
                                                        [
                                                            'class' => 'form-control text-right amount-input',
                                                            'type' => 'number',
                                                            'placeholder' => '0.00',
                                                            'required' => true,
                                                            'step' => 'any',
                                                            'min' => '0',
                                                            'readonly' => ($value->no_limit == frontend\models\office\claim\ClaimEntitlementDetails::noLimitAmountSts ? true : false)
                                                        ])
                                                ?>

                                                <?=
                                                Html::hiddenInput("ClaimEntitlementDetails[$key][no_limit]",
                                                        ($value->no_limit ?? 0) == frontend\models\office\claim\ClaimEntitlementDetails::noLimitAmountSts ?
                                                                frontend\models\office\claim\ClaimEntitlementDetails::noLimitAmountSts : 0.00,
                                                        ['class' => 'no-limit-value'])
                                                ?>
                                            </div>

                                            <div class="col-lg-4 col-md-6 mb-3">
                                                <label class="form-label small text-muted font-weight-bold">Period</label>
                                                <div class="row align-items-center">
                                                    <div class="col-5">
                                                        <?=
                                                        Html::dropDownList("ClaimEntitlementDetails[$key][month_start]",
                                                                $value->month_start ?? '', $month, [
                                                            'class' => 'form-control form-control-sm'
                                                                ])
                                                        ?>
                                                    </div>
                                                    <div class="col-2 text-center">
                                                        <i class="fas fa-arrow-right text-muted"></i>
                                                    </div>
                                                    <div class="col-5">
                                                        <?=
                                                        Html::dropDownList("ClaimEntitlementDetails[$key][month_end]",
                                                                $value->month_end ?? '12', $month, [
                                                            'class' => 'form-control form-control-sm'
                                                                ])
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-5 col-md-12 mb-3">
                                                <label class="form-label small text-muted font-weight-bold">Remark</label>
                                                <?=
                                                Html::textArea("ClaimEntitlementDetails[$key][remark]",
                                                        $value->remark ?? '', [
                                                    'class' => 'form-control',
                                                    'rows' => 2,
                                                    'placeholder' => 'Optional remarks...'
                                                        ])
                                                ?>
                                            </div>

                                            <div class="col-lg-12 col-md-12 mb-3">
                                                <div class="form-check mb-2">
                                                    <?=
                                                    Html::checkbox(
                                                            "ClaimEntitlementDetails[$key][no_limit_check]",
                                                            ($value->no_limit == \frontend\models\office\claim\ClaimEntitlementDetails::noLimitAmountSts ? true : false),
                                                            [
                                                                'class' => 'form-check-input no-limit-checkbox',
                                                                'id' => "noLimit_$key",
                                                            ]
                                                    )
                                                    ?>
                                                    <label class="form-check-label small text-success font-weight-bold" for="noLimit_<?= $key ?>">
                                                        No Limit
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php } else if (!$hr) { ?>
                    <div class="col-12">
                        <?php foreach ($claimDetails as $key => $value): ?>
                            <div class="card mb-3 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 font-weight-bold text-primary">
                                        <i class="fas fa-file-invoice-dollar mr-2"></i>
                                        <?= Html::encode($value->claimTypeCode->claim_name) ?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <label class="form-label small text-muted font-weight-bold">Amount Per Month</label>
                                            <div class="form-control-plaintext font-weight-bold text-success">
                                                <?php
                                                if ($value->no_limit == frontend\models\office\claim\ClaimEntitlementDetails::noLimitAmountSts) {
                                                    echo "<i>(No Limit)</i>";
                                                } else {
                                                    echo \common\models\myTools\MyFormatter::asDecimal2($value->amount ?: "0.00");
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-4 col-md-6 mb-3">
                                            <label class="form-label small text-muted font-weight-bold">Period</label>
                                            <div class="form-control-plaintext">
                                                <?php
                                                $startMonth = $month[$value->month_start ?? ''] ?? 'Not Set';
                                                $endMonth = $month[$value->month_end ?? '12'] ?? 'December';
                                                ?>
                                                <span class="badge badge-primary"><?= $startMonth ?></span>
                                                <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                <span class="badge badge-primary"><?= $endMonth ?></span>
                                            </div>
                                        </div>

                                        <div class="col-lg-5 col-md-12 mb-3">
                                            <label class="form-label small text-muted font-weight-bold">Remark</label>
                                            <div class="form-control-plaintext">
                                                <?= Html::encode($value->remark ?? 'No remarks') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'user_id')->hiddenInput()->label(false) ?>
    <div class="form-group">
        <div class="mt-3 text-right">
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior]) && $hr) { ?>
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?php if (!$model->isNewRecord) { ?>
                    <?=
                       Html::a("Deactivate", 'javascript:deactivateUser("' . $model->id . '")', ['class' => 'btn btn-warning float-right ml-1']);

                    ?>
                <?php } ?>
            <?php } else if ($model->superior_id == Yii::$app->user->id && !$hr && ($model->status != RefGeneralStatus::STATUS_SuperiorRejected && $model->status != RefGeneralStatus::STATUS_Approved)) { ?>

                <?=
                Html::a("Reject",
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['ajax-superior-approval', 'id' => $model->id, 'status' => RefGeneralStatus::STATUS_SuperiorRejected]),
                            "class" => "modalButtonMedium btn btn-danger mx-1",
                            'data-modaltitle' => "Reject"
                        ]
                )
                ?>
                <?=
                Html::a("Approve",
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['ajax-superior-approval', 'id' => $model->id, 'status' => RefGeneralStatus::STATUS_Approved]),
                            "class" => "modalButtonMedium btn btn-success mx-1",
                            'data-modaltitle' => "Approve"
                        ]
                )
                ?>

            <?php } ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    function deactivateUser(id) {
        var ans = confirm("Are you sure to Deactivate the entitlement?");
        if (ans) {
            $("#form-edit").attr('action', '/office/claim-entitlement/deactivate?id=' + id);
            $("#form-edit").submit();
        }
    }
    
    $(document).ready(function () {
        let claimTypeIndex = <?= count($claimType) ?>;

        // Available claim types and months (get from PHP)
        const availableClaimTypes = <?= json_encode($claimType) ?>;
        const monthOptions = <?= json_encode($month) ?>;
        const noLimitValue = <?= frontend\models\office\claim\ClaimEntitlementDetails::noLimitAmountSts ?>;

        // Handle no limit checkbox change
        $(document).on('change', '.no-limit-checkbox', function () {
            const card = $(this).closest('.card');
            const amountInput = card.find('.amount-input');
            const noLimitHidden = card.find('.no-limit-value');

            if ($(this).is(':checked')) {
                // No limit selected
                amountInput.val('0.00').prop('readonly', true).addClass('bg-light');
                noLimitHidden.val(noLimitValue);
            } else {
                // Amount limit selected
                amountInput.prop('readonly', false).removeClass('bg-light');
                noLimitHidden.val(0);
            }
        });

        // Initialize existing checkboxes
        $('.no-limit-checkbox').each(function () {
            if ($(this).is(':checked')) {
                const card = $(this).closest('.card');
                card.find('.amount-input').prop('readonly', true).addClass('bg-light');
            }
        });

        // Remove claim type
        $(document).on('click', '.remove-claim-type', function () {
            if ($('.card').length > 1) {
                $(this).closest('.card').remove();
            } else {
                alert('At least one claim type is required.');
            }
        });

        // Add claim type
        $('#addClaimType').click(function () {
            showClaimTypeSelector();
        });

        function getUsedClaimTypeCodes() {
            const usedCodes = [];
            $('.card').each(function () {
                const code = $(this).find('input[name*="[claim_type_code]"]').val();
                if (code) {
                    usedCodes.push(code);
                }
            });
            return usedCodes;
        }

        function getAvailableClaimTypes() {
            const usedCodes = getUsedClaimTypeCodes();
            const available = {};

            Object.entries(availableClaimTypes).forEach(([key, value]) => {
                if (!usedCodes.includes(value.code)) {
                    available[key] = value;
            }
            });

            return available;
        }

        function showClaimTypeSelector() {
            const availableForSelection = getAvailableClaimTypes();

            if (Object.keys(availableForSelection).length === 0) {
                alert('All claim types have already been added.');
                return;
            }

            const modalHtml = `
                             <div class="modal fade" id="claimTypeModal" tabindex="-1">
                                 <div class="modal-dialog">
                                     <div class="modal-content">
                                         <div class="modal-header">
                                             <h5 class="modal-title">Select Claim Type</h5>
                                             <button type="button" class="close" data-dismiss="modal">
                                                 <span>&times;</span>
                                             </button>
                                         </div>
                                         <div class="modal-body">
                                             <select class="form-control" id="claimTypeSelector">
                                                 <option value="">Select a claim type...</option>
                                                 ${Object.entries(availableForSelection).map(([key, value]) =>
                    `<option value="${key}">${value.claim_name}</option>`
            ).join('')}
                                             </select>
                                         </div>
                                         <div class="modal-footer">
                                             <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                             <button type="button" class="btn btn-primary" id="addSelectedClaimType">Add</button>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         `;

            $('#claimTypeModal').remove();
            $('body').append(modalHtml);
            $('#claimTypeModal').modal('show');

            $('#addSelectedClaimType').click(function () {
                const selectedKey = $('#claimTypeSelector').val();
                if (selectedKey && availableClaimTypes[selectedKey]) {
                    addClaimTypeCard(selectedKey, availableClaimTypes[selectedKey]);
                    $('#claimTypeModal').modal('hide');
                } else {
                    alert('Please select a claim type.');
                }
            });
        }

        function addClaimTypeCard(originalKey, claimTypeData) {
            const newKey = claimTypeIndex++;
            const monthStartOptions = Object.entries(monthOptions).map(([key, value]) =>
                    `<option value="${key}">${value}</option>`
            ).join('');

            const monthEndOptions = Object.entries(monthOptions).map(([key, value]) =>
                    `<option value="${key}" ${key == '12' ? 'selected' : ''}>${value}</option>`
            ).join('');

            const amountSection =
                    `<input type="number" name="ClaimEntitlementDetails[${newKey}][amount]" 
                                     class="form-control text-right amount-input" placeholder="0.00" 
                                     required step="any" min="0">
                              <input type="hidden" name="ClaimEntitlementDetails[${newKey}][no_limit]" value="0" class="no-limit-value">`;

            const cardHtml = `
                             <div class="card mb-3" data-key="${newKey}">
                                 <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                     <h6 class="mb-0 font-weight-bold text-primary">
                                         <i class="fas fa-file-invoice-dollar mr-2"></i>
                                         ${claimTypeData.claim_name}
                                     </h6>
                                     <button type="button" class="btn btn-danger btn-sm remove-claim-type" title="Remove this claim type">
                                         <i class="fas fa-times"></i>
                                     </button>
                                 </div>
                                 <div class="card-body">
                                     <input type="hidden" name="ClaimEntitlementDetails[${newKey}][claim_type_code]" value="${claimTypeData.code}">

                                     <div class="row align-items-center">
                                         <div class="col-lg-3 col-md-6 mb-3">
                                             <label class="form-label small text-muted font-weight-bold">Amount Per Month</label>
                                             ${amountSection}
                                         </div>

                                         <div class="col-lg-4 col-md-6 mb-3">
                                             <label class="form-label small text-muted font-weight-bold">Period</label>
                                             <div class="row align-items-center">
                                                 <div class="col-5">
                                                     <select name="ClaimEntitlementDetails[${newKey}][month_start]" class="form-control form-control-sm">
                                                         ${monthStartOptions}
                                                     </select>
                                                 </div>
                                                 <div class="col-2 text-center">
                                                     <i class="fas fa-arrow-right text-muted"></i>
                                                 </div>
                                                 <div class="col-5">
                                                     <select name="ClaimEntitlementDetails[${newKey}][month_end]" class="form-control form-control-sm">
                                                         ${monthEndOptions}
                                                     </select>
                                                 </div>
                                             </div>
                                         </div>

                                         <div class="col-lg-5 col-md-12 mb-3">
                                             <label class="form-label small text-muted font-weight-bold">Remark</label>
                                             <textarea name="ClaimEntitlementDetails[${newKey}][remark]" 
                                                      class="form-control" rows="2" 
                                                      placeholder="Optional remarks..."></textarea>
                                         </div>
                                        
                                         <div class="col-lg-12 col-md-12 mb-3">
                                             <div class="form-check">
                                                 <input type="checkbox" class="form-check-input no-limit-checkbox" id="noLimit_${newKey}" name="ClaimEntitlementDetails[${newKey}][no_limit_check]">
                                                 <label class="form-check-label small text-success font-weight-bold" for="noLimit_${newKey}">
                                                     No Limit
                                                 </label>
                                             </div>
                                         </div>
                                
                                     </div>
                                 </div>
                             </div>
                         `;

            $('#claimTypesContainer').append(cardHtml);
        }
    });
</script>