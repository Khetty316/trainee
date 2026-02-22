<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefCurrencies;

$supplier = $po->supplier;

// Set default currency_id if not set
if ($po->isNewRecord && empty($po->currency_id)) {
    $currencyKeys = array_keys($currencyList);
    $po->currency_id = !empty($currencyKeys) ? $currencyKeys[0] : null;
}
?>

<div class="po">
    <div class="po-form">

        <div class="po-header">
            <div class="row">
                <div class="col-md-7">
                    <div style="padding: 10px; min-height: 150px;">
                        <div style="font-weight: bold;"><?= Html::encode($supplier->name) ?></div>
                        <div style="margin-bottom: 10px;">
                            <?= Html::encode($supplier->address1) ?><br>
                            <?= Html::encode($supplier->address2) ?><br>
                            <?= Html::encode($supplier->address3) ?><br>
                            <?= Html::encode($supplier->address4) ?>
                        </div>
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 80px;">ATTN.</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;"><?= Html::encode($supplier->contact_name) ?></td>
                            </tr>
                            <tr>
                                <td style="width: 80px;">TEL.</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;"><?= Html::encode($supplier->contact_number) ?></td>
                            </tr>
                            <tr>
                                <td style="width: 80px;">FAX</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;"><?= Html::encode($supplier->contact_fax) ?></td>
                            </tr>
                            <tr>
                                <td style="width: 80px;">A/C NO.</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;"><?= Html::encode($supplier->code) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-5">
                    <div style="text-align: right;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: right; padding: 2px 5px;">NO.</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;">
                                    <?= $form->field($po, 'po_no')->textInput(['maxlength' => true, 'class' => 'form-control', 'readonly' => true, 'style' => 'height: auto;']) ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding: 2px 5px;">DATE</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;">
                                    <?php
                                    if (empty($po->po_date)) {
                                        $po->po_date = date('d/m/Y');
                                    }
                                    ?>
                                    <?=
                                    $form->field($po, 'po_date')->widget(yii\jui\DatePicker::className(), [
                                        'options' => ['class' => 'form-control', 'style' => 'height: auto;'],
                                        'dateFormat' => 'dd/MM/yyyy'
                                    ])
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding: 2px 5px;">COMPANY GROUP</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;">
                                    <?= $form->field($po, 'company_group')->dropDownList($companyGroupList, ['class' => 'form-control', 'style' => 'height: auto;']) ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: right; padding: 2px 5px;">CURRENCY</td>
                                <td style="width: 10px; padding: 2px 5px;">:</td>
                                <td style="padding: 2px 5px;">
                                    <?=
                                    $form->field($po, 'currency_id')->dropDownList($currencyList, [
                                        'class' => 'form-control',
                                        'id' => 'inventorypurchaseorder-currency',
                                        'style' => 'height: auto;'
                                    ])
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
            <table id="items-table" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr>
                        <th style="width: 3%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">#</th>
                        <th style="width: 10%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">ITEM NO.</th>
                        <th style="width: 30%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">DESCRIPTION</th>
                        <th style="width: 10%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">QTY</th>
                        <th style="width: 10%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">UNIT TYPE</th>
                        <th style="width: 10%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">U.PRICE</th>
                        <th style="width: 20%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 0; text-align: center; font-weight: bold;">
                            <div style="padding: 8px 5px 2px 5px;">DISCOUNT</div>
                            <div style="display: flex; border-top: 1px solid #999; margin-top: 2px;">
                                <div style="flex: 1; padding: 4px 5px; font-size: 11px; border-right: 1px solid #999; font-weight: normal;">%</div>
                                <div style="flex: 1; padding: 4px 5px; font-size: 11px; font-weight: normal;">Amt</div>
                            </div>
                        </th>
                        <th style="width: 10%; border-top: 2px solid #000; border-bottom: 2px solid #000; padding: 8px 5px; text-align: center; font-weight: bold;">AMOUNT</th>
                        <th style="text-align: center;"></th>
                    </tr>
                </thead>
                <tbody id="items-tbody">
                    <?php
                    $itemsTotalAmount = 0;
                    foreach ($purchaseOrderItems as $index => $poItem):
                        $quantity = $poItem->order_qty;
                        $unitType = $poItem->unit_type;
                        $unitPrice = $poItem->unit_price;
                        $discountAmt = $poItem->discount_amt ?? 0;
                        $totalPrice = $poItem->total_price;
                        $lineTotal = $quantity * $unitPrice;
                        $discountPercent = $lineTotal > 0 ? ($discountAmt / $lineTotal) * 100 : 0;
                        $itemAmount = ($quantity * $unitPrice) - $discountAmt;
                        $itemsTotalAmount += $itemAmount;
                        $isReceived = ($poItem->received_qty != 0 && $poItem->received_qty !== null);
                        $disabledAttr = $isReceived ? ['disabled' => true] : [];
                        ?>
                        <tr class="item-row"
                            data-index="<?= $index ?>"
                            data-item-id="<?= $poItem->id ?>"
                            data-is-received="<?= $isReceived ? 'true' : 'false' ?>"
                            <?= $isReceived ? 'style="background-color: #f0f0f0;"' : '' ?>>

                            <?= Html::hiddenInput("POItem[$index][id]", $poItem->id) ?>
                            <?= Html::hiddenInput("POItem[$index][inventory_detail_id]", $poItem->inventory_detail_id) ?>
                            <?= Html::hiddenInput("POItem[$index][removed]", 0, ['class' => 'item-removed-flag', 'id' => "removed-flag-$index"]) ?>
                            <?= Html::hiddenInput("POItem[$index][total_price]", number_format($totalPrice, 2, '.', ''), ['class' => 'item-pr-total-price']) ?>

                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                                <span class="row-number"><?= $index + 1 ?></span>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                                <span class="item-code"><?= Html::encode($poItem->inventoryDetail->code ?? ($index + 1)) ?></span>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd;">
                                <?= Html::encode($poItem->brand->name) ?>, <?= Html::encode($poItem->model_description) ?>
                                <?php if ($isReceived): ?>
                                    <span class="badge badge-success ml-2">Has Received Record</span>
                                <?php endif; ?>
                                <br>MODEL: <?= Html::encode($poItem->model_type) ?>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                                <span class="qty-display"><?= $quantity ?></span>
                                <?=
                                Html::a('<i class="fa fa-edit"></i>', "javascript:void(0)", [
                                    'title' => "Edit Qty",
                                    'value' => yii\helpers\Url::to(['view-po-item-detail', 'poItemId' => $poItem->id, 'moduleIndex' => $moduleIndex]),
                                    'class' => 'modalButton ml-2',
                                    'data-modaltitle' => 'Edit Qty',
                                ])
                                ?>
                                <?= Html::hiddenInput("POItem[$index][order_qty]", $quantity, ['class' => 'item-qty', 'data-index' => $index]) ?>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                                <?=
                                Html::input('text', "POItem[$index][unit_type]", $unitType, array_merge([
                                    'class' => 'form-control text-center item-unit_type', 'data-index' => $index, 'style' => 'height: auto; width: 100%;',
                                                ], $disabledAttr))
                                ?>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: right;">
                                <?=
                                Html::input('number', "POItem[$index][unit_price]", number_format($unitPrice, 2, '.', ''), array_merge([
                                    'class' => 'form-control text-right item-price', 'step' => '0.01', 'data-index' => $index, 'style' => 'height: auto; width: 100%;',
                                                ], $disabledAttr))
                                ?>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: right;">
                                <div style="display: flex; gap: 5px;">
                                    <div style="flex: 1;">
                                        <?=
                                        Html::input('number', "POItem[$index][discount_percent]", number_format($discountPercent, 2, '.', ''), array_merge([
                                            'class' => 'form-control text-right item-discount-percent', 'step' => '0.01', 'data-index' => $index,
                                            'style' => 'height: auto; padding: 5px; width: 100%;', 'placeholder' => '0.00', 'max' => '100',
                                                        ], $disabledAttr))
                                        ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <?=
                                        Html::input('number', "POItem[$index][discount_amt]", number_format($discountAmt, 2, '.', ''), array_merge([
                                            'class' => 'form-control text-right item-discount', 'step' => '0.01', 'data-index' => $index,
                                            'style' => 'height: auto; padding: 5px; width: 100%;', 'placeholder' => '0.00',
                                                        ], $disabledAttr))
                                        ?>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: right;">
                                <span class="item-amount" data-index="<?= $index ?>"><?= number_format($itemAmount, 2) ?></span>
                            </td>
                            <td class="text-center" style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd;">
                                <?=
                                !$isReceived ? Html::a('<i class="fa fa-trash"></i>', '#', [
                                            'class' => 'btn btn-danger btn-sm btn-remove-item',
                                            'data-index' => $index,
                                            'data-item-id' => $poItem->id,
                                            'title' => 'Remove Item',
                                        ]) : '-'
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Item Button -->
        <div class="mt-2 mb-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addItemModal">
                <i class="fas fa-plus-circle"></i> Add Item
            </button>
        </div>

        <?= Html::hiddenInput('removed_po_item_ids', '', ['id' => 'removed-po-item-ids']) ?>

        <!-- Comment and Summary -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="comment">
                    <strong>COMMENT</strong><br>
                    <?= $form->field($po, 'comment')->textarea(['rows' => 6, 'class' => 'form-control', 'style' => 'height: auto; width: 100%;']) ?>
                </div>
                <div class="amount-words" style="margin-top: 20px; padding: 10px; border-bottom: 1px solid #000;">
                    <span id="amount-in-words">RINGGIT MALAYSIA : ZERO ONLY</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <table style="width: 100%; border-collapse: collapse;">
                    <tbody>
                        <tr>
                            <td style="padding: 8px 5px; text-align: right;">TOTAL QUANTITY</td>
                            <td style="text-align: right;"><span id="total-quantity"><?= $po->total_qty ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4">
                <table style="width: 100%; border-collapse: collapse; border-left: 1px solid #000; border-right: 1px solid #000;">
                    <tbody>
                        <tr>
                            <td style="padding: 8px 5px; text-align: left;">TOTAL</td>
                            <td style="padding: 8px 5px; text-align: right;"><span id="total-amount"><?= number_format($itemsTotalAmount, 2) ?></span></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 5px; border-bottom: 1px solid #000;">TOTAL DISCOUNT</td>
                            <td style="padding: 8px 5px; border-bottom: 1px solid #000; text-align: right;">
                                <?=
                                Html::input('number', 'InventoryPurchaseOrder[total_discount]', $po->total_discount ?? '0.00', [
                                    'class' => 'form-control text-right', 'step' => '0.01', 'id' => 'po-total-discount', 'style' => 'max-width:120px; float:right;'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 5px;">TAX</td>
                            <td style="padding: 8px 5px; text-align: right;">
                                <?=
                                Html::input('number', 'InventoryPurchaseOrder[tax_amount]', $po->tax_amount ?? '0.00', [
                                    'class' => 'form-control text-right', 'step' => '0.01', 'id' => 'tax-amount', 'style' => 'max-width:120px; float:right;'
                                ])
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 5px; border-bottom: 1px solid #000; font-weight: bold;">GROSS</td>
                            <td style="padding: 8px 5px; border-bottom: 1px solid #000; text-align: right; font-weight: bold;">
                                <?php
                                $defaultCurrencySign = 'RM';
                                if ($po->currency_id) {
                                    $defaultCurrency = RefCurrencies::findOne($po->currency_id);
                                    $defaultCurrencySign = $defaultCurrency ? trim($defaultCurrency->currency_sign) : 'RM';
                                }
                                ?>
                                <span id="gross-currency"><?= Html::encode($defaultCurrencySign) ?></span>&nbsp;
                                <span id="gross-amount"><?= number_format($itemsTotalAmount, 2) ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?= Html::hiddenInput("InventoryPurchaseOrder[total_quantity]", '', ['class' => 'total_quantity']) ?>
        <?= Html::hiddenInput("InventoryPurchaseOrder[total_amount]", '', ['class' => 'total_amount']) ?>
        <?= Html::hiddenInput("InventoryPurchaseOrder[net_amount]", '', ['class' => 'net_amount']) ?>
        <?= Html::hiddenInput("InventoryPurchaseOrder[gross_amount]", '', ['class' => 'gross_amount']) ?>

    </div>
</div>


<!-- ================================================================
     ADD ITEM MODAL
     ================================================================ -->
<div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Item
                    <small class="text-muted ml-2" style="font-size: 13px;">
                        Supplier: <strong><?= Html::encode($supplier->name) ?></strong>
                    </small>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <!-- Supplier lock notice -->
                <div class="alert alert-info py-2 mb-3">
                    <i class="fas fa-info-circle"></i>
                    Only items from supplier <strong><?= Html::encode($supplier->name) ?></strong> are shown.
                </div>

                <!-- Step 1: Search -->
                <div class="form-group">
                    <label><strong>Step 1:</strong> Search Item <small class="text-muted">(by model, brand, or item code)</small></label>
                    <div class="input-group">
                        <input type="text" id="item-search-input" class="form-control" placeholder="Type at least 2 characters...">
                        <div class="input-group-append">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                    </div>
                </div>

                <div id="item-search-loading" class="text-center py-2" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> Searching...
                </div>
                <div id="item-search-empty" class="alert alert-warning" style="display:none;">
                    No items found for this supplier matching your search.
                </div>

                <div id="item-search-results" style="max-height: 220px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px; display:none;">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="thead-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th>Item Code</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Description</th>
                                <th>Unit Price</th>
                                <th>Unit Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="item-search-tbody"></tbody>
                    </table>
                </div>

                <hr id="add-item-divider" style="display:none;">

                <!-- Step 2: Fill Details -->
                <div id="add-item-form" style="display:none;">
                    <label><strong>Step 2:</strong> Fill Item Details</label>

                    <div class="form-row mb-2">
                        <div class="col-md-3">
                            <label class="small text-muted">Item Code</label>
                            <input type="text" id="new-item-code" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Brand</label>
                            <input type="text" id="new-item-brand" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Model</label>
                            <input type="text" id="new-item-model" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted">Description</label>
                            <input type="text" id="new-item-description" class="form-control form-control-sm" readonly>
                        </div>
                    </div>

                    <div class="form-row mt-2">
                        <div class="col-md-2">
                            <label>Qty <span class="text-danger">*</span></label>
                            <input type="number" id="new-item-qty" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <label>Unit Type</label>
                            <input type="text" id="new-item-unit-type" class="form-control" placeholder="PCS">
                        </div>
                        <div class="col-md-2">
                            <label>Unit Price <span class="text-danger">*</span></label>
                            <input type="number" id="new-item-unit-price" class="form-control" step="0.01" min="0" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label>Discount %</label>
                            <input type="number" id="new-item-discount-percent" class="form-control" step="0.01" min="0" max="100" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label>Discount Amt</label>
                            <input type="number" id="new-item-discount-amt" class="form-control" step="0.01" min="0" value="0.00">
                        </div>
                        <div class="col-md-2">
                            <label>Amount</label>
                            <input type="text" id="new-item-amount" class="form-control font-weight-bold" readonly value="0.00">
                        </div>
                    </div>

                    <!-- Hidden metadata -->
                    <input type="hidden" id="new-item-inventory-detail-id">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-add-item" style="display:none;">
                    <i class="fas fa-check"></i> Add to PO
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Build a set of inventory_detail_ids already in the PO on page load
    const existingDetailIds = new Set();

    function rebuildExistingDetailIds() {
        existingDetailIds.clear();
        $('#items-tbody tr.item-row').each(function () {
            // Skip removed rows
            if ($(this).find('.item-removed-flag').val() == 1)
                return;
            if ($(this).is(':hidden'))
                return;

            const detailId = $(this).find('input[name*="[inventory_detail_id]"]').val();
            if (detailId)
                existingDetailIds.add(String(detailId));
        });
    }

    $(document).ready(function () {
        rebuildExistingDetailIds(); // populate on load
        calculateTotals();
        $('#inventorypurchaseorder-currency').trigger('change');
    });

    /* =========================================================
     Pass supplier_id to JS for AJAX filtering
     ========================================================= */
    const PO_SUPPLIER_ID = <?= (int) $po->supplier_id ?>;

    /* =========================================================
     Currency
     ========================================================= */
    const currencyData = <?= json_encode($currencies) ?>;
    const currencyById = {};
    currencyData.forEach(c => {
        currencyById[c.currency_id] = {name: c.currency_name, sign: c.currency_sign || c.currency_code};
    });

    let isSyncingDiscount = false;

    /* =========================================================
     calculateTotals
     ========================================================= */
    window.calculateTotals = function () {
        let grossItemsTotal = 0;
        let totalQty = 0;

        $('.item-row').each(function () {
            if ($(this).find('.item-removed-flag').val() == 1)
                return;
            if ($(this).is(':hidden'))
                return;

            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            let   discount = parseFloat($(this).find('.item-discount').val()) || 0;
            const lineTotal = qty * price;

            if (discount > lineTotal) {
                discount = lineTotal;
                $(this).find('.item-discount').val(lineTotal.toFixed(2));
            }

            const lineNet = lineTotal - discount;
            $(this).find('.item-amount').text(lineNet.toFixed(2));
            $(this).find('.item-pr-total-price').val(lineNet.toFixed(2));

            grossItemsTotal += lineNet;
            totalQty += qty;
        });

        const poDiscount = parseFloat($('#po-total-discount').val()) || 0;
        const netAfterDiscount = Math.max(0, grossItemsTotal - poDiscount);
        const tax = parseFloat($('#tax-amount').val()) || 0;
        const gross = netAfterDiscount + tax;

        $('#total-quantity').text(totalQty);
        $('#total-amount').text(grossItemsTotal.toFixed(2));
        $('#gross-amount').text(gross.toFixed(2));
        $('.total_quantity').val(totalQty);
        $('.total_amount').val(grossItemsTotal.toFixed(2));
        $('.net_amount').val(netAfterDiscount.toFixed(2));
        $('.gross_amount').val(gross.toFixed(2));

        updateAmountInWords(gross);
    };

    /* =========================================================
     removeItemFromPO
     ========================================================= */
    window.removeItemFromPO = function (itemId) {
        let row = $('#items-tbody').find('tr[data-item-id="' + itemId + '"]');
        if (row.length) {
            row.find('.item-removed-flag').val(1);
            let current = $('#removed-po-item-ids').val();
            $('#removed-po-item-ids').val(current ? current + ',' + itemId : itemId);
            row.hide();
            rebuildExistingDetailIds(); // ← keep in sync after removal
            calculateTotals();
        }
    };

    $(document).on('click', '.btn-remove-item', function (e) {
        e.preventDefault();
        removeItemFromPO($(this).data('item-id'));
    });

    /* =========================================================
     Discount sync (existing rows)
     ========================================================= */
    $(document).on('input', '.item-discount-percent', function () {
        if (isSyncingDiscount)
            return;
        const row = $(this).closest('.item-row');
        if (row.data('is-received') === true || row.data('is-received') === 'true')
            return;
        isSyncingDiscount = true;
        let pct = Math.min(100, Math.max(0, parseFloat($(this).val()) || 0));
        $(this).val(pct.toFixed(2));
        const lt = (parseFloat(row.find('.item-qty').val()) || 0) * (parseFloat(row.find('.item-price').val()) || 0);
        row.find('.item-discount').val(((pct / 100) * lt).toFixed(2));
        isSyncingDiscount = false;
        calculateTotals();
    });

    $(document).on('input', '.item-discount', function () {
        if (isSyncingDiscount)
            return;
        const row = $(this).closest('.item-row');
        if (row.data('is-received') === true || row.data('is-received') === 'true')
            return;
        isSyncingDiscount = true;
        const lt = (parseFloat(row.find('.item-qty').val()) || 0) * (parseFloat(row.find('.item-price').val()) || 0);
        let   dAmt = Math.min(lt, Math.max(0, parseFloat($(this).val()) || 0));
        $(this).val(dAmt.toFixed(2));
        row.find('.item-discount-percent').val(lt > 0 ? ((dAmt / lt) * 100).toFixed(2) : '0.00');
        isSyncingDiscount = false;
        calculateTotals();
    });

    $(document).on('input', '.item-price', function () {
        const row = $(this).closest('.item-row');
        if (row.data('is-received') === true || row.data('is-received') === 'true')
            return;
        isSyncingDiscount = true;
        const lt = (parseFloat(row.find('.item-qty').val()) || 0) * (parseFloat($(this).val()) || 0);
        const pct = parseFloat(row.find('.item-discount-percent').val()) || 0;
        let   dAmt = (pct / 100) * lt;
        if (dAmt > lt) {
            dAmt = lt;
            row.find('.item-discount-percent').val('100.00');
        }
        row.find('.item-discount').val(dAmt.toFixed(2));
        isSyncingDiscount = false;
        calculateTotals();
    });

    $(document).on('input', '#tax-amount, #po-total-discount', function () {
        calculateTotals();
    });

    /* =========================================================
     Currency
     ========================================================= */
    $('#inventorypurchaseorder-currency').on('change', function () {
        const cur = currencyById[$(this).val()];
        if (cur) {
            $('#gross-currency').text(cur.sign);
            updateAmountInWords(parseFloat($('.gross_amount').val()) || 0);
        }
    });

    function updateAmountInWords(amount) {
        const cur = currencyById[$('#inventorypurchaseorder-currency').val()];
        const name = (cur && cur.name) ? cur.name.toUpperCase() : 'RINGGIT MALAYSIA';
        $('#amount-in-words').text(name + ' : ' + numberToWords(amount).toUpperCase() + ' ONLY');
    }

    function numberToWords(num) {
        if (num === 0)
            return 'ZERO';
        const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE'];
        const tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
        const teens = ['TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
        function chunk(n) {
            let s = '';
            if (n >= 100) {
                s += ones[Math.floor(n / 100)] + ' HUNDRED ';
                n %= 100;
            }
            if (n >= 10 && n <= 19)
                s += teens[n - 10] + ' ';
            else {
                if (n >= 20) {
                    s += tens[Math.floor(n / 10)] + ' ';
                    n %= 10;
                }
                if (n > 0)
                    s += ones[n] + ' ';
            }
            return s.trim();
        }
        const parts = num.toFixed(2).split('.');
        const whole = parseInt(parts[0], 10), cents = parseInt(parts[1], 10);
        let r = '';
        if (whole >= 1000000)
            r += chunk(Math.floor(whole / 1000000)) + ' MILLION ';
        if (whole >= 1000)
            r += chunk(Math.floor((whole % 1000000) / 1000)) + ' THOUSAND ';
        r += chunk(whole % 1000);
        if (cents > 0)
            r += ' AND CENTS ' + chunk(cents);
        return r.trim();
    }

    window.getNextItemNumber = function () {
        let max = 0;
        $('.item-code').each(function () {
            const t = $(this).text().trim(), m = t.match(/(\d+)$/);
            const n = m ? parseInt(m[1], 10) : (parseInt(t, 10) || 0);
            if (n > max)
                max = n;
        });
        return max + 1;
    };

    /* =========================================================
     ADD ITEM MODAL
     ========================================================= */
    let searchTimer = null;
    let selectedItem = null;

    $('#addItemModal').on('show.bs.modal', function () {
        selectedItem = null;
        $('#item-search-input').val('');
        $('#item-search-results, #item-search-empty, #item-search-loading, #add-item-form, #add-item-divider').hide();
        $('#btn-confirm-add-item').hide();
        $('#item-search-tbody').empty();
        resetNewItemForm();
    });

    $('#addItemModal').on('shown.bs.modal', function () {
        $('#item-search-input').focus();
    });

    $('#item-search-input').on('input', function () {
        const q = $(this).val().trim();
        clearTimeout(searchTimer);
        $('#item-search-results, #item-search-empty').hide();

        if (q.length < 2) {
            $('#item-search-loading').hide();
            return;
        }

        $('#item-search-loading').show();

        searchTimer = setTimeout(function () {
            $.ajax({
                // -------------------------------------------------------
                // BACKEND: actionSearchInventoryItems
                // Filters by supplier_id AND search keyword q
                //
                // Controller example (see below this script block)
                // -------------------------------------------------------
                url: '<?= yii\helpers\Url::to(['/inventory/inventory/search-inventory-items']) ?>',
                data: {
                    q: q,
                    supplier_id: PO_SUPPLIER_ID   // <-- supplier filter passed here
                },
                dataType: 'json',
                success: function (data) {
                    $('#item-search-loading').hide();
                    $('#item-search-tbody').empty();

                    if (!data || data.length === 0) {
                        $('#item-search-empty').show();
                        return;
                    }

                    $.each(data, function (i, item) {
                        const tr = $('<tr style="cursor:pointer;" class="item-result-row">').html(
                                '<td>' + escHtml(item.code || '-') + '</td>' +
                                '<td>' + escHtml(item.brand_name || '-') + '</td>' +
                                '<td>' + escHtml(item.model_type || '-') + '</td>' +
                                '<td>' + escHtml(item.model_description || '-') + '</td>' +
                                '<td class="text-right">' + escHtml(item.unit_price || '0.00') + '</td>' +
                                '<td>' + escHtml(item.unit_type || '-') + '</td>' +
                                '<td><button type="button" class="btn btn-success btn-sm">Select</button></td>'
                                ).data('item', item);

                        tr.on('click', function () {
                            selectItem($(this).data('item'));
                        });
                        $('#item-search-tbody').append(tr);
                    });

                    $('#item-search-results').show();
                },
                error: function () {
                    $('#item-search-loading').hide();
                    $('#item-search-empty').text('Search failed. Please try again.').show();
                }
            });
        }, 400);
    });

    function selectItem(item) {
        selectedItem = item;

        // Warn if already in PO
        if (existingDetailIds.has(String(item.inventory_detail_id))) {
            $('#add-item-divider, #add-item-form').hide();
            $('#btn-confirm-add-item').hide();
            $('#item-search-empty')
                    .removeClass('alert-warning')
                    .addClass('alert-danger')
                    .html('<i class="fas fa-exclamation-triangle"></i> <strong>' + escHtml(item.brand_name) + ' - ' + escHtml(item.model_type) + '</strong> is already in this PO. Please edit the existing row instead.')
                    .show();
            return; // stop here, don't populate the form
        }

        // Reset warning style in case previously shown
        $('#item-search-empty').removeClass('alert-danger').addClass('alert-warning');


        $('#new-item-code').val(item.code || '');
        $('#new-item-brand').val(item.brand_name || '');
        $('#new-item-model').val(item.model_type || '');
        $('#new-item-description').val(item.model_description || '');
        // Pre-fill unit price from inventory_detail.unit_price and unit_type
        $('#new-item-unit-type').val(item.unit_type || '');
        $('#new-item-unit-price').val(parseFloat(item.unit_price || 0).toFixed(2));

        $('#new-item-inventory-detail-id').val(item.inventory_detail_id || '');
        $('#new-item-brand-id').val(item.brand_id || '');
        $('#new-item-model-type').val(item.model_type || '');
        $('#new-item-model-group').val(item.model_group || '');
        $('#new-item-department-code').val(item.department_code || '');

        $('#new-item-qty').val(1);
        $('#new-item-discount-percent').val('0.00');
        $('#new-item-discount-amt').val('0.00');

        // Calculate initial amount
        recalcModalAmount();

        $('#item-search-results').hide();
        $('#item-search-input').val(item.brand_name + ' — ' + item.model_type + ' (' + item.code + ')');
        $('#add-item-divider, #add-item-form').show();
        $('#btn-confirm-add-item').show();
    }

    function resetNewItemForm() {
        $('#new-item-code, #new-item-brand, #new-item-model, #new-item-description, #new-item-unit-type').val('');
        $('#new-item-qty').val(1);
        $('#new-item-unit-price, #new-item-discount-percent, #new-item-discount-amt').val('0.00');
        $('#new-item-amount').val('0.00');
        $('#new-item-inventory-detail-id, #new-item-brand-id, #new-item-model-type, #new-item-model-group, #new-item-department-code').val('');
    }

    function recalcModalAmount() {
        const qty = parseFloat($('#new-item-qty').val()) || 0;
        const price = parseFloat($('#new-item-unit-price').val()) || 0;
        const disc = parseFloat($('#new-item-discount-amt').val()) || 0;
        $('#new-item-amount').val(Math.max(0, qty * price - disc).toFixed(2));
    }

    $('#new-item-discount-percent').on('input', function () {
        const qty = parseFloat($('#new-item-qty').val()) || 0;
        const price = parseFloat($('#new-item-unit-price').val()) || 0;
        const lt = qty * price;
        const pct = Math.min(100, Math.max(0, parseFloat($(this).val()) || 0));
        $('#new-item-discount-amt').val(((pct / 100) * lt).toFixed(2));
        recalcModalAmount();
    });

    $('#new-item-discount-amt').on('input', function () {
        const qty = parseFloat($('#new-item-qty').val()) || 0;
        const price = parseFloat($('#new-item-unit-price').val()) || 0;
        const lt = qty * price;
        let   dAmt = Math.min(lt, Math.max(0, parseFloat($(this).val()) || 0));
        $(this).val(dAmt.toFixed(2));
        $('#new-item-discount-percent').val(lt > 0 ? ((dAmt / lt) * 100).toFixed(2) : '0.00');
        recalcModalAmount();
    });

    $('#new-item-qty, #new-item-unit-price').on('input', function () {
        const qty = parseFloat($('#new-item-qty').val()) || 0;
        const price = parseFloat($('#new-item-unit-price').val()) || 0;
        const lt = qty * price;
        const pct = parseFloat($('#new-item-discount-percent').val()) || 0;
        let   dAmt = (pct / 100) * lt;
        if (dAmt > lt) {
            dAmt = lt;
            $('#new-item-discount-percent').val('100.00');
        }
        $('#new-item-discount-amt').val(dAmt.toFixed(2));
        recalcModalAmount();
    });

    // Confirm: append new row to table
    $('#btn-confirm-add-item').on('click', function () {
        if (!selectedItem) {
            alert('Please select an item first.');
            return;
        }

        // ← CHECK: is this item already in the PO?
        const detailId = String($('#new-item-inventory-detail-id').val());
        if (existingDetailIds.has(detailId)) {
            alert('This item has already been added to the PO. Please update the existing row instead.');
            return;
        }

        const qty = parseFloat($('#new-item-qty').val()) || 0;
        const unitType = $('#new-item-unit-type').val().trim();
        const unitPrice = parseFloat($('#new-item-unit-price').val()) || 0;
        const discPercent = parseFloat($('#new-item-discount-percent').val()) || 0;
        const discAmt = parseFloat($('#new-item-discount-amt').val()) || 0;
        const amount = Math.max(0, (qty * unitPrice) - discAmt);

        if (qty <= 0) {
            alert('Quantity must be greater than 0.');
            return;
        }
        if (unitPrice <= 0) {
            alert('Unit price must be greater than 0.');
            return;
        }

        const newIndex = $('#items-tbody tr').length; // use total rows to avoid index collision

        const html = `
            <tr class="item-row" data-index="${newIndex}" data-item-id="new-${newIndex}" data-is-received="false">
                <input type="hidden" name="POItem[${newIndex}][id]"                  value="">
                <input type="hidden" name="POItem[${newIndex}][inventory_detail_id]" value="${escHtml($('#new-item-inventory-detail-id').val())}">
                <input type="hidden" name="POItem[${newIndex}][removed]"             value="0"   class="item-removed-flag" id="removed-flag-${newIndex}">
                <input type="hidden" name="POItem[${newIndex}][total_price]"         value="${amount.toFixed(2)}" class="item-pr-total-price">
                <input type="hidden" name="POItem[${newIndex}][order_qty]"           value="${qty}" class="item-qty" data-index="${newIndex}">

                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:center;">
                    <span class="row-number">${$('#items-tbody tr:visible').length + 1}</span>
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:center;">
                    <span class="item-code">${escHtml($('#new-item-code').val() || '-')}</span>
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;">
                    ${escHtml($('#new-item-brand').val())}, ${escHtml($('#new-item-description').val())}
                    <br>MODEL: ${escHtml($('#new-item-model').val())}
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:center;">
                    <span class="qty-display">${qty}</span>
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:center;">
                    <input type="text" name="POItem[${newIndex}][unit_type]" value="${escHtml(unitType)}"
                           class="form-control text-center item-unit_type" data-index="${newIndex}"
                           style="height:auto;width:100%;">
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:right;">
                    <input type="number" name="POItem[${newIndex}][unit_price]" value="${unitPrice.toFixed(2)}"
                           class="form-control text-right item-price" step="0.01" data-index="${newIndex}"
                           style="height:auto;width:100%;">
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:right;">
                    <div style="display:flex;gap:5px;">
                        <div style="flex:1;">
                            <input type="number" name="POItem[${newIndex}][discount_percent]" value="${discPercent.toFixed(2)}"
                                   class="form-control text-right item-discount-percent" step="0.01" max="100"
                                   data-index="${newIndex}" style="height:auto;padding:5px;width:100%;" placeholder="0.00">
                        </div>
                        <div style="flex:1;">
                            <input type="number" name="POItem[${newIndex}][discount_amt]" value="${discAmt.toFixed(2)}"
                                   class="form-control text-right item-discount" step="0.01"
                                   data-index="${newIndex}" style="height:auto;padding:5px;width:100%;" placeholder="0.00">
                        </div>
                    </div>
                </td>
                <td style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;text-align:right;">
                    <span class="item-amount" data-index="${newIndex}">${amount.toFixed(2)}</span>
                </td>
                <td class="text-center" style="padding:8px 5px;vertical-align:top;border-bottom:1px solid #ddd;">
                    <a href="#" class="btn btn-danger btn-sm btn-remove-item"
                       data-index="${newIndex}" data-item-id="new-${newIndex}" title="Remove Item">
                        <i class="fa fa-trash"></i>
                    </a>
                </td>
            </tr>`;

        $('#items-tbody').append(html);
        rebuildExistingDetailIds(); // ← keep the set in sync
        calculateTotals();
        $('#addItemModal').modal('hide');
    });

    function escHtml(str) {
        return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
    }

    /* =========================================================
     Init
     ========================================================= */
    $(document).ready(function () {
        calculateTotals();
        $('#inventorypurchaseorder-currency').trigger('change');
    });
</script>