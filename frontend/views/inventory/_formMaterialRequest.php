<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
} else if ($moduleIndex === 'personalStock') {
    $pageName = 'Material Requests - Personal';
}

$this->title = 'Add New Material Request';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => 'Material Request List', 'url' => ['material-request-list', 'type' => $moduleIndex]];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .searchable-wrapper {
        position: relative;
        width: 100%;
    }

    .searchable-wrapper .dropdown-list {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 250px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 4px 4px;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .searchable-wrapper .dropdown-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }

    .searchable-wrapper .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .searchable-wrapper .dropdown-item.selected {
        background-color: #007bff;
        color: white;
    }

    .searchable-wrapper .hidden-value {
        display: none;
    }

    .quantity-warning {
        color: #ffc107;
        font-size: 11px;
        margin-top: 2px;
    }
</style>

<div class="add-new-material-request-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row mt-3 mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex justify-content-left align-items-center">
                <h6 class="mb-0 mr-2 text-nowrap">Request For: </h6>
                <span>
                    <?=
                    $form->field($model, 'user_id')->dropDownList(
                            $staffList,
                            [
                                'prompt' => 'Select Staff',
                                'class' => 'form-control',
                                'required' => true,
                                'oninvalid' => "this.setCustomValidity('Please select a staff')",
                                'oninput' => "this.setCustomValidity('')",
                            ]
                    )->label(false);
                    ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row mt-3 mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <table class="table table-bordered mb-0" id="item_table">
                <thead class="table-dark">
                    <tr>
                        <th class="text-center" style="min-width: 50px;">No.</th>
                        <th style="min-width: 130px;">Reference Type</th>
                        <th style="min-width: 250px;">Reference ID</th>
                        <th style="min-width: 180px;">Description</th>
                        <th style="min-width: 250px;">Model Type</th>
                        <th style="min-width: 120px;">Brand</th>
                        <th style="min-width: 180px;">Item Description</th>
                        <th style="min-width: 200px;">Supplier</th>
                        <th style="min-width: 80px;">Available Qty</th>
                        <th style="min-width: 100px;">Request Qty</th>
                        <th style="min-width: 80px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="listTBody">
                    <?php if (empty($items)): ?>
                        <tr id="emptyRow">
                            <td colspan="11" class="text-center">Click "Add Row" to add items</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $index => $item): ?>
                            <tr class="item-row" data-row-id="<?= $index ?>">
                        <input type="hidden" name="RequestItem[<?= $index ?>][inventory_detail_id]" class="detail-id">
                        <td class="text-center row-number"><?= $index + 1 ?></td>
                        <td>
                            <select class="form-control reference-type-select" name="RequestItem[<?= $index ?>][reference_type]" required>
                                <option value="">Select Reference Type</option>
                                <option value="1">Project</option>
                                <option value="2">Corrective Maintenance</option>
                                <option value="3">Preventive Maintenance</option>
                                <option value="4">Others</option>
                            </select>
                        </td>
                        <td class="reference-id-cell">
                            <select class="form-control reference-id-select" name="RequestItem[<?= $index ?>][reference_id]" disabled required>
                                <option value="">Select Reference Type First</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="RequestItem[<?= $index ?>][desc]" class="form-control desc-input" readonly>
                        </td>
                        <td class="model-cell">
                            <div class="searchable-wrapper">
                                <input type="text" class="form-control model-search-input" placeholder="Type to search model..." autocomplete="off" required>
                                <div class="dropdown-list"></div>
                                <input type="hidden" class="model-id-hidden" name="RequestItem[<?= $index ?>][model_id]">
                                <div class="error-message" style="display:none;"></div>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control brand-input" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control description-input" readonly>
                        </td>
                        <td>
                            <select class="form-control supplier-select" 
                                    name="RequestItem[<?= $index ?>][supplier_id]" 
                                    disabled required>
                                <option value="">Select Model First</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control available-qty-input text-center" readonly value="0">
                        </td>
                        <td>
                            <input type="number" name="RequestItem[<?= $index ?>][request_qty]" class="form-control text-center quantity-input" min="1" required disabled>
                            <div class="quantity-warning" style="display:none;"></div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm remove-row">
                                <i class="fas fa-minus-circle"></i>
                            </button>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11">
                            <button type="button" class="btn btn-primary" onclick="addRow()">
                                Add Row <i class="fas fa-plus-circle"></i>
                            </button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<script>
    let getModelSuppliersUrl = '<?= Url::to(['get-model-suppliers']) ?>';
    let getReferenceIdsUrl = '<?= Url::to(['get-reference-ids']) ?>';
    let rowCounter = <?= count($items) ?>;

    let supplierAllocations = {};

    function initSearchableReference(container, row, referenceType, currentName) {
        let wrapper = document.createElement('div');
        wrapper.className = 'searchable-wrapper';

        let input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control reference-search-input';
        input.placeholder = 'Type to search reference ID...';
        input.required = true;
        input.autocomplete = 'off';

        let dropdown = document.createElement('div');
        dropdown.className = 'dropdown-list';

        let hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = currentName;
        hidden.className = 'reference-id-hidden';

        wrapper.appendChild(input);
        wrapper.appendChild(dropdown);
        wrapper.appendChild(hidden);

        container.innerHTML = '';
        container.appendChild(wrapper);

        let dataList = [];

        fetch(`${getReferenceIdsUrl}?referenceType=${referenceType}`)
                .then(res => res.json())
                .then(data => {
                    dataList = data;
                });

        function render(text = '') {
            dropdown.innerHTML = '';

            let filtered = dataList.filter(item =>
                (item.code + ' ' + (item.description || ''))
                        .toLowerCase()
                        .includes(text.toLowerCase())
            );

            filtered.forEach(item => {
                let div = document.createElement('div');
                div.className = 'dropdown-item';
                div.textContent = `${item.code} ${item.description || ''}`;

                div.onclick = () => {
                    input.value = item.code;
                    hidden.value = item.id;
                    input.setCustomValidity(''); // ✅ Clear validation error

                    let descInput = row.querySelector('.desc-input');
                    descInput.value = item.description || '';

                    dropdown.style.display = 'none';
                };

                dropdown.appendChild(div);
            });
        }

        input.addEventListener('focus', () => {
            render(input.value);
            dropdown.style.display = 'block';
        });

        input.addEventListener('input', () => {
            render(input.value);
            dropdown.style.display = 'block';

            // ✅ Validate that user selected from dropdown
            if (!hidden.value) {
                input.setCustomValidity('Please select a reference ID from the dropdown');
            } else {
                input.setCustomValidity('');
            }
        });

        // ✅ Add blur validation to ensure selection from dropdown
        input.addEventListener('blur', () => {
            setTimeout(() => {
                if (!hidden.value && input.value) {
                    input.setCustomValidity('Please select a reference ID from the dropdown');
                }
            }, 200); // Small delay to allow click to register
        });

        document.addEventListener('click', e => {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
    document.addEventListener('change', function (e) {

        if (!e.target.classList.contains('reference-type-select'))
            return;

        let row = e.target.closest('tr');
        let type = e.target.value;

        let cell = row.querySelector('.reference-id-cell');
        let descInput = row.querySelector('.desc-input');

        let index = row.querySelector('.row-number').textContent - 1;
        let name = `RequestItem[${index}][reference_id]`;

        descInput.value = '';

        if (!type) {
            cell.innerHTML = `
            <select class="form-control" disabled>
                <option>Select Reference Type First</option>
            </select>
        `;
            return;
        }

        // Others = manual input
        if (type == '4') {
            cell.innerHTML = `
            <input type="text" class="form-control reference-text-input" 
                   name="${name}" placeholder="Enter reference ID" required>
        `;
            descInput.readOnly = false;
            return;
        }

        // Use searchable dropdown
        descInput.readOnly = true;
        initSearchableReference(cell, row, type, name);
    });

// ================= MODEL DATA =================
    const modelDataArray = [];
<?php foreach ($modelBrandList as $combo): ?>
        modelDataArray.push({
            modelId: '<?= $combo['model_id'] ?>',
            modelName: '<?= Html::encode($combo['model_name']) ?>',
            brandName: '<?= Html::encode($combo['brand_name']) ?>',
            description: '<?= Html::encode($combo['description']) ?>',
            searchText: '<?= Html::encode($combo['model_name'] . ' ' . $combo['brand_name'] . ' ' . $combo['description']) ?>'.toLowerCase()
        });
<?php endforeach; ?>

// ================= UTILITY =================
    function getRowId(row) {
        return row.getAttribute('data-row-id') || row.querySelector('.row-number')?.textContent;
    }

    function updateSupplierAllocation(supplierId, maxAvailable) {
        if (!supplierAllocations[supplierId]) {
            supplierAllocations[supplierId] = {
                totalAllocated: 0,
                maxAvailable: maxAvailable,
                rows: {}
            };
        } else {
            supplierAllocations[supplierId].maxAvailable = maxAvailable;
        }
    }

    function recalculateSupplierAllocation(supplierId) {
        if (!supplierAllocations[supplierId])
            return 0;

        let total = 0;
        for (let rowId in supplierAllocations[supplierId].rows) {
            total += supplierAllocations[supplierId].rows[rowId];
        }
        supplierAllocations[supplierId].totalAllocated = total;
        return total;
    }

    function getRemainingQuantity(supplierId) {
        if (!supplierAllocations[supplierId])
            return 0;
        return supplierAllocations[supplierId].maxAvailable - supplierAllocations[supplierId].totalAllocated;
    }

    function updateAllSupplierDropdowns() {
        document.querySelectorAll('.supplier-select').forEach(select => {
            if (select.disabled)
                return;

            let currentValue = select.value;

            Array.from(select.options).forEach(option => {
                let supplierId = option.value;
                if (!supplierId || !supplierAllocations[supplierId])
                    return;

                let original = supplierAllocations[supplierId].maxAvailable;
                let remaining = getRemainingQuantity(supplierId);

                let name = option.getAttribute('data-supplier-name');
                option.textContent = `${name} (Available: ${remaining}/${original})`;

                option.disabled = remaining <= 0 && supplierId !== currentValue;
            });

            select.value = currentValue;
        });
    }

// ================= MODEL SEARCH =================
    function initSearchableModel(inputElement) {
        let wrapper = inputElement.closest('.searchable-wrapper');
        let dropdownList = wrapper.querySelector('.dropdown-list');
        let hiddenInput = wrapper.querySelector('.model-id-hidden');
        let row = inputElement.closest('tr');

        let brandInput = row.querySelector('.brand-input');
        let descInput = row.querySelector('.description-input');
        let supplierSelect = row.querySelector('.supplier-select');
        let availableQtyInput = row.querySelector('.available-qty-input');
        let quantityInput = row.querySelector('.quantity-input');

        function renderOptions(text = '') {
            dropdownList.innerHTML = '';

            let filtered = modelDataArray.filter(item =>
                item.searchText.includes(text.toLowerCase())
            );

            filtered.forEach(item => {
                let div = document.createElement('div');
                div.className = 'dropdown-item';
                div.textContent = `${item.modelName} - ${item.brandName}`;

                div.onclick = () => {
                    inputElement.value = div.textContent;
                    hiddenInput.value = item.modelId;
                    brandInput.value = item.brandName;
                    descInput.value = item.description;
                    dropdownList.style.display = 'none';

                    loadSuppliers(item.modelId, supplierSelect, availableQtyInput, quantityInput, row);
                };

                dropdownList.appendChild(div);
            });
        }

        inputElement.addEventListener('focus', () => {
            renderOptions(inputElement.value);
            dropdownList.style.display = 'block';
        });

        inputElement.addEventListener('input', () => {
            renderOptions(inputElement.value);
            dropdownList.style.display = 'block';
        });

        document.addEventListener('click', e => {
            if (!wrapper.contains(e.target)) {
                dropdownList.style.display = 'none';
            }
        });
    }

// ================= LOAD SUPPLIERS =================
function loadSuppliers(modelId, supplierSelect, availableQtyInput, quantityInput, row) {
    
    supplierSelect.innerHTML = '<option>Loading...</option>';
    supplierSelect.disabled = true;
    
    fetch(`${getModelSuppliersUrl}?modelId=${modelId}`)
        .then(res => res.json())
        .then(data => {
            
            supplierSelect.innerHTML = '<option value="">Select Supplier</option>';
            
            data.forEach(supplier => {
                let option = document.createElement('option');
                option.value = supplier.id; // This should be inventory_detail_id, not supplier_id!
                
                let qty = supplier.available_qty || supplier.qty || 0;
                
                updateSupplierAllocation(supplier.id, qty);
                
                let remaining = getRemainingQuantity(supplier.id);
                
                option.textContent = `${supplier.supplier_name} (Available: ${remaining}/${qty})`;
                option.setAttribute('data-original-qty', qty);
                option.setAttribute('data-supplier-name', supplier.supplier_name);
                
                supplierSelect.appendChild(option);
            });
            
            supplierSelect.disabled = false;
        });
}

// ================= SUPPLIER CHANGE =================
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('supplier-select'))
        return;
    
    let row = e.target.closest('tr');
    let option = e.target.selectedOptions[0];
    let inventoryDetailId = option.value; // ✅ This is actually the inventory_detail_id
    
    let quantityInput = row.querySelector('.quantity-input');
    let availableInput = row.querySelector('.available-qty-input');
    let detailIdInput = row.querySelector('.detail-id'); // ✅ Get the hidden input
    
    let oldSupplier = row.getAttribute('data-current-supplier');
    if (oldSupplier && supplierAllocations[oldSupplier]) {
        delete supplierAllocations[oldSupplier].rows[getRowId(row)];
        recalculateSupplierAllocation(oldSupplier);
    }
    
    if (!inventoryDetailId) {
        quantityInput.disabled = true;
        quantityInput.value = '';
        availableInput.value = 0;
        detailIdInput.value = ''; // ✅ Clear inventory_detail_id
        updateAllSupplierDropdowns();
        return;
    }
    
    let originalQty = parseInt(option.getAttribute('data-original-qty'));
    
    row.setAttribute('data-current-supplier', inventoryDetailId);
    
    supplierAllocations[inventoryDetailId].rows[getRowId(row)] = 0;
    
    let remaining = getRemainingQuantity(inventoryDetailId);
    
    availableInput.value = remaining;
    detailIdInput.value = inventoryDetailId; // ✅ Set inventory_detail_id from supplier select value
    
    quantityInput.disabled = false;
    quantityInput.max = remaining;
    
    updateAllSupplierDropdowns();
});

// ================= QUANTITY =================
    document.addEventListener('input', function (e) {
        if (!e.target.classList.contains('quantity-input'))
            return;

        let row = e.target.closest('tr');
        let supplierId = row.getAttribute('data-current-supplier');
        if (!supplierId)
            return;

        let qty = parseInt(e.target.value) || 0;
        let rowId = getRowId(row);

        let otherTotal = 0;

        for (let id in supplierAllocations[supplierId].rows) {
            if (id !== rowId) {
                otherTotal += supplierAllocations[supplierId].rows[id];
            }
        }

        let max = supplierAllocations[supplierId].maxAvailable - otherTotal;

        if (qty > max) {
            qty = max;
            e.target.value = max;
        }

        supplierAllocations[supplierId].rows[rowId] = qty;
        recalculateSupplierAllocation(supplierId);

        updateAllSupplierDropdowns();
    });

// ================= ADD ROW =================
    function addRow() {
        let tbody = document.getElementById('listTBody');

        // remove empty row if exists
        let emptyRow = document.getElementById('emptyRow');
        if (emptyRow)
            emptyRow.remove();

        let index = rowCounter; // ✅ IMPORTANT
        let id = Date.now() + '_' + index;

        let row = document.createElement('tr');
        row.className = 'item-row';
        row.setAttribute('data-row-id', id);

        row.innerHTML = `
        <input type="hidden" name="RequestItem[${index}][inventory_detail_id]" class="detail-id">

        <td class="text-center row-number">${index + 1}</td>

        <td>
            <select class="form-control reference-type-select" name="RequestItem[${index}][reference_type]" required>
                <option value="">Select Reference Type</option>
                <option value="1">Project</option>
                <option value="2">Corrective Maintenance</option>
                <option value="3">Preventive Maintenance</option>
                <option value="4">Others</option>
            </select>
        </td>

        <td class="reference-id-cell">
            <select class="form-control reference-id-select" name="RequestItem[${index}][reference_id]" disabled required>
                <option value="">Select Reference Type First</option>
            </select>
        </td>

        <td>
            <input type="text" name="RequestItem[${index}][desc]" 
                   class="form-control desc-input" readonly>
        </td>

        <td class="model-cell">
            <div class="searchable-wrapper">
                <input type="text" class="form-control model-search-input" 
                       placeholder="Type to search model..." autocomplete="off" required>
                <div class="dropdown-list"></div>
                <input type="hidden" class="model-id-hidden" 
                       name="RequestItem[${index}][model_id]">
                <div class="error-message" style="display:none;"></div>
            </div>
        </td>

        <td>
            <input type="text" class="form-control brand-input" readonly>
        </td>

        <td>
            <input type="text" class="form-control description-input" readonly>
        </td>

        <td>
    <select class="form-control supplier-select" 
            name="RequestItem[${index}][supplier_id]" 
            disabled required>
        <option value="">Select Model First</option>
    </select>
</td>

        <td>
            <input type="text" class="form-control available-qty-input text-center" readonly value="0">
        </td>

        <td>
            <input type="number" name="RequestItem[${index}][request_qty]" 
                   class="form-control text-center quantity-input" 
                   min="1" required disabled>
            <div class="quantity-warning" style="display:none;"></div>
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm remove-row">
                <i class="fas fa-minus-circle"></i>
            </button>
        </td>
    `;

        tbody.appendChild(row);

        // ✅ init model search
        initSearchableModel(row.querySelector('.model-search-input'));

        // ✅ increase counter
        rowCounter++;

        // ✅ fix numbering & names
        updateRowNumbers();
    }

// ================= REMOVE ROW =================
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.remove-row'))
            return;

        let row = e.target.closest('tr');
        let supplierId = row.getAttribute('data-current-supplier');

        if (supplierId && supplierAllocations[supplierId]) {
            delete supplierAllocations[supplierId].rows[getRowId(row)];
            recalculateSupplierAllocation(supplierId);
        }

        row.remove();
        updateRowNumbers();
        updateAllSupplierDropdowns();
    });

// ================= ROW NUMBER =================
    function updateRowNumbers() {
        document.querySelectorAll('#listTBody tr').forEach((row, i) => {
            let cell = row.querySelector('.row-number');
            if (cell)
                cell.textContent = i + 1;
        });
    }

// ================= VALIDATION =================
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.add-new-material-request-form form');

        if (form) {
            form.addEventListener('submit', function (e) {
                let hasErrors = false;
                let messages = [];
                let rows = document.querySelectorAll('#listTBody .item-row');

                if (rows.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item');
                    return false;
                }

                rows.forEach((row, i) => {

                    let type = row.querySelector('.reference-type-select')?.value;
                    let desc = row.querySelector('.desc-input')?.value;

                    let modelId = row.querySelector('.model-id-hidden')?.value;
                    let supplierId = row.querySelector('.supplier-select')?.value;
                    let quantity = row.querySelector('.quantity-input')?.value;

                    let referenceId = '';
                    let referenceWrapper = row.querySelector('.reference-id-cell .searchable-wrapper');
                    if (referenceWrapper) {
                        let hidden = referenceWrapper.querySelector('.reference-id-hidden');
                        referenceId = hidden ? hidden.value : '';
                    }

                    let referenceTextInput = row.querySelector('.reference-text-input');

                    if (!type) {
                        hasErrors = true;
                        messages.push(`Row ${i + 1}: Please select Reference Type`);
                    }

                    if (type == '1' || type == '2' || type == '3') {
                        if (!referenceId) {
                            hasErrors = true;
                            messages.push(`Row ${i + 1}: Please select a reference ID`);
                        }
                    }

                    if (type == '4') {
                        if (!referenceTextInput || !referenceTextInput.value) {
                            hasErrors = true;
                            messages.push(`Row ${i + 1}: Please enter reference ID`);
                        }

                        if (!desc) {
                            hasErrors = true;
                            messages.push(`Row ${i + 1}: Description is required`);
                        }
                    }

                    if (!modelId) {
                        hasErrors = true;
                        messages.push(`Row ${i + 1}: Please select a Model`);
                    }

                    if (!supplierId) {
                        hasErrors = true;
                        messages.push(`Row ${i + 1}: Please select a Supplier`);
                    }

                    if (!quantity || quantity <= 0) {
                        hasErrors = true;
                        messages.push(`Row ${i + 1}: Please enter valid quantity`);
                    }

                });

                if (hasErrors) {
                    e.preventDefault();
                    alert('Please fix the following errors:\n\n' + messages.join('\n'));
                    return false;
                }

                // If no errors, form will submit normally
            });
        }
    });

// ================= INIT =================
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.model-search-input').forEach(initSearchableModel);
    });
</script>