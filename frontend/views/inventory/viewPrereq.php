<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\RefGeneralStatus;

$moduleIndex = 'inventory';

if ($module === 'execPendingPurchasing') {
    $pageName = 'Purchasing - Executive';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingInventory'];
} else if ($module === 'execAllPurchasing') {
    $pageName = 'Purchasing - Executive';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventory'];
}

else if ($module === 'assistPendingPurchasing') {
    $pageName = 'Purchasing - Assistant';
    $pageName2 = 'Pending Requisition Approval';
        $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingInventory'];

} else if ($module === 'assistAllPurchasing') {
    $pageName = 'Purchasing - Assistant';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventory'];
} 

else if ($module === 'projcoorPendingApproval') {
    $pageName = 'Purchasing - Project Coordinator';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingApprovalInventoryProjcoor'];
} else if ($module === 'projcoorReadyForProcurement') {
    $pageName = 'Purchasing - Project Coordinator';
    $pageName2 = 'Ready for Procurement';
        $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingProcurementInventoryProjcoor'];
} else if ($module === 'projcoorAllApproval') {
    $pageName = 'Purchasing - Project Coordinator';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventoryProjcoor'];
} 

else if ($module === 'maintenanceHeadPendingApproval') {
    $pageName = 'Purchasing - Head of Maintenance';
    $pageName2 = 'Pending Requisition Approval';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingApprovalInventoryMaintenanceHead'];
} else if ($module === 'maintenanceHeadReadyForProcurement') {
    $pageName = 'Purchasing - Head of Maintenance';
    $pageName2 = 'Ready for Procurement';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'pendingProcurementInventoryMaintenanceHead'];
} else if ($module === 'maintenanceHeadAllApproval') {
    $pageName = 'Purchasing - Head of Maintenance';
    $pageName2 = 'All Pre-Requisitions';
    $url = ['pre-requisition-list', 'type' => $module, 'context' => 'allInventoryMaintenanceHead'];
}

$this->title = $master->prf_no;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => $pageName2, 'url' => $url];
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->params['breadcrumbs'][] = ['label' => "View"];
\yii\web\YiiAsset::register($this);
?>
<style>
    .deleted-row {
        text-decoration: line-through;
        opacity: 0.6;
        color: #6c757d;
    }
    .total-amount-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .table-sm.font-weight-bold {
        font-size: 14px;
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
                <?php if ($moduleIndex === 'inventory'): ?>
                    <?= Html::a('Update', ['update-pre-requisition', 'id' => $master->id, 'moduleIndex' => $module], ['class' => 'btn btn-primary']) ?>
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

    <div class="table-responsive">
        <table class="table table-bordered mb-0" id="item_table">
            <thead class="table-dark">
                <tr>
                    <th rowspan="2" class="text-center" width="4%">No.</th>
                    <th rowspan="2" width="8%">Department</th>
                    <th rowspan="2" width="8%">Supplier</th>
                    <th rowspan="2" width="8%">Brand</th>
                    <th rowspan="2" width="8%">Model</th>
                    <th rowspan="2" width="18%">Item Description</th>
                    <th rowspan="2" width="6%">Quantity</th>
                    <th rowspan="2" width="6%">Unit Type</th>
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
                    <th class="text-right" width="8%">Total Price</th>
                    <th class="text-left" width="8%">Remark</th>
                </tr>
            </thead>

            <tbody id="listTBody">
                <?php foreach ($items as $i => $item): ?>
                    <?php
                    $isDeleted = ($item->is_deleted == 1);
                    $worklist = $worklists[$item->id] ?? null;
                    ?>
                    <tr <?= $isDeleted ? 'class="deleted-row"' : '' ?>>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= $departmentList[$item->department_code] ?? '-' ?></td>
                        <td><?= Html::encode($item->supplier_name ?? '-') ?></td>
                        <td><?= Html::encode($item->brand_name ?? '-') ?></td>
                        <td><?= Html::encode($item->model_name ?? '-') ?></td>
                        <td><?= Html::encode($item->item_description ?? '-') ?></td>
                        <td class="text-center"><?= Html::encode($item->quantity ?? '-') ?></td>
                        <td class="text-center"><?= Html::encode($item->model_unit_type ?? '-') ?></td>
                        <td class="text-center"><?= Html::encode($item->currency ?? '-') ?></td>
                        <td class="text-right"><?= number_format($item->unit_price ?? 0, 2) ?></td>
                        <td class="text-right"><?= number_format($item->total_price ?? 0, 2) ?></td>
                        <td><?= Html::encode($item->purpose_or_function ?? '-') ?></td>

                        <?php if ($item->quantity_approved !== null): ?>
                            <td class="text-center"><?= $item->quantity_approved ?></td>
                            <td class="text-center"><?= $item->currency_approved ?></td>
                            <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($item->unit_price_approved) ?></td>
                            <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($item->total_price_approved) ?></td>
                            <td>
                                <div class="decision-result">
                                    <?php if ($worklist->status == \frontend\models\RefGeneralStatus::STATUS_Approved): ?>
                                        <span class="text-success">Approved</span><br>
                                        <?php
                                        $responder = User::findOne($worklist->responded_by);
                                        if ($responder):
                                            ?>
                                            by <?= Html::encode($responder->fullname) ?>
                                        <?php endif; ?>
                                        @ <?= MyFormatter::asDateTime_ReaddmYHi($worklist->created_at) ?>

                                    <?php elseif ($worklist->status == RefGeneralStatus::STATUS_SuperiorRejected): ?>
                                        <!--Rejected Display--> 
                                        <span class="text-danger">Rejected</span><br>
                                        <?php
                                        $responder = User::findOne($worklist->responded_by);
                                        if ($responder):
                                            ?>
                                            by <?= Html::encode($responder->fullname) ?>
                                            @ <?= MyFormatter::asDateTime_ReaddmYHi($worklist->created_at) ?>
                                            <br>
                                            <small class="text-danger">
                                                <strong>Reject Reason:</strong><br>
                                                <?= Html::encode($worklist->remark) ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php else: ?>
                            <!-- No superior response yet -->
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-right">-</td>
                            <td class="text-right">-</td>
                            <td class="text-center text-muted">Pending</td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <!-- Total amount rows will be inserted here by JavaScript -->
            </tfoot>
        </table>
    </div>
</div>

<script>
    (function () {
        'use strict';

        // Configuration
        const config = {
            masterId: "<?= $master->id ?>",
            hasSuperiorUpdate: <?= $hasSuperiorUpdate ? 'true' : 'false' ?>,
            STATUS_APPROVED: "<?= \frontend\models\RefGeneralStatus::STATUS_Approved ?>",
            STATUS_REJECTED: "<?= \frontend\models\RefGeneralStatus::STATUS_SuperiorRejected ?>",
            csrfToken: $('meta[name="csrf-token"]').attr('content')
        };

        // Setup CSRF for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-Token': config.csrfToken
            }
        });

        // Initialize when DOM is ready
        document.addEventListener("DOMContentLoaded", function () {
            buildCurrencyTotals();
        });

        // ✅ Get SUBMITTED currency (left side - from requestor) - column 8 (0-indexed)
        function getSubmittedCurrencyFromRow(row) {
            return (row.cells[8]?.textContent || "").trim();
        }

        // ✅ Get SUBMITTED total price (left side - from requestor) - column 10
        function getTotalPriceFromRow(row) {
            const totalPriceText = row.cells[10]?.textContent || "0";
            return parseFloat(totalPriceText.replace(/,/g, '')) || 0;
        }

        function getApprovedCurrencyFromRow(row) {
            const currencyText = (row.cells[13]?.textContent || "").trim();
            // Return null if it's a dash (no data)
            return (currencyText === '-' || currencyText === '') ? null : currencyText;
        }

        function getApprovedTotalPriceFromRow(row) {
            const totalPriceText = row.cells[15]?.textContent || "0";
            // Return 0 if it's a dash or empty
            if (totalPriceText === '-' || totalPriceText === '') {
                return 0;
            }
            return parseFloat(totalPriceText.replace(/,/g, '')) || 0;
        }

        // ✅ Get row status from remark column - column 16
        function getRowStatus(row) {
            const statusCell = row.cells[16];
            if (statusCell) {
                const statusText = statusCell.textContent.trim().toLowerCase();

                // Check for approved status
                if (statusText === 'approved' || statusText.includes('approved')) {
                    return "approved";
                }

                // Check for rejected status
                if (statusText.startsWith('rejected') || statusText.includes('reject')) {
                    return "rejected";
                }

                // Check for pending
                if (statusText === 'pending' || statusText === '-') {
                    return "pending";
                }
            }

            return "pending";
        }

        // ✅ Calculate totals for a specific currency
        function updateCurrencyTotal(currency) {
            let submittedTotal = 0;
            let approvedTotal = 0;

            document.querySelectorAll('#listTBody tr').forEach(row => {
                // For SUBMITTED total (left side)
                const submittedCurrency = getSubmittedCurrencyFromRow(row);
                if (submittedCurrency === currency) {
                    const submittedPrice = getTotalPriceFromRow(row);
                    submittedTotal += submittedPrice;
                }

                // For APPROVED total (right side)
                const approvedCurrency = getApprovedCurrencyFromRow(row);
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

            const formatted = value.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            el.textContent = formatted;
        }

        // ✅ Build totals footer per currency
        function buildCurrencyTotals() {
            const currencies = new Set();

            // Collect currencies from BOTH submitted and approved columns
            document.querySelectorAll("#listTBody tr").forEach(row => {
                // Add submitted currency
                const submittedCurrency = getSubmittedCurrencyFromRow(row);
                if (submittedCurrency && submittedCurrency !== '-') {
                    currencies.add(submittedCurrency);
                }

                // Add approved currency
                const approvedCurrency = getApprovedCurrencyFromRow(row);
                if (approvedCurrency && approvedCurrency !== '-') {
                    currencies.add(approvedCurrency);
                }
            });

            // Remove existing total rows
            document.querySelectorAll('tr[id^="total_amount_"]').forEach(row => row.remove());

            let tfoot = document.querySelector("#item_table tfoot");
            if (!tfoot) {
                tfoot = document.createElement("tfoot");
                document.querySelector("#item_table").appendChild(tfoot);
            }

            currencies.forEach(currency => {
                const tr = document.createElement("tr");
                tr.id = "total_amount_" + currency;
                tr.className = "table-sm font-weight-bold total-amount-row";

                const tdLabel = document.createElement("td");
                tdLabel.colSpan = 10;
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

            // Add a message if no currencies found
            if (currencies.size === 0) {
                const tr = document.createElement("tr");
                tr.className = "table-sm text-muted";

                const td = document.createElement("td");
                td.colSpan = 16;
                td.className = "text-center";
                td.style.padding = "10px";
                td.textContent = "No items available";
                tr.appendChild(td);

                tfoot.prepend(tr);
            }
        }

    })();
</script>