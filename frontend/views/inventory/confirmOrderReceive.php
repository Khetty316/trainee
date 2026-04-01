<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

if ($moduleIndex === 'execPendingReceiving') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'execAllReceiving') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'assistPendingReceiving') {
    $pageName = 'Receiving - Assistant';
} else if ($moduleIndex === 'assistAllReceiving') {
    $pageName = 'Receiving - Assistant';
}else if ($moduleIndex === 'maintenanceHeadPendingReceiving') {
    $pageName = 'Receiving - Head of Maintenance';
} else if ($moduleIndex === 'maintenanceHeadAllReceiving') {
    $pageName = 'Receiving - Head of Maintenance';
}
$url = 'po?type=' . $moduleIndex;
$url2 = 'update-receive-items?id=' . $po->id . '&moduleIndex=' . $moduleIndex;

$this->title = 'Confirm Order Receive';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName, 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => 'Updates Order Receive', 'url' => [$url2]];
$this->params['breadcrumbs'][] = ['label' => $po->po_no];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mb-5">
    <?php
    $form = ActiveForm::begin([
        'id' => 'myForm',
        'method' => 'post',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
        'options' => [
            'autocomplete' => 'off',
            'enctype' => 'multipart/form-data'
        ],
    ]);
    ?>

    <div class="row">
        <div class="col-md-8">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
    </div>

    <div class="alert alert-info border-left-primary mb-3">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <strong class="text-muted">PO No.:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= Html::encode($po->po_no) ?>
                        </span>
                    </div>
                    <div class="col-md-2">
                        <strong class="text-muted">PO Date:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= MyFormatter::asDate_Read($po->po_date) ?>
                        </span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Company Group:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?php $companyGroup = frontend\models\common\RefCompanyGroupList::findOne($po->company_group); ?>
                            <?= Html::encode($companyGroup->company_name) ?>
                        </span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Status:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= $po->status0->name ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Received Item</legend>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Item No.</th>
                                <th>Description</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-right">Discount</th>
                                <th class="text-right">Amount</th>
                                <th class="text-center">Order Qty</th>
                                <th class="text-center">Received Qty</th>
                                <th class="text-center">Remaining Qty</th>
                                <th class="text-center">Receive Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($postData)):
                                foreach ($postData as $index => $item):
                                    $detail = frontend\models\inventory\InventoryPurchaseOrderItem::findOne($item['id']);

                                    $allocations = \frontend\models\inventory\InventoryOrderRequestAllocation::find()
                                            ->where(['inventory_po_item_id' => $item['id']])
                                            ->andWhere(new \yii\db\Expression('order_qty > IFNULL(received_qty,0)'))
                                            ->all();

                                    $receiveQty = $item['new_receive_qty'] ?? 0;
                                    $hasAllocation = !empty($allocations);
                                    ?>

                                    <!-- MAIN ITEM ROW -->
                                    <tr class="align-middle">
                                <input type="hidden" name="receive[<?= $index ?>][id]" value="<?= $item['id'] ?>" />
                                <input type="hidden" name="receive[<?= $index ?>][new_receive_qty]" value="<?= $receiveQty ?>" />

                                <?php if (!$hasAllocation): ?>
                                    <!-- Only render for non-allocation items. Allocation items use the tfoot input below. -->
                                    <input type="hidden" name="receive[<?= $index ?>][add_to_stock]" value="<?= $receiveQty ?>" />
                                <?php endif; ?>

                                <td class="text-center font-weight-bold"><?= $index + 1 ?></td>
                                <td>
                                    <?= Html::encode($detail->inventoryDetail->code) ?>
                                    <br>
                                    <?php if ($hasAllocation): ?>
                                        <span class="badge badge-info">Has Request</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">No Request</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= Html::encode($detail->brand->name) ?>,
                                    <?= Html::encode($detail->model_description) ?><br>
                                    <small class="text-muted">
                                        MODEL: <?= Html::encode($detail->model_type) ?>
                                    </small>
                                </td>

                                <td class="text-right"><?= number_format($detail->unit_price, 2) ?></td>
                                <td class="text-right"><?= number_format($detail->discount_amt, 2) ?></td>
                                <td class="text-right"><?= number_format($detail->total_price, 2) ?></td>
                                <td class="text-center"><?= $detail->order_qty ?? 0 ?></td>
                                <td class="text-center"><?= $detail->received_qty ?? 0 ?></td>
                                <td class="text-center"><?= $detail->remaining_qty ?? 0 ?></td>

                                <!-- Highlighted Receive Qty -->
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="font-weight-bold text-success">
                                            <?= $receiveQty ?>
                                        </span>

                                        <?php if ($hasAllocation): ?>
                                            <button class="btn btn-sm btn-outline-primary mt-1"
                                                    type="button"
                                                    data-toggle="collapse"
                                                    data-target="#allocation-<?= $index ?>">
                                                <i class="fa fa-tasks"></i> Allocate
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                </tr>

                                <!-- COLLAPSIBLE ALLOCATION SECTION -->
                                <?php if ($receiveQty > 0): ?>
                                    <tr>
                                        <td colspan="10" class="p-0">
                                            <div class="border-left border-right p-1">

                                                <?php if ($hasAllocation): ?>

                                                    <div class="collapse" id="allocation-<?= $index ?>">

                                                        <div data-receive-qty="<?= $receiveQty ?>" data-index="<?= $index ?>">

                                                            <table class="table table-sm table-bordered">
                                                                <thead class="thead-light">
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Reference Type</th>
                                                                        <th>Reference ID</th>
                                                                        <th>Requested By</th>
                                                                        <th class="text-center">Order</th>
                                                                        <th class="text-center">Received</th>
                                                                        <th class="text-center">Allocate</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                    foreach ($allocations as $rIndex => $allocation):
                                                                        $referenceId = '-';
                                                                        if ($allocation->inventoryOrderRequest->reference_type === 'bom_detail') {
                                                                            $ref = frontend\models\bom\BomDetails::findOne($allocation->inventoryOrderRequest->reference_id);
                                                                            $referenceId = $ref->bomMaster->productionPanel->project_production_panel_code ?? '-';
                                                                            $referenceType = 'Project - Bill of Material';
                                                                        } elseif ($allocation->inventoryOrderRequest->reference_type === 'bomstockoutbound') {
                                                                            $ref = frontend\models\bom\StockOutboundDetails::findOne($allocation->inventoryOrderRequest->reference_id);
                                                                            $referenceId = $ref->bomDetail->bomMaster->productionPanel->project_production_panel_code ?? '-';
                                                                            $referenceType = 'Project - Bill of Material (Stockoutbound)';
                                                                        } else if ($allocation->inventoryOrderRequest->reference_type === 'reserve') {
                                                                            $id = common\models\User::findOne($allocation->inventoryOrderRequest->reference_id);
                                                                            $referenceId = $id->fullname ?? '-';
                                                                            $referenceType = 'Reservation';
                                                                        } else if ($allocation->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                                                                            $referenceType = 'Corrective Maintenance';
                                                                            $referenceId = $allocation->inventoryOrderRequest->reference_id;
                                                                        } else if ($allocation->inventoryOrderRequest->reference_type === frontend\models\cmms\CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                                                                            $referenceType = 'Preventive Maintenance';
                                                                            $referenceId = $allocation->inventoryOrderRequest->reference_id;
                                                                        }
                                                                        ?>

                                                                        <tr>
                                                                            <td><?= $rIndex + 1 ?></td>
                                                                            <td><?= Html::encode($referenceType) ?></td>
                                                                            <td><?= Html::encode($referenceId) ?></td>
                                                                            <td><?= ($allocation->inventoryOrderRequest->requestedBy->fullname) . " @ " . common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($allocation->inventoryOrderRequest->requested_at) ?></td>
                                                                            <td class="text-center"><?= $allocation->order_qty ?? 0 ?></td>
                                                                            <td class="text-center"><?= $allocation->received_qty ?? 0 ?></td>

                                                                            <td class="text-center">
                                                                                <input type="number"
                                                                                       name="receive[<?= $index ?>][allocation][<?= $rIndex ?>][qty]"
                                                                                       class="form-control text-center allocation-input"
                                                                                       data-index="<?= $index ?>"
                                                                                       data-max-allocatable="<?= $allocation->order_qty - $allocation->received_qty ?>"
                                                                                       min="0"
                                                                                       max="<?= $allocation->order_qty - $allocation->received_qty ?>"
                                                                                       value="0">

                                                                                <input type="hidden"
                                                                                       name="receive[<?= $index ?>][allocation][<?= $rIndex ?>][id]"
                                                                                       value="<?= $allocation->id ?>">
                                                                            </td>
                                                                        </tr>

                                                                    <?php endforeach; ?>

                                                                </tbody>

                                                                <tfoot>
                                                                    <tr>
                                                                        <td colspan="6" class="text-right font-weight-bold">
                                                                            Balance to Stock:
                                                                        </td>
                                                                        <td class="text-center">
                                                                            <!-- This is the ONLY add_to_stock input for allocation items -->
                                                                            <input type="number"
                                                                                   name="receive[<?= $index ?>][add_to_stock]"
                                                                                   class="form-control text-center add-to-stock"
                                                                                   data-index="<?= $index ?>"
                                                                                   value="<?= $receiveQty ?>"
                                                                                   readonly>
                                                                        </td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>

                                                        </div>

                                                    </div>

                                                <?php else: ?>
                                                    <div class="text-muted">
                                                        No pending request. All received quantity will be added to available stock.
                                                    </div>
                                                <?php endif; ?>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>

                                <?php
                            endforeach;
                        else:
                            ?>
                            <tr>
                                <td colspan="10" class="text-center">No items to receive</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Upload Attachments <span class="text-danger">*</span></legend>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 8%;">No.</th>
                                <th style="width: 30%;">Document Type <span class="text-danger">*</span></th>
                                <th style="width: 27%;">Document No. <span class="text-danger">*</span></th>
                                <th style="width: 27%;">File <span class="text-danger">*</span></th>
                                <th style="width: 8%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="attachment-table-body">
                            <tr class="attachment-row">
                                <td class="text-center">1</td>
                                <td>
                                    <select name="InventoryPurchaseOrderItemDoc[document_type][]" class="form-control form-control-sm">
                                        <option value="">Select Type</option>
                                        <option value="1">Delivery Order</option>
                                        <option value="2">Invoice</option>
                                        <option value="3">Others</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="InventoryPurchaseOrderItemDoc[document_no][]" class="form-control form-control-sm" placeholder="Enter Document No.">
                                </td>
                                <td>
                                    <input type="file" name="InventoryPurchaseOrderItemDoc[filename][]" class="form-control form-control-sm" accept=".pdf, .doc, .docx, .xls, .xlsx, .jpg, .jpeg, .png">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeAttachmentRow(this)" disabled>
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button type="button" class="btn btn-sm btn-primary mb-3" onclick="addAttachmentRow()">
                    Add More <i class="fas fa-plus-circle"></i>
                </button>
            </fieldset>
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 text-right">
            <?php if (!empty($postData)) { ?>
                <?=
                Html::submitButton('Confirm & Save <i class="fas fa-check"></i>', [
                    'class' => 'btn btn-success px-3 mb-3',
                    'id' => 'saveButton',
                ])
                ?>
            <?php } ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>

    /* ============================================================
     ALLOCATION INPUT — Live validation & add-to-stock update
     ============================================================ */

    $(document).on('input', '.allocation-input', function () {

        const $container = $(this).closest('[data-receive-qty]');
        const totalReceive = parseInt($container.data('receive-qty')) || 0;
        const index = $container.data('index');

        // Cap this individual input to its own max-allocatable
        const maxAlloc = parseInt($(this).data('max-allocatable')) || 0;
        if (parseInt($(this).val()) > maxAlloc) {
            $(this).val(maxAlloc);
        }

        // Cap across all inputs so combined total never exceeds totalReceive
        let totalAllocated = 0;
        $container.find('.allocation-input').each(function () {
            totalAllocated += parseInt($(this).val()) || 0;
        });

        if (totalAllocated > totalReceive) {
            const excess = totalAllocated - totalReceive;
            const current = parseInt($(this).val()) || 0;
            $(this).val(Math.max(0, current - excess));

            // Recalculate after capping
            totalAllocated = 0;
            $container.find('.allocation-input').each(function () {
                totalAllocated += parseInt($(this).val()) || 0;
            });
        }

        // Remaining goes to stock
        const addToStock = Math.max(0, totalReceive - totalAllocated);
        $container.find('.add-to-stock[data-index="' + index + '"]').val(addToStock);

        // Clear previous messages
        $container.find('.allocation-error-message').remove();
        $container.find('.allocation-success-message').remove();

        if (hasAllocationRow($container)) {

            const allocationError = getAllocationError($container, totalReceive, totalAllocated);

            if (allocationError) {

                const $errorDiv = $('<div class="allocation-error-message alert alert-danger py-1 mt-2 mb-0">' +
                        '<i class="fa fa-exclamation-circle"></i> ' + allocationError +
                        '</div>');

                $container.append($errorDiv);
                $container.find('.allocation-input').addClass('is-invalid');

            } else {

                $container.find('.allocation-input').removeClass('is-invalid');

                const $successDiv = $('<div class="allocation-success-message alert alert-success py-1 mt-2 mb-0">' +
                        '<i class="fa fa-check-circle"></i> ' +
                        totalAllocated + ' allocated, ' + addToStock + ' will be added to stock.' +
                        '</div>');

                $container.append($successDiv);

                setTimeout(function () {
                    $successDiv.fadeOut(300, function () {
                        $(this).remove();
                    });
                }, 3000);
            }
        }

    });


    /* ============================================================
     HELPER — Core allocation validation logic.
     ─────────────────────────────────────────────────────────────
     
     Rules:
     1. Each row value cannot exceed its own max-allocatable.
     (enforced live above, double-checked here)
     
     2. Total allocated cannot exceed totalReceive.
     
     3. requiredTotal = MIN(totalReceive, totalMaxAllocatable).
     The user MUST allocate this many units before anything
     goes to stock. They are FREE to put the qty on ANY row
     they choose — order does not matter.
     
     Examples (receive=1, Row1 max=1, Row2 max=5):
     requiredTotal = MIN(1, 6) = 1
     → Row1=0, Row2=1  totalAllocated=1 ✅ VALID
     → Row1=1, Row2=0  totalAllocated=1 ✅ VALID
     → Row1=0, Row2=0  totalAllocated=0 ❌ shortfall=1
     
     Examples (receive=6, Row1 max=1, Row2 max=5):
     requiredTotal = MIN(6, 6) = 6
     → Row1=1, Row2=5  totalAllocated=6 ✅ VALID
     → Row1=0, Row2=5  totalAllocated=5 ❌ shortfall=1
     
     ============================================================ */

    function getAllocationError($container, totalReceive, totalAllocated) {

        // Sum of all pending request quantities across all rows
        let totalMaxAllocatable = 0;
        $container.find('.allocation-input').each(function () {
            totalMaxAllocatable += parseInt($(this).data('max-allocatable')) || 0;
        });

        // The minimum total that MUST be allocated before stock gets anything
        const requiredTotal = Math.min(totalReceive, totalMaxAllocatable);

        if (totalAllocated < requiredTotal) {
            const shortfall = requiredTotal - totalAllocated;
            return 'You still need to allocate ' + shortfall + ' more unit(s) to pending requests ' +
                    'before any quantity can go to stock.';
        }

        if (totalAllocated > totalReceive) {
            return 'Allocated quantity (' + totalAllocated + ') exceeds received quantity (' + totalReceive + ').';
        }

        return null; // ✅ Valid
    }


    /* ============================================================
     HELPER — Check if container has allocation rows
     ============================================================ */

    function hasAllocationRow($container) {
        return $container.find('.allocation-input').length > 0;
    }


    /* ============================================================
     AUTO-OPEN COLLAPSES — Open panels that have errors
     ============================================================ */

    function checkAllAllocationsAndOpenCollapses() {

        $('[data-receive-qty]').each(function () {

            const $container = $(this);
            const index = $container.data('index');
            const $collapse = $('#allocation-' + index);

            if (!$collapse.length)
                return;
            if ($container.find('.allocation-input').length === 0)
                return;

            const totalReceive = parseInt($container.data('receive-qty')) || 0;

            let totalAllocated = 0;
            $container.find('.allocation-input').each(function () {
                totalAllocated += parseInt($(this).val()) || 0;
            });

            const error = getAllocationError($container, totalReceive, totalAllocated);

            if (error) {
                if (!$collapse.hasClass('show')) {
                    $collapse.collapse('show');
                }
            }

        });

    }


    /* ============================================================
     DOCUMENT READY — Auto-fill allocations on page load.
     Fills rows in order. Only remainder goes to stock.
     ============================================================ */

    $(document).ready(function () {

        $('[data-receive-qty]').each(function () {

            const $container = $(this);
            const totalReceive = parseInt($container.data('receive-qty')) || 0;
            let remaining = totalReceive;

            $container.find('.allocation-input').each(function () {
                const maxAlloc = parseInt($(this).data('max-allocatable')) || 0;
                const give = Math.min(maxAlloc, remaining);
                $(this).val(give);
                remaining -= give;
            });

            const index = $container.data('index');
            const addToStock = Math.max(0, remaining);
            $container.find('.add-to-stock[data-index="' + index + '"]').val(addToStock);

        });

        setTimeout(checkAllAllocationsAndOpenCollapses, 500);

    });


    /* ============================================================
     ATTACHMENT SECTION — Add / Remove / Renumber rows
     ============================================================ */

    let attachmentCount = 1;

    function addAttachmentRow() {

        attachmentCount++;

        const tbody = document.getElementById('attachment-table-body');
        const newRow = document.createElement('tr');
        newRow.className = 'attachment-row';

        newRow.innerHTML = `
        <td class="text-center">${attachmentCount}</td>
        <td>
            <select name="InventoryPurchaseOrderItemDoc[document_type][]" class="form-control form-control-sm">
                <option value="">Select Type</option>
                <option value="1">Delivery Order</option>
                <option value="2">Invoice</option>
            </select>
        </td>
        <td>
            <input type="text" name="InventoryPurchaseOrderItemDoc[document_no][]"
            class="form-control form-control-sm" placeholder="Enter Document No.">
        </td>
        <td>
            <input type="file" name="InventoryPurchaseOrderItemDoc[filename][]"
            class="form-control form-control-sm"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeAttachmentRow(this)">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    `;

        tbody.appendChild(newRow);
        updateRowNumbers();

    }


    function removeAttachmentRow(button) {

        const tbody = document.getElementById('attachment-table-body');
        const rows = tbody.querySelectorAll('.attachment-row');

        if (rows.length > 1) {
            button.closest('.attachment-row').remove();
            attachmentCount--;
            updateRowNumbers();
        }

    }


    function updateRowNumbers() {

        const tbody = document.getElementById('attachment-table-body');
        const rows = tbody.querySelectorAll('.attachment-row');

        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
            const deleteBtn = row.querySelector('button');
            deleteBtn.disabled = rows.length === 1;
        });

    }


    /* ============================================================
     ALLOCATION VALIDATION — Called before form submission.
     Uses getAllocationError() directly — no CSS class dependency.
     ============================================================ */

    function validateAllAllocations() {

        let hasError = false;
        let errorMessages = [];
        let $firstErrorContainer = null;

        $('[data-receive-qty]').each(function () {

            const $container = $(this);
            const totalReceive = parseInt($container.data('receive-qty')) || 0;
            const $inputs = $container.find('.allocation-input');

            if ($inputs.length === 0)
                return; // No allocation rows — skip

            let totalAllocated = 0;
            $inputs.each(function () {
                totalAllocated += parseInt($(this).val()) || 0;
            });

            // ✅ Fresh direct call — no CSS class dependency
            const error = getAllocationError($container, totalReceive, totalAllocated);

            if (error) {

                hasError = true;
                $inputs.addClass('is-invalid');
                errorMessages.push(error);
                if (!$firstErrorContainer)
                    $firstErrorContainer = $container;

            } else {

                $inputs.removeClass('is-invalid');

            }

        });

        if ($firstErrorContainer) {

            const index = $firstErrorContainer.data('index');
            $('#allocation-' + index).collapse('show');

            setTimeout(function () {
                const $firstInvalid = $firstErrorContainer.find('.allocation-input').first();
                if ($firstInvalid.length) {
                    $('html, body').animate({
                        scrollTop: $firstInvalid.offset().top - 120
                    }, 400);
                }
            }, 300);

        }

        if (hasError) {
            alert('Please fix the following allocation issues:\n\n• ' + errorMessages.join('\n• '));
            return false;
        }

        return true;

    }


    /* ============================================================
     FORM SUBMIT HANDLER
     ============================================================ */

    function handleFormSubmit(e) {

        e.preventDefault();
        e.stopImmediatePropagation();

        const saveBtn = document.getElementById('saveButton');
        if (saveBtn.disabled)
            return;

        // Step 1 — Validate allocations directly (no .trigger('input'))
        if (!validateAllAllocations())
            return;

        // Step 2 — Validate attachments
        const rows = document.querySelectorAll('#attachment-table-body .attachment-row');
        let hasValidAttachment = false;
        let attachmentErrors = [];

        rows.forEach((row, index) => {

            const docType = row.querySelector('select').value.trim();
            const docNo = row.querySelector('input[type="text"]').value.trim();
            const file = row.querySelector('input[type="file"]').files.length;
            const hasAnyValue = docType || docNo || file;

            if (hasAnyValue) {
                if (!docType)
                    attachmentErrors.push(`Row ${index + 1}: Select document type`);
                if (!docNo)
                    attachmentErrors.push(`Row ${index + 1}: Enter document number`);
                if (!file)
                    attachmentErrors.push(`Row ${index + 1}: Upload file`);

                if (docType && docNo && file) {
                    hasValidAttachment = true;
                }
            }

        });

        if (!hasValidAttachment) {
            alert('Please complete at least one attachment:\n• Document Type\n• Document No\n• File');
            return;
        }

        if (attachmentErrors.length > 0) {
            alert('Please fix attachment errors:\n\n' + attachmentErrors.join('\n'));
            return;
        }

        // Step 3 — Final confirmation before submitting
        if (confirm('Are you sure you want to save this order receive?\n\nThis will update inventory and cannot be undone.')) {

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            $('#myForm').off('submit');
            document.getElementById('myForm').submit();

        }

    }


    /* ============================================================
     BIND FORM SUBMIT — Override Yii2 ActiveForm handler
     ============================================================ */

    $('#myForm').off('submit').on('submit', handleFormSubmit);


    /* ============================================================
     COLLAPSE RESET — Only clear errors when panel is CLOSED.
     ============================================================ */

    $(document).on('hide.bs.collapse', '.collapse', function () {

        const $container = $(this).closest('[data-receive-qty]');

        if (!$container.length)
            return;

        $container.find('.allocation-error-message').remove();
        $container.find('.allocation-success-message').remove();
        $container.find('.allocation-input').removeClass('is-invalid');

    });


    /* ============================================================
     DOM CONTENT LOADED — Final init
     ============================================================ */

    document.addEventListener('DOMContentLoaded', function () {

        updateRowNumbers();

        setTimeout(function () {
            $('.allocation-input').trigger('input');
            checkAllAllocationsAndOpenCollapses();
        }, 500);

    });

</script>