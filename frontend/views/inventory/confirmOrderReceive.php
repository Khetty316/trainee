<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyFormatter;

$this->title = 'Confirm Order Receive';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Receiving', 'url' => ['executive-pending-receive-purchase-order']];
$this->params['breadcrumbs'][] = ['label' => $po->po_no];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mb-5">
    <?php
    $form = ActiveForm::begin([
        'id' => 'myForm',
        'method' => 'post',
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
        <div class="col-md-4 text-right">
            <?php if (!empty($postData)) { ?>
                <?=
                Html::submitButton('Save', [
                    'class' => 'btn btn-success px-3 mb-3',
                    'id' => 'saveButton',
                    'onclick' => 'return confirmSubmit(event)'
                ])
                ?>
            <?php } ?>
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
        <div class="col-lg-7 col-md-12 col-sm-12">
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
                                    ?>
                                    <tr>
                                <input type="hidden" name="receive[<?= $index ?>][id]" value="<?= $item['id'] ?>" />
                                <input type="hidden" name="receive[<?= $index ?>][new_receive_qty]" value="<?= $item['new_receive_qty'] ?>" />
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= Html::encode($detail->inventoryDetail->code) ?></td>
                                <td>
                                    <?= Html::encode($detail->brand->name) ?>, 
                                    <?= Html::encode($detail->model_description) ?>
                                    <br>
                                    MODEL: <?= Html::encode($detail->model_type) ?>
                                </td>
                                <td class="text-right"><?= number_format($detail->unit_price, 2) ?></td>
                                <td class="text-right"><?= number_format($detail->discount_amt, 2) ?></td>
                                <td class="text-right"><?= number_format($detail->total_price, 2) ?></td>
                                <td class="text-center"><?= $detail->order_qty ?? 0 ?></td>
                                <td class="text-center"><?= $detail->received_qty ?? 0 ?></td>
                                <td class="text-center"><?= $detail->remaining_qty ?? 0 ?></td>
                                <td class="text-center"><strong><?= $item['new_receive_qty'] ?? 0 ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No items to receive</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </fieldset>
        </div>

        <div class="col-lg-5 col-md-12 col-sm-12">
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
                    <i class="fa fa-plus"></i> Add More
                </button>
            </fieldset>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script>
    let attachmentCount = 1;
    let isValidating = false;

    function confirmSubmit(event) {
        if (!isValidating) {
            return true; // Let the form validation run first
        }

        // This runs after validation passes
        return confirm('Are you sure you want to save this order receive? This action cannot be undone.');
    }

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
                <input type="text" name="InventoryPurchaseOrderItemDoc[document_no][]" class="form-control form-control-sm" placeholder="Enter Document No.">
            </td>
            <td>
                <input type="file" name="InventoryPurchaseOrderItemDoc[filename][]" class="form-control form-control-sm" accept=".pdf, .doc, .docx, .xls, .xlsx, .jpg, .jpeg, .png">
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
            const numberCell = row.querySelector('td:first-child');
            numberCell.textContent = index + 1;

            const deleteBtn = row.querySelector('button');
            if (rows.length === 1) {
                deleteBtn.disabled = true;
            } else {
                deleteBtn.disabled = false;
            }
        });
    }

    document.getElementById('myForm').addEventListener('submit', function (e) {
        if (isValidating) {
            return true;
        }

        e.preventDefault();

        const rows = document.querySelectorAll('#attachment-table-body .attachment-row');
        let hasValidAttachment = false;
        let errors = [];

        rows.forEach((row, index) => {
            let docTypeSelect = row.querySelector('select[name="InventoryPurchaseOrderItemDoc[document_type][]"]');
            let docNoInput = row.querySelector('input[type="text"][name="InventoryPurchaseOrderItemDoc[document_no][]"]');
            let fileInput = row.querySelector('input[type="file"][name="InventoryPurchaseOrderItemDoc[filename][]"]');

            if (!docTypeSelect)
                docTypeSelect = row.querySelector('select');
            if (!docNoInput)
                docNoInput = row.querySelector('input[type="text"]');
            if (!fileInput)
                fileInput = row.querySelector('input[type="file"]');

            const docTypeValue = docTypeSelect ? docTypeSelect.value.trim() : '';
            const docNoValue = docNoInput ? docNoInput.value.trim() : '';
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

            const hasAnyValue = docTypeValue !== '' || docNoValue !== '' || hasFile;

            if (hasAnyValue) {
                if (!docTypeValue || docTypeValue === '') {
                    errors.push(`Row ${index + 1}: Please select a document type.`);
                }
                if (!docNoValue || docNoValue === '') {
                    errors.push(`Row ${index + 1}: Please enter a document number.`);
                }
                if (!hasFile) {
                    errors.push(`Row ${index + 1}: Please select a file.`);
                }

                if (docTypeValue && docTypeValue !== '' && docNoValue && docNoValue !== '' && hasFile) {
                    hasValidAttachment = true;
                }
            }
        });

        if (!hasValidAttachment) {
            alert('Please complete at least one attachment with all required fields:\n• Document Type\n• Document No.\n• File');
            return false;
        }

        if (errors.length > 0) {
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
            return false;
        }

        // Validation passed - now show confirmation
        if (confirm('Are you sure you want to save this order receive?\n\nThis will update the inventory and cannot be undone.')) {
            isValidating = true;
            this.submit();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        updateRowNumbers();
    });
</script>