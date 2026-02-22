<?php

use yii\helpers\Html;

$departmentList = frontend\models\common\RefUserDepartments::getDropDownList();
$departmentList = ['' => 'ALL'] + $departmentList;
// Create an array of existing item IDs for quick lookup
$existingItemIds = [];
if (!empty($purchaseRequestItems)) {
    foreach ($purchaseRequestItems as $prItem) {
        $existingItemIds[] = $prItem->id;
    }
}

// Received items IDs (passed from controller)
$receivedItemIds = isset($receivedItemIds) ? $receivedItemIds : [];

// to properly check if items are received
$inventoryDetailToPrItemMap = [];

if (in_array($page, ['new', 'reorder', 'receiving'], true) && !empty($po->inventoryPurchaseOrderItems)) {
    foreach ($po->inventoryPurchaseOrderItems as $item) {
        $detailId = $item->inventory_detail_id ?? null;
        $prItemId = $item->inventory_pr_item_id ?? null;

        if ($detailId && $prItemId) {
            $inventoryDetailToPrItemMap[$detailId] = $prItemId;
        }
    }
}

$issuedPo = in_array($page, ['new', 'reorder', 'receiving'], true);
?>
<div class="input-group mb-2">
    <label class="mr-2 mb-0 align-self-center">Department</label>
    <?=
    Html::dropDownList(
            'department',
            null,
            $departmentList,
            [
                'id' => 'departmentSelect',
                'class' => 'form-control w-auto'
            ]
    )
    ?>
</div>
<div style="max-height: 400px; overflow:auto;">
    <table class="table table-sm table-striped table-bordered" id="myList">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Model Type</th>
                <th>Description</th>
                <th class="text-center">
                    Select All<br>
                    <?=
                    Html::checkbox('selection_all', false, [
                        'id' => 'select-all-checkbox',
                        'style' => 'transform: scale(1.3); cursor:pointer;',
                    ])
                    ?>
                </th>
            </tr>
            <tr>
                <th><input class="form-control form-control-sm" id="brandFilter" type="text" placeholder="Search"></th>
                <th><input class="form-control form-control-sm" id="modelFilter" type="text" placeholder="Search"></th>
                <th><input class="form-control form-control-sm" id="descriptionFilter" type="text" placeholder="Search"></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($itemList as $item):
                // Determine if item is checked (already in PO)
                $isChecked = false;
                $poItemId = null; // Store the inventory_pr_item_id if it exists

                if ($issuedPo) {
                    // For "new" page, check if this inventory_detail is in any PO item
                    foreach ($po->inventoryPurchaseOrderItems as $poItem) {
                        if ($poItem->inventory_detail_id == $item->id) {
                            $isChecked = true;
                            $poItemId = $poItem->inventory_pr_item_id;
                            break;
                        }
                    }
                } else {
                    // For other pages, use existing logic
                    $isChecked = in_array($item->id, $existingItemIds);
                    $poItemId = $item->id; // For non-new pages, item->id is already inventory_pr_item_id
                }

                // Determine if item is received
                $isReceived = false;
                if ($issuedPo) {
                    // For "new" page, check if this inventory_detail has a received PO item
                    foreach ($po->inventoryPurchaseOrderItems as $poItem) {
                        if ($poItem->inventory_detail_id == $item->id && $poItem->received_qty > 0) {
                            $isReceived = true;
                            break;
                        }
                    }
                } else {
                    // For other pages, use existing logic
                    $isReceived = in_array($item->id, $receivedItemIds);
                }

                // Get quantity and unit price from PO item if it exists
                $quantity = $item->quantity ?? 1;
                $unitPrice = $item->unit_price ?? '0.00';
                $discount = $item->discount_amt ?? '0.00';

                // Try to get values from existing PO item
                if ($issuedPo) {
                    foreach ($po->inventoryPurchaseOrderItems as $poItem) {
                        if ($poItem->inventory_detail_id == $item->id) {
                            $quantity = $poItem->order_qty ?? $quantity;
                            $unitPrice = $poItem->unit_price ?? $unitPrice;
                            $discount = $poItem->discount_amt ?? $discount;
                            break;
                        }
                    }
                }

                // For the checkbox value, we need to use the correct ID
                // For "new" page: use inventory_pr_item_id if exists, otherwise use inventory_detail_id
                // For other pages: use inventory_pr_item_id
                $checkboxValue = $poItemId ? $poItemId : $item->id;

                // Also store the inventory_detail_id in a data attribute for mapping
                $inventoryDetailId = ($issuedPo) ? $item->id : null;
                ?>
                <tr
                <?= $isReceived ? 'style="background-color: #f0f0f0;"' : '' ?>
                    data-department="<?= Html::encode($item->department_code ?? '') ?>"
                    >
                    <td class="brand"><?= Html::encode($item->brand->name ?? '-') ?></td>
                    <td class="model"><?= Html::encode($item->model->type ?? $item->model_type) ?></td>
                    <td class="description">
                        <?= Html::encode($item->model->description ?? $item->model_description) ?>
                        <?php if ($isReceived): ?>
                            <span class="badge badge-success ml-2">Has Received Record</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?=
                        Html::checkbox('items[]', $isChecked, [
                            'value' => $checkboxValue,
                            'class' => 'item-checkbox',
                            'style' => 'transform: scale(1.3); cursor:pointer;',
                            'disabled' => $isReceived,
                            'data-item-no' => $item->code ?? '',
                            'data-brand-id' => $item->brand_id,
                            'data-brand-name' => $item->brand->name ?? 'N/A',
                            'data-model-type' => $item->model->type ?? $item->model_type,
                            'data-model-group' => $item->model->group ?? $item->model_group,
                            'data-model-description' => $item->model->description ?? $item->model_description,
                            'data-department-code' => $item->department_code ?? '',
                            'data-quantity' => $quantity,
                            'data-unit-price' => $unitPrice,
                            'data-discount' => $discount,
                            'data-is-received' => $isReceived ? 'true' : 'false',
                            'data-inventory-detail-id' => $inventoryDetailId,
                            'data-inventory-pr-item-id' => $poItemId,
                        ])
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(function () {
        // Set the page type globally
        window.pageType = '<?= $page ?>';

        // Create a mapping object for inventory_detail_id to inventory_pr_item_id
        window.inventoryDetailToPrItemMap = {};
<?php
if ($page === 'new' && !empty($inventoryDetailToPrItemMap)) {
    foreach ($inventoryDetailToPrItemMap as $detailId => $prItemId) {
        echo "window.inventoryDetailToPrItemMap[$detailId] = $prItemId;\n";
    }
}
?>

        // ---- Filters ----
        function filterTable() {
            const brand = $('#brandFilter').val().toLowerCase();
            const model = $('#modelFilter').val().toLowerCase();
            const desc = $('#descriptionFilter').val().toLowerCase();
            const dept = $('#departmentSelect').val(); // department code

            $('#myList tbody tr').each(function () {
                const b = $(this).find('.brand').text().toLowerCase();
                const m = $(this).find('.model').text().toLowerCase();
                const d = $(this).find('.description').text().toLowerCase();
                const rowDept = $(this).data('department') || '';

                const matchText = b.includes(brand) && m.includes(model) && d.includes(desc);
                const matchDept = !dept || rowDept === dept;

                $(this).toggle(matchText && matchDept);
            });
        }

        $('#departmentSelect').on('change', filterTable);
        $('#brandFilter, #modelFilter, #descriptionFilter').on('keyup', filterTable);

        // ---- Select All ----
        $('#select-all-checkbox').on('change', function () {
            $('.item-checkbox:visible:not(:disabled)').prop('checked', this.checked).trigger('change');
        });

        function syncSelectAll() {
            const visibleCheckboxes = $('.item-checkbox:visible:not(:disabled)');
            const checkedCheckboxes = $('.item-checkbox:visible:checked:not(:disabled)');
            $('#select-all-checkbox').prop('checked', visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedCheckboxes.length);
        }

        syncSelectAll();

        // ---- Checkbox change event ----
        $('.item-checkbox').on('change', function () {
            const checkbox = $(this);
            const itemId = checkbox.val();
            const isReceived = checkbox.data('is-received') === 'true';
            const inventoryDetailId = checkbox.data('inventory-detail-id');
            const inventoryPrItemId = checkbox.data('inventory-pr-item-id');

            console.log('Checkbox changed. Item ID:', itemId,
                    'Inventory Detail ID:', inventoryDetailId,
                    'Inventory PR Item ID:', inventoryPrItemId,
                    'Checked:', checkbox.is(':checked'),
                    'Page type:', window.pageType);

            if (checkbox.is(':checked')) {
                console.log('Adding item to PO:', itemId);
                addItemToPO({
                    id: itemId,
                    inventory_detail_id: inventoryDetailId,
                    inventory_pr_item_id: inventoryPrItemId,
                    code: checkbox.data('item-no'),
                    brand_id: checkbox.data('brand-id'),
                    brand_name: checkbox.data('brand-name'),
                    model_type: checkbox.data('model-type'),
                    model_group: checkbox.data('model-group'),
                    model_description: checkbox.data('model-description'),
                    department_code: checkbox.data('department-code'),
                    quantity: checkbox.data('quantity'),
                    unit_type: checkbox.data('unit_type'),
                    unit_price: checkbox.data('unit-price'),
                    discount: checkbox.data('discount'),
                    is_received: isReceived
                });
            } else {
                console.log('Removing item from PO:', itemId);
                removeItemFromPO(itemId);
            }

            syncSelectAll();
        });

        // ---- Add Item Function ----
        function addItemToPO(item) {
            // Check if item already exists
            let existingRow = null;

            // Try multiple ways to find existing row
            existingRow = $('#items-tbody').find(`tr[data-item-id="${item.id}"]`);

            if (existingRow.length === 0) {
                existingRow = $('#items-tbody').find(`input[name*="[inventory_pr_item_id]"][value="${item.id}"]`).closest('tr');
            }

            if (existingRow.length === 0 && item.inventory_detail_id) {
                existingRow = $('#items-tbody').find(`input[name*="[inventory_detail_id]"][value="${item.inventory_detail_id}"]`).closest('tr');
            }

            if (existingRow.length > 0) {
                console.log('Item already exists in PO table:', item.id);
                return;
            }

            // Get the next row index
            const nextIndex = $('#items-tbody tr').length;

            // Determine which ID field to use based on what's available
            let hiddenInputs = '';
            if (item.inventory_detail_id) {
                hiddenInputs += `<input type="hidden" name="POItem[${nextIndex}][inventory_detail_id]" value="${item.inventory_detail_id}">`;
            }
            if (item.inventory_pr_item_id) {
                hiddenInputs += `<input type="hidden" name="POItem[${nextIndex}][inventory_pr_item_id]" value="${item.inventory_pr_item_id}">`;
            } else {
                // If no pr_item_id, use the id as pr_item_id
                hiddenInputs += `<input type="hidden" name="POItem[${nextIndex}][inventory_pr_item_id]" value="${item.id}">`;
            }

            // Create the row HTML
            const newRow = `
            <tr class="item-row" data-index="${nextIndex}" 
                data-item-id="${item.id}" 
                data-is-received="${item.is_received ? 'true' : 'false'}"
                ${item.is_received ? 'style="background-color: #f0f0f0;"' : ''}>
                
                ${hiddenInputs}
                <input type="hidden" name="POItem[${nextIndex}][department_code]" value="${item.department_code}">
                <input type="hidden" name="POItem[${nextIndex}][brand_id]" value="${item.brand_id}">
                <input type="hidden" name="POItem[${nextIndex}][model_type]" value="${item.model_type}">
                <input type="hidden" name="POItem[${nextIndex}][model_group]" value="${item.model_group}">
                <input type="hidden" name="POItem[${nextIndex}][model_description]" value="${item.model_description}">
                <input type="hidden" class="item-pr-total-price" name="POItem[${nextIndex}][total_price]" value="0">
                
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                    <span class="row-number">${nextIndex + 1}</span>
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                    <span class="item-code">${item.code || (nextIndex + 1)}</span>
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd;">
                    ${item.brand_name}, ${item.model_description}
                    ${item.is_received ? '<span class="badge badge-success ml-2">Has Received Record</span>' : ''}
                    <br>MODEL: ${item.model_type}
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                    <input type="number" name="POItem[${nextIndex}][order_qty]" 
                           value="${item.quantity}" 
                           class="form-control text-center item-qty" 
                           data-index="${nextIndex}"
                           style="height: auto; width: 100%;"
                           ${item.is_received ? 'disabled' : ''}>
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: center;">
                    <input type="text" name="POItem[${nextIndex}][unit_qty]" 
                           value="${item.unit_type}" 
                           class="form-control text-center item-unit-type" 
                           data-index="${nextIndex}"
                           style="height: auto; width: 100%;"
                           ${item.is_received ? 'disabled' : ''}>
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: right;">
                    <input type="number" name="POItem[${nextIndex}][unit_price]" 
                           value="${item.unit_price}" 
                           class="form-control text-right item-price" 
                           step="0.01"
                           data-index="${nextIndex}"
                           style="height: auto; width: 100%;"
                           ${item.is_received ? 'disabled' : ''}>
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: right;">
                    <div style="display: flex; gap: 5px;">
                        <div style="flex: 1;">
                            <input type="number" name="POItem[${nextIndex}][discount_percent]" 
                                   value="0.00" 
                                   class="form-control text-right item-discount-percent" 
                                   step="0.01"
                                   data-index="${nextIndex}"
                                   style="height: auto; padding: 5px; width: 100%;"
                                   placeholder="0.00"
                                   max="100"
                                   ${item.is_received ? 'disabled' : ''}>
                        </div>
                        <div style="flex: 1;">
                            <input type="number" name="POItem[${nextIndex}][discount_amt]" 
                                   value="${item.discount || '0.00'}" 
                                   class="form-control text-right item-discount" 
                                   step="0.01"
                                   data-index="${nextIndex}"
                                   style="height: auto; padding: 5px; width: 100%;"
                                   placeholder="0.00"
                                   ${item.is_received ? 'disabled' : ''}>
                        </div>
                    </div>
                </td>
                <td style="padding: 8px 5px; vertical-align: top; border-bottom: 1px solid #ddd; text-align: right;">
                    <span class="item-amount" data-index="${nextIndex}">0.00</span>
                </td>
            </tr>
        `;

            // Add the row to the table
            $('#items-tbody').append(newRow);

            // Update row numbers and calculate initial amount
            updateItemNumbers();
            calculateItemAmount(nextIndex);

            // Recalculate totals
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        }

        // ---- Remove Item Function ----
        function removeItemFromPO(itemId) {
            console.log('Attempting to remove item:', itemId, 'Page type:', window.pageType);

            let row = null;

            // First, try all possible ways to find the row

            // Method 1: Find by data-item-id on tr
            row = $('#items-tbody').find(`tr[data-item-id="${itemId}"]`);
            console.log('Method 1 (data-item-id):', row.length, 'rows found');

            if (row.length === 0) {
                // Method 2: Find by inventory_pr_item_id input (for existing items)
                row = $('#items-tbody').find(`input[name*="[inventory_pr_item_id]"][value="${itemId}"]`).closest('tr');
                console.log('Method 2 (inventory_pr_item_id):', row.length, 'rows found');
            }

            if (row.length === 0) {
                // Method 3: Find by inventory_detail_id input (for new items)
                row = $('#items-tbody').find(`input[name*="[inventory_detail_id]"][value="${itemId}"]`).closest('tr');
                console.log('Method 3 (inventory_detail_id):', row.length, 'rows found');
            }

            if (row.length === 0 && window.pageType === 'new') {
                // Method 4: For "new" page, we might need to map inventory_detail_id to inventory_pr_item_id
                // Check if there's a mapping in the page
                if (typeof window.inventoryDetailToPrItemMap !== 'undefined') {
                    const prItemId = window.inventoryDetailToPrItemMap[itemId];
                    if (prItemId) {
                        console.log('Found mapping:', itemId, '->', prItemId);
                        row = $('#items-tbody').find(`input[name*="[inventory_pr_item_id]"][value="${prItemId}"]`).closest('tr');
                        console.log('Method 4 (mapped to pr_item_id):', row.length, 'rows found');
                    }
                }
            }

            if (row && row.length) {
                console.log('Found and removing row for item ID:', itemId);
                row.remove();
                updateItemNumbers();
                if (typeof calculateTotals === 'function') {
                    calculateTotals();
                }
            } else {
                console.log('Item not found in table. Tried ID:', itemId);

                // Debug: Show all rows and their IDs
                console.log('All rows in table:');
                $('#items-tbody tr').each(function (index) {
                    const $row = $(this);
                    console.log(`Row ${index}:`, {
                        'data-item-id': $row.data('item-id'),
                        'inventory_detail_id': $row.find('input[name*="[inventory_detail_id]"]').val(),
                        'inventory_pr_item_id': $row.find('input[name*="[inventory_pr_item_id]"]').val()
                    });
                });
            }
        }

        // ---- Update Row Numbers & Input Names ----
        function updateItemNumbers() {
            $('#items-tbody tr').each(function (index) {
                const row = $(this);
                row.attr('data-index', index);

                // Update row number
                row.find('.row-number').text(index + 1);

                // Update item code if it's just a number (for new items)
                const itemCodeCell = row.find('.item-code');
                if (itemCodeCell.text().trim() === '' || /^\d+$/.test(itemCodeCell.text().trim())) {
                    // If it's empty or just a number, update it
                    if (typeof window.getNextItemNumber === 'function') {
                        itemCodeCell.text(window.getNextItemNumber());
                    } else {
                        // Fallback: use the existing item code or row number
                        const currentCode = itemCodeCell.text().trim();
                        if (!currentCode || currentCode === String(index)) {
                            itemCodeCell.text(index + 1);
                        }
                    }
                }

                // Update input names
                row.find('input').each(function () {
                    const input = $(this);
                    const name = input.attr('name');
                    if (name && name.startsWith('POItem[')) {
                        const newName = name.replace(/\[\d+\]/, `[${index}]`);
                        input.attr('name', newName);
                        input.attr('data-index', index);
                    }
                });

                // Update span data-index
                row.find('.item-amount').attr('data-index', index);

                // Calculate amount for this item
                calculateItemAmount(index);
            });

            // Recalculate totals
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        }

        function calculateItemAmount(index) {
            const row = $(`#items-tbody tr[data-index="${index}"]`);
            if (!row.length)
                return;

            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const discount = parseFloat(row.find('.item-discount').val()) || 0;

            const lineTotal = qty * price;
            const net = Math.max(0, lineTotal - discount);

            row.find('.item-amount').text(net.toFixed(2));
            row.find('.item-pr-total-price').val(net.toFixed(2));
        }

        // ---- Qty/Price/Discount Listeners ----
        let isSyncingDiscount = false;
        $(document).on('input', '.item-qty, .item-price', function () {
            const row = $(this).closest('.item-row');
            if (row.data('is-received') === true || row.data('is-received') === 'true')
                return;
            isSyncingDiscount = true;

            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const percent = parseFloat(row.find('.item-discount-percent').val()) || 0;
            const discountAmt = Math.min((percent / 100) * (qty * price), qty * price);

            row.find('.item-discount').val(discountAmt.toFixed(2));
            isSyncingDiscount = false;

            // Calculate this item's amount
            const index = row.data('index');
            calculateItemAmount(index);

            // Recalculate totals
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        });

        $(document).on('input', '.item-discount-percent', function () {
            if (isSyncingDiscount)
                return;
            const row = $(this).closest('.item-row');
            if (row.data('is-received') === true || row.data('is-received') === 'true')
                return;
            isSyncingDiscount = true;

            let percent = parseFloat($(this).val()) || 0;
            if (percent < 0)
                percent = 0;
            if (percent > 100)
                percent = 100;
            $(this).val(percent.toFixed(2));

            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            const discountAmt = (percent / 100) * (qty * price);

            row.find('.item-discount').val(discountAmt.toFixed(2));
            isSyncingDiscount = false;

            // Calculate this item's amount
            const index = row.data('index');
            calculateItemAmount(index);

            // Recalculate totals
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        });

        $(document).on('input', '.item-discount', function () {
            if (isSyncingDiscount)
                return;
            const row = $(this).closest('.item-row');
            if (row.data('is-received') === true || row.data('is-received') === 'true')
                return;
            isSyncingDiscount = true;

            let discount = parseFloat($(this).val()) || 0;
            const qty = parseFloat(row.find('.item-qty').val()) || 0;
            const price = parseFloat(row.find('.item-price').val()) || 0;
            if (discount < 0)
                discount = 0;
            if (discount > qty * price)
                discount = qty * price;

            $(this).val(discount.toFixed(2));
            const percent = (qty * price) > 0 ? (discount / (qty * price)) * 100 : 0;
            row.find('.item-discount-percent').val(percent.toFixed(2));
            isSyncingDiscount = false;

            // Calculate this item's amount
            const index = row.data('index');
            calculateItemAmount(index);

            // Recalculate totals
            if (typeof calculateTotals === 'function') {
                calculateTotals();
            }
        });
    });
</script>