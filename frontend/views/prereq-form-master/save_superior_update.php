<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\VPrereqFormMasterDetail */
/* @var $form yii\widgets\ActiveForm */

$this->title = $master->prf_no;
$this->params['breadcrumbs'][] = ['label' => 'Pre-Requisition Form - Superior', 'url' => [$moduleIndex . '-pending-approval']];
$this->params['breadcrumbs'][] = ['label' => $master->prf_no];
?>
<meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
<style>
    .date-of-material-required-row{
        display: block;
        margin-left: 80%;
    }
</style>

<div class="prereq-form-master-view mb-5">
    <div class="row mb-1">
        <div class="col-md-8">
            <h5><?= Html::encode($this->title) ?></h5>
            <p class="text-muted">
                Submitted by <?= Html::encode($master->createdBy->fullname) ?> 
                on <?= MyFormatter::asDateTime_ReaddmYHi($master->created_at) ?>
            </p>
        </div>
        <div class="col-md-4 text-right">
            <?php if ($master->status === frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval): ?>
                <?php if ($moduleIndex === 'personal'): ?>
                    <?= Html::a('Update', ['update', 'id' => $master->id, 'moduleIndex' => $moduleIndex], ['class' => 'btn btn-primary']) ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="alert alert-info border-left-primary mb-4">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <strong class="text-muted">Date of Material Required:</strong>
                        <span class="ml-2 text-dark font-weight-bold"><?= MyFormatter::asDate_Read($master->date_of_material_required) ?></span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Superior:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?= Html::encode($master->superior->fullname) ?>
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Status:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?= Html::encode($master->status0->status_name) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-bordered mb-0" id="item_table">
        <thead class="table-dark">
            <tr>
                <th rowspan="2" class="text-center" width="4%">No.</th>
                <th rowspan="2" width="8%">Department</th>
                <th rowspan="2" width="8%">Supplier</th>
                <th rowspan="2" width="8%">Brand</th>
                <th rowspan="2" width="8%">Model Type</th>
                <th rowspan="2" width="8%">Model Group</th>
                <th rowspan="2" width="18%">Item Description</th>
                <th rowspan="2" width="6%">Quantity</th>
                <th rowspan="2" class="text-center" width="3%">Currency</th>
                <th rowspan="2" class="text-right" width="9%">Unit Price</th>
                <th rowspan="2" class="text-right" width="9%">Total Price</th>
                <th rowspan="2" width="16%">Purpose</th>
                <th colspan="5" class="text-center" width="35%">Superior's Response</th>
            </tr>
            <tr>
                <th width="5%">Quantity</th>
                <th class="text-center" width="6%">Currency</th>
                <th class="text-right" width="8%">Unit Price</th>
                <th class="text-right" width="9%">Total Price</th>
                <th class="text-left" width="7%">Remark</th>
            </tr>
        </thead>

        <tbody id="listTBody">
            <?php
            if (!is_array($vmodel)) {
                $vmodel = [$vmodel];
            }
            // solves the issue of repeating vmodels
            $vmodelMap = [];
            foreach ($vmodel as $v) {
                $vmodelMap[$v->item_id] = $v;
            }
            ?>
            <?php foreach ($items as $i => $item): ?>
                <?=
                $this->render('_superior_form_row', [
                    'key' => $i,
                    'form' => $form,
                    'model' => $vmodelMap[$item->id] ?? $item,
                    'master' => $master,
                    'isUpdate' => $isUpdate,
                    'isView' => $isView,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist()
                ])
                ?>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <?php if (!$hasSuperiorUpdate): ?>
                <tr>
                    <td colspan="17">      
                        <?=
                        Html::submitButton('Save', [
                            'id' => 'save-btn',
                            'class' => 'float-right btn btn-success'
                        ])
                        ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tfoot>
    </table>

    <?php ActiveForm::end(); ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
$firstVModel = is_array($vmodel) ? reset($vmodel) : $vmodel;
?>
<script>
    (function () {
        'use strict';
        console.log("k");
        console.log(<?= $hasSuperiorUpdate ?>);
        const hasSuperiorUpdate = <?= $hasSuperiorUpdate == 1 ? 'false' : 'true' ?>; // dynamically set based on PHP variable
        const masterId = "<?= $firstVModel && isset($firstVModel->master_id) ? $firstVModel->master_id : '' ?>";
        const STATUS_APPROVED = "<?= \frontend\models\RefGeneralStatus::STATUS_Approved ?>";
        const STATUS_REJECTED = "<?= \frontend\models\RefGeneralStatus::STATUS_SuperiorRejected ?>";

        // Setup CSRF for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            if (hasSuperiorUpdate) {
                initializeTable();
                attachApprovalListeners();
                attachApprovedFieldListeners();
            }
            buildCurrencyTotals();
        });

        function initializeTable() {
            // Calculate approved row totals on init (editable mode only)
            $("#listTBody tr").each(function () {
                updateApprovedRowTotal(this);
            });

            buildCurrencyTotals();
        }

        // ✅ This updates APPROVED total (right side) - only if input fields exist
        function updateApprovedRowTotal(row) {
            const $row = $(row);
            const qtyApprovedInput = $row.find('input[name*="quantity_approved"]');
            const unitApprovedInput = $row.find('input[name*="unit_price_approved"]');
            const totalApprovedInput = $row.find('input[name*="total_price_approved"]');

            // Only update if input fields exist (editable mode)
            if (qtyApprovedInput.length && unitApprovedInput.length && totalApprovedInput.length) {
                const qtyApproved = parseFloat(qtyApprovedInput.val()) || 0;
                const unitApproved = parseFloat(unitApprovedInput.val()) || 0;
                const totalApproved = (qtyApproved * unitApproved).toFixed(2);
                totalApprovedInput.val(totalApproved);
                return parseFloat(totalApproved);
            }

            return 0;
        }

function getCurrencyFromRow(row) {
    if (hasSuperiorUpdate) {
        // Try to get from dropdown first (editable mode)
        const dropdown = row.querySelector('select[name*="[currency_approved]"]');
        if (dropdown)
            return dropdown.value.trim();

        // Fallback to text in column 13 (approved currency column) - UPDATED from 12
        return (row.cells[13]?.textContent || "").trim();
    } else {
        // Read-only mode: get from column 13 - UPDATED from 12
        return (row.cells[13]?.textContent || "").trim();
    }
}

        // ✅ Get SUBMITTED total price (left side - from requestor)
function getTotalPriceFromRow(row) {
    const totalPriceText = row.cells[10]?.textContent || "0";  // UPDATED from 9
    return parseFloat(totalPriceText.replace(/,/g, '')) || 0;
}

        // ✅ Get SUBMITTED currency (left side - from requestor)
function getSubmittedCurrencyFromRow(row) {
    return (row.cells[8]?.textContent || "").trim();  // This stays the same
}

        // ✅ Get APPROVED total price (right side - superior's response)
function getApprovedTotalPriceFromRow(row) {
    // Try to get from input field first (editable mode)
    const input = row.querySelector('input[name*="total_price_approved"]');
    if (input) {
        return parseFloat(input.value || 0);
    }

    // Fallback to text in column 15 for read-only mode - UPDATED from 14
    const totalPriceText = row.cells[15]?.textContent || "0";
    return parseFloat(totalPriceText.replace(/,/g, '')) || 0;
}

        function getRowStatus(row) {
    if (hasSuperiorUpdate) {
        // Editable mode: check radio buttons
        const approveRadio = row.querySelector(`input[type="radio"][value="${STATUS_APPROVED}"]:checked`);
        const rejectRadio = row.querySelector(`input[type="radio"][value="${STATUS_REJECTED}"]:checked`);

        if (approveRadio)
            return "approved";
        if (rejectRadio)
            return "rejected";

        return "pending";
    } else {
        // Read-only mode: check status text in column 16 - UPDATED from 15
        const statusCell = row.cells[16];
        if (statusCell) {
            const statusText = statusCell.textContent.trim().toLowerCase();
            console.log('Status text:', statusText);
            if (statusText.includes('approved'))
                return "approved";
            if (statusText.includes('reject'))
                return "rejected";
        }

        return "pending";
    }
}

        // ✅ Calculate totals: left side = submitted (from HTML text), right side = approved only
        function updateCurrencyTotal(currency) {
            let submittedTotal = 0;
            let approvedTotal = 0;

            document.querySelectorAll('#listTBody tr').forEach(row => {
                // For SUBMITTED total (left side): use the currency from column 4
                const submittedCurrency = getSubmittedCurrencyFromRow(row);
                if (submittedCurrency === currency) {
                    const submittedPrice = getTotalPriceFromRow(row);
                    submittedTotal += submittedPrice;
                }

                // For APPROVED total (right side): use the currency from dropdown or column 8
                const approvedCurrency = getCurrencyFromRow(row);
                if (approvedCurrency === currency) {
                    const status = getRowStatus(row);
                    if (status === "approved") {
                        const approvedPrice = getApprovedTotalPriceFromRow(row);
                        approvedTotal += approvedPrice;
                    }
                }
            });

            updateTotalDisplay('totalAmountSubmitted-' + currency, submittedTotal);
            updateTotalDisplay('totalAmountApproved-' + currency, approvedTotal);
        }

        function updateTotalDisplay(elementId, value) {
            const el = document.getElementById(elementId);
            if (!el)
                return;
            const formatted = value.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            el.textContent = formatted;
        }

        // ✅ Attach listeners to APPROVED fields ONLY (right side) - only in editable mode
        function attachApprovedFieldListeners() {
            if (!hasSuperiorUpdate)
                return; // Skip if read-only mode

            document.querySelectorAll('#listTBody tr').forEach(row => {
                const qtyApprovedInput = row.querySelector('input[name*="quantity_approved"]');
                const unitApprovedInput = row.querySelector('input[name*="unit_price_approved"]');
                const currencyApprovedSelect = row.querySelector('select[name*="[currency_approved]"]');

                // Update approved total when quantity_approved changes
                if (qtyApprovedInput) {
                    qtyApprovedInput.addEventListener('input', function () {
                        updateApprovedRowTotal(row);
                        const currency = getCurrencyFromRow(row);
                        if (currency)
                            updateCurrencyTotal(currency);
                    });
                }

                // Update approved total when unit_price_approved changes
                if (unitApprovedInput) {
                    unitApprovedInput.addEventListener('input', function () {
                        updateApprovedRowTotal(row);
                        const currency = getCurrencyFromRow(row);
                        if (currency)
                            updateCurrencyTotal(currency);
                    });
                }

                // Update totals when currency_approved changes
                if (currencyApprovedSelect) {
                    currencyApprovedSelect.addEventListener('change', function () {
                        buildCurrencyTotals();
                    });
                }
            });
        }

        // ✅ Attach approve/reject radio logic - only in editable mode
        function attachApprovalListeners() {
            if (!hasSuperiorUpdate)
                return; // Skip if read-only mode

            document.querySelectorAll('#listTBody tr').forEach(row => {
                const approveRadio = row.querySelector(`input[type="radio"][value="${STATUS_APPROVED}"]`);
                const rejectRadio = row.querySelector(`input[type="radio"][value="${STATUS_REJECTED}"]`);
                const remark = row.querySelector('.reject-remark');
                const approveLabel = row.querySelector('.approve-btn label');
                const rejectLabel = row.querySelector('.reject-btn label');

                if (!approveRadio || !rejectRadio)
                    return;

                approveRadio.addEventListener('change', function () {
                    if (this.checked) {
                        if (remark) {
                            remark.style.display = 'none';
                            remark.required = false;
                        }

                        approveLabel?.classList.add('btn-success', 'text-white', 'fw-bold');
                        approveLabel?.classList.remove('btn-outline-success');
                        rejectLabel?.classList.remove('btn-danger', 'text-white', 'fw-bold');
                        rejectLabel?.classList.add('btn-outline-danger');

                        const currency = getCurrencyFromRow(row);
                        updateApprovedRowTotal(row);
                        if (currency)
                            updateCurrencyTotal(currency);
                    }
                });

                rejectRadio.addEventListener('change', function () {
                    if (this.checked) {
                        if (remark) {
                            remark.style.display = 'block';
                            remark.required = true;
                        }

                        rejectLabel?.classList.add('btn-danger', 'text-white', 'fw-bold');
                        rejectLabel?.classList.remove('btn-outline-danger');
                        approveLabel?.classList.remove('btn-success', 'text-white', 'fw-bold');
                        approveLabel?.classList.add('btn-outline-success');

                        const currency = getCurrencyFromRow(row);
                        if (currency)
                            updateCurrencyTotal(currency);
                    }
                });
            });
        }

        // ✅ Build totals footer per currency
        function buildCurrencyTotals() {
    const currencies = new Set();

    // Collect currencies from BOTH submitted and approved columns
    document.querySelectorAll("#listTBody tr").forEach(row => {
        // Add submitted currency
        const submittedCurrency = getSubmittedCurrencyFromRow(row);
        if (submittedCurrency)
            currencies.add(submittedCurrency);

        // Add approved currency
        const approvedCurrency = getCurrencyFromRow(row);
        if (approvedCurrency)
            currencies.add(approvedCurrency);
    });

    document.querySelectorAll('tr[id^="total_amount_"]').forEach(row => row.remove());

    let tfoot = document.querySelector("#item_table tfoot");
    if (!tfoot) {
        tfoot = document.createElement("tfoot");
        document.querySelector("#item_table").appendChild(tfoot);
    }

    currencies.forEach(currency => {
        const tr = document.createElement("tr");
        tr.id = "total_amount_" + currency;
        tr.className = "table-sm font-weight-bold";

        const tdLabel = document.createElement("td");
        tdLabel.colSpan = 10;  // UPDATED from 9 (includes model_group column)
        tdLabel.className = "text-right";
        tdLabel.style.padding = "10px";
        tdLabel.textContent = `Total Amount (${currency}):`;
        tr.appendChild(tdLabel);

        const tdSubmitted = document.createElement("td");
        tdSubmitted.style.padding = "10px";
        tdSubmitted.colSpan = 2;
        tdSubmitted.innerHTML = `<span class="text-muted">Requested:</span> <span id="totalAmountSubmitted-${currency}" class="fw-bold">0.00</span>`;
        tr.appendChild(tdSubmitted);

        const tdApproved = document.createElement("td");
        tdApproved.style.padding = "10px";
        tdApproved.colSpan = 5;
        tdApproved.innerHTML = `<span class="text-muted">Approved:</span> <span id="totalAmountApproved-${currency}" class="fw-bold text-success">0.00</span>`;
        tr.appendChild(tdApproved);

        tfoot.prepend(tr);

        updateCurrencyTotal(currency);
    });
}

    })();
</script>


