<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

$locationList = frontend\models\RefTravelLocation::find()->orderBy(['order' => SORT_ASC])->all();
$locationDropdownData = ArrayHelper::map($locationList, 'code', 'name');
$user = common\models\User::findOne(Yii::$app->user->identity->id);
?>
<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0">Work Traveling Requisition Details</h6>
    </div>
    <div class="card-body p-2 table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>WTR Code</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Reason</th>
                    <th>Total Days</th>
                    <th>Location</th>
                    <th>Allowance Per Day (RM)</th>
                    <th>Total Allowance (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" width="10%"><?= $leaveRecord->leave_code ?></td>
                    <td class="text-center" width="10%"><?= Html::encode(date('d/m/Y', strtotime($leaveRecord->start_date))) ?></td>
                    <td class="text-center" width="10%"><?= Html::encode(date('d/m/Y', strtotime($leaveRecord->end_date))) ?></td>
                    <td width="23%">
                        <span class="text-left"><?= Html::encode($leaveRecord->reason) ?></span>

                        <?php if (isset($leaveRecord->support_doc) && !empty($leaveRecord->support_doc)): ?>
                            <?php
//                            =
//                            Html::a(
//                                    "<i class='far fa-file-alt fa-lg float-right'></i>",
//                                    ["/working/leavemgmt/get-file", 'filename' => urlencode($leaveRecord->support_doc)],
//                                    [
//                                        'title' => "Supporting Document",
//                                        'target' => "_blank",
//                                        'data-pjax' => "0",
//                                    ]
//                            )
                            ?>
                            <?=
                            Html::a(
                                    "<i class='far fa-file-alt fa-lg float-right'></i>",
                                    "#",
                                    [
                                        'title' => "Supporting Document",
                                        'value' => "/working/leavemgmt/get-file?filename=" . urlencode($leaveRecord->support_doc),
                                        'class' => "docModal"
                                    ]
                            );
                            ?>
                            <?=
                            $this->render('/_docModal')
                            ?>  
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><?= Html::encode($leaveRecord->total_days) ?></td>
                    <td>
                        <?php
                        $isReadonly = ($detail->is_paid == 1);
                        ?>

                        <?php if ($isReadonly): ?>
                            <!-- Show the full location name if disabled -->
                            <p class="form-control-plaintext">
                                <?= Html::encode($locationDropdownData[$detail->travel_location_code] ?? 'N/A') ?>
                            </p>
                            <?=
                            Html::activeHiddenInput($model, 'travel_location_code', [
                                'value' => $detail->travel_location_code
                            ])
                            ?>
                        <?php else: ?>
                            <?=
                            Html::activeDropDownList($model, 'travel_location_code', $locationDropdownData, [
                                'id' => 'location-dropdown',
                                'prompt' => 'Select Location',
                                'required' => true,
                                'class' => 'form-control',
                                'value' => $detail->travel_location_code,
                            ])
                            ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <input type="text" 
                               id="allowance-per-day" 
                               class="form-control text-right" 
                               readonly 
                               value=<?= \common\models\myTools\MyFormatter::asDecimal2($detail->receipt_amount) ?>
                               placeholder="Select location">
                    </td>
                    <td>
                        <input type="text" 
                               id="total-allowance" 
                               class="form-control text-right" 
                               readonly 
                               value=<?= \common\models\myTools\MyFormatter::asDecimal2($detail->amount_to_be_paid ?: 0.00) ?>>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="6" class="text-right font-weight-bold">Total Allowance To Be Paid (RM):</th>
                    <td>
                        <?=
                        Html::activeTextInput($model, 'total_allowance_to_be_paid', [
                            'id' => 'total-allowance-to-be-paid',
                            'readonly' => true,
                            'class' => 'form-control text-right',
                            'value' => \common\models\myTools\MyFormatter::asDecimal2($detail->amount_to_be_paid ?: 0.00),
                        ])
                        ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Message container for user feedback -->
        <div id="allowance-message" class="mt-2" style="display: none;">
            <div class="alert" role="alert">
                <span id="allowance-message-text"></span>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var locationData = <?= json_encode(array_keys($locationDropdownData)) ?>; // Use keys (location codes)
        const totalDays = <?= (int) $leaveRecord->total_days ?>;

        $('#location-dropdown').on('change', function () {
            const locationCode = $(this).val();
            var grade = '<?= Html::encode($user->grade) ?>';

            // Reset fields and messages first
            resetFields();
            hideMessage();

            if (locationCode !== '' && $.inArray(locationCode, locationData) !== -1) {
                // Show loading state
                showLoadingState();
                $.ajax({
                    url: '/office/claim/ajax-eh-travel-allowance',
                    type: 'GET',
                    data: {
                        locationCode: locationCode,
                        grade: grade
                    },
                    beforeSend: function () {
                        $('#location-dropdown').prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            const allowancePerDay = parseFloat(response.allowancePerDay);
                            const totalAllowance = allowancePerDay * totalDays;

                            // Update the fields
                            $('#allowance-per-day').val(allowancePerDay.toFixed(2));
                            $('#total-allowance').val(totalAllowance.toFixed(2));
                            $('#total-allowance-to-be-paid').val(totalAllowance.toFixed(2));

                        } else {
                            // Handle API error response
                            handleError(response.message || 'Unable to retrieve allowance data');
                        }
                    },
                    error: function (xhr, status, error) {
                        // Handle AJAX error
                        let errorMessage = 'Network error occurred while retrieving allowance data';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 404) {
                            errorMessage = 'Allowance service not found';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error occurred';
                        }

                        handleError(errorMessage);
                        console.error('AJAX Error:', {
                            status: xhr.status,
                            error: error,
                            response: xhr.responseText
                        });
                    },
                    complete: function () {
                        $('#location-dropdown').prop('disabled', false);
                        $('#allowance-per-day').removeClass('loading');
                    }
                });
            } else {
                // Clear the fields if no valid location selected
                resetFields();
            }
        });

        // Helper functions
        function resetFields() {
            $('#allowance-per-day').val('0.00');
            $('#total-allowance').val('0.00');
            $('#total-allowance-to-be-paid').val('0.00');
            $('#total-allowance, #total-allowance-to-be-paid, #allowance-per-day').removeClass('font-weight-bold text-success text-danger loading');
        }

        function showLoadingState() {
            $('#allowance-per-day').val('Loading...').addClass('loading');
        }

        function handleError(message) {
            $('#allowance-per-day').val('N/A');
            $('#total-allowance, #total-allowance-to-be-paid, #allowance-per-day').addClass('text-danger');
            showMessage(message, 'danger');
        }

        function showMessage(text, type) {
            $('#allowance-message-text').text(text);
            $('#allowance-message .alert').removeClass('alert-success alert-danger alert-info alert-warning').addClass('alert-' + type);
            $('#allowance-message').slideDown();

            // Auto-hide success messages after 3 seconds
            if (type === 'success') {
                setTimeout(function () {
                    hideMessage();
                }, 3000);
            }
        }

        function hideMessage() {
            $('#allowance-message').slideUp();
        }

        // Optional: Trigger calculation on page load if location is pre-selected
        $('#location-dropdown').trigger('change');
    });
</script>

<style>

    #location-dropdown, #allowance-per-day, #total-allowance, #total-allowance-to-be-paid {
        min-width: 120px;
    }

    .loading {
        position: relative;
        color: transparent !important;
    }

    .loading::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 16px;
        height: 16px;
        margin: -8px 0 0 -8px;
        border: 2px solid #f3f3f3;
        border-radius: 50%;
        border-top: 2px solid #3498db;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    #allowance-message {
        transition: all 0.3s ease;
    }
</style>