<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
<style>
    .duplicate-row {
        background-color: #fff1f1 !important;
    }

    .model-cell .duplicate-warning {
        margin-top: 4px;
        font-size: 12px;
        color: red;
        font-weight: bold;
    }

    /* Style for disabled save button */
    #save-btn:disabled {
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Responsive table styling */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'validateOnSubmit' => true,
        ]);
?>

<div class="container-fluid">
    <div class="row mb-3">
        <?php if (!$isView): ?>
            <div class="col-12 col-md-6 col-lg-4 offset-md-6 offset-lg-8">
                <?=
                        $form->field($master, 'date_of_material_required')
                        ->input('date', [
                            'class' => 'form-control',
                            'required' => true,
                            'readonly' => $isView
                        ])
                        ->label('Date Of Material Required:')
                ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Responsive Table -->
<div class="table-responsive">
    <table class="table table-bordered mb-0" id="item_table">
        <thead class="table-dark">
            <tr>
                <th class="text-center" style="min-width: 50px;">No.</th>
                <th style="min-width: 120px;">Department</th>
                <th style="min-width: 120px;">Supplier</th>
                <th style="min-width: 120px;">Brand</th>
                <th style="min-width: 130px;">Model Type</th>
                <th style="min-width: 130px;">Model Group</th>
                <th style="min-width: 180px;">Item Description</th>
                <th style="min-width: 80px;">Quantity</th>
                <th style="min-width: 50px;">Unit Type</th>
                <th class="text-center" style="min-width: 100px;">Currency</th>
                <th class="text-right" style="min-width: 110px;">Unit Price</th>
                <th class="text-right" style="min-width: 110px;">Total Price</th>
                <th style="min-width: 150px;">Purpose</th>
                <th class="text-left" style="min-width: 100px;">Remark</th>
                <th style="min-width: 80px;" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody id="listTBody">
            <?php
            if (!is_array($vmodel)) {
                $vmodel = [$vmodel];
            }
            $vmodelMap = [];
            foreach ($vmodel as $v) {
                $vmodelMap[$v->item_id] = $v;
            }
            ?>
            <?php foreach ($items as $i => $item): ?>
                <?=
                $this->render('_prereq_form_row', [
                    'key' => $i,
                    'form' => $form,
                    'model' => $item,
                    'master' => $master,
                    'isUpdate' => $isUpdate,
                    'isView' => $isView,
                    'moduleIndex' => $moduleIndex,
                    'worklists' => $worklists,
                    'hasSuperiorUpdate' => $hasSuperiorUpdate,
                    'departmentList' => $departmentList,
                    'supplierList' => $supplierList,
                    'brandList' => $brandList,
                    'currencyList' => $currencyList,
                ])
                ?>
            <?php endforeach; ?>

        </tbody>

        <tfoot>
            <?php if (!$isView || $moduleIndex === 'superior'): ?>
                <tr>
                    <td colspan="14">
                        <!--<div class="container-fluid">-->
                        <div class="row">
                            <?php if ($moduleIndex === 'personal' || $moduleIndex === 'inventory'): ?>
                                <div class="col-2 col-sm-1 col-md-1 col-lg-1">
                                    <a class='btn btn-primary btn-block' href='javascript:addRow()'> 
                                        <i class="fas fa-plus-circle"></i> Add Row
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="col-1 col-sm-1 col-md-1 col-lg-1 ml-auto">
                                <?=
                                Html::submitButton('Save', [
                                    'id' => 'save-btn',
                                    'class' => 'btn btn-success btn-block'
                                ])
                                ?>
                            </div>
                        </div>
                        <!--</div>-->
                    </td>
                </tr>
            <?php endif; ?>
        </tfoot>
    </table>
</div>

<?php ActiveForm::end(); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
$firstVModel = is_array($vmodel) ? reset($vmodel) : $vmodel;
?>
<script>
    var masterId = "<?= $firstVModel && isset($firstVModel->master_id) ? $firstVModel->master_id : '' ?>";
    var currentKey = $('tr[id^="tr_"]').length;
    var moduleIndex = '<?= $moduleIndex ?>';

// Store dropdown data for reverse lookup
    var supplierList = <?= json_encode($supplierList) ?>;
    var brandList = <?= json_encode($brandList) ?>;

    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        // Initialize totals
        $("#listTBody tr").each(function () {
            const input = $(this).find('input[name*="quantity"], input[name*="unit_price"]').first();
            if (input.length) {
                updateTotal(input[0]);
            }
        });

        let currencies = currencyNum()[1];
        if (currencies) {
            currencies.forEach(currency => updateTotalAmount(currency));
        }

        // Attach event listeners for duplicate checking
        attachDuplicateCheckListeners();

        // Initial duplicate check
        setTimeout(function () {
            checkAllDuplicates();
        }, 500);
    });

// ---------------------- ROW DUPLICATE CHECK ----------------------
    function checkAllDuplicates() {
        checkFormDuplicates();

        // Then check inventory duplicates (if inventory module)
        if (moduleIndex === 'inventory') {
            // Check each row independently with a small delay to avoid race conditions
            $('#listTBody tr').each(function (index) {
                const $row = $(this);
                setTimeout(function () {
                    checkRowInventoryDuplicate($row);
                }, index * 50); // Stagger checks by 50ms
            });
        }
    }

    function attachDuplicateCheckListeners() {
        // Remove existing listeners to prevent duplicates
        $('#listTBody').off('input change', '.supplier-select, .supplier-field, .brand-select, .brand-field, .model-field, .department-select');

        // Attach listeners for form duplicate checking
        $('#listTBody').on('input change', '.supplier-select, .supplier-field, .brand-select, .brand-field, .model-field, .department-select', function () {
            const fieldName = $(this).attr('class');

            const $row = $(this).closest('tr');
            const rowIndex = $row.index();

            // Use debounce to avoid too many checks
            clearTimeout(window.duplicateCheckTimeout);
            window.duplicateCheckTimeout = setTimeout(function () {
                checkFormDuplicates();

                // Also check inventory duplicate for this specific row
                if (moduleIndex === 'inventory') {
                    checkRowInventoryDuplicate($row);
                }
            }, 300);
        });
    }

// Check for duplicate rows within the form itself
    function checkFormDuplicates() {
        console.log('--- Checking form duplicates ---');
        const rowData = [];
        const duplicateKeys = new Set();

        // First pass: collect all row data
        $('#listTBody tr').each(function (index) {
            const $row = $(this);
            const rowKey = getRowKey($row);

            if (rowKey) {
                rowData.push({
                    row: $row,
                    key: rowKey,
                    index: index
                });
            } else {
                // Only clear if not an inventory duplicate
                if (!$row.hasClass('inventory-duplicate')) {
                    $row.removeClass('duplicate-row form-duplicate');
                    $row.find('.duplicate-warning').hide();
                }
            }
        });

        // Second pass: find duplicates within form
        for (let i = 0; i < rowData.length; i++) {
            for (let j = i + 1; j < rowData.length; j++) {
                if (rowData[i].key === rowData[j].key) {
                    duplicateKeys.add(rowData[i].key);
                }
            }
        }

        // Third pass: apply styling for form duplicates
        rowData.forEach(item => {
            if (duplicateKeys.has(item.key)) {
                item.row.addClass('duplicate-row form-duplicate');
                // Only change warning text if not already showing inventory warning
                if (!item.row.hasClass('inventory-duplicate')) {
                    item.row.find('.duplicate-warning').text('⚠ Duplicate item in this form').show();
                }
            } else {
                // Only remove form-duplicate class
                item.row.removeClass('form-duplicate');
                // Only hide warning and remove duplicate-row if not inventory duplicate
                if (!item.row.hasClass('inventory-duplicate')) {
                    item.row.removeClass('duplicate-row');
                    item.row.find('.duplicate-warning').hide();
                }
            }
        });

        toggleSaveButton();
    }

// Check if items exist in inventory (for inventory module only)
    function checkRowInventoryDuplicate($row) {
        const rowIndex = $row.index();
        const department = $row.find('.department-select').val();
        const supplier = $row.find('.supplier-select').val();
        const brand = $row.find('.brand-select').val();
        const model = $row.find('.model-field').val();

        // Only check if all fields are filled
        if (!department || !supplier || !brand || !model) {
            // Clear inventory duplicate status if fields are incomplete
            $row.removeClass('inventory-duplicate');
            if (!$row.hasClass('form-duplicate')) {
                $row.removeClass('duplicate-row');
                $row.find('.duplicate-warning').hide();
            }
            toggleSaveButton();
            return;
        }

        // Trim values
        const deptVal = String(department).trim();
        const suppVal = String(supplier).trim();
        const brandVal = String(brand).trim();
        const modelVal = String(model).trim();

        if (!deptVal || !suppVal || !brandVal || !modelVal) {
            $row.removeClass('inventory-duplicate');
            if (!$row.hasClass('form-duplicate')) {
                $row.removeClass('duplicate-row');
                $row.find('.duplicate-warning').hide();
            }
            toggleSaveButton();
            return;
        }

        // Store current row reference to prevent closure issues
        const $currentRow = $row;
        const currentRowIndex = rowIndex;

        // Call backend to check inventory
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['inventory-check-duplicate']) ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                department: deptVal,
                supplier: suppVal,
                brand: brandVal,
                model: modelVal
            },
            success: function (response) {
                if (response.exists === true) {
                    $currentRow.addClass('duplicate-row inventory-duplicate');
                    $currentRow.find('.duplicate-warning').text('⚠ This item already exists in inventory').show();
                } else {
                    $currentRow.removeClass('inventory-duplicate');
                    // Only remove duplicate styling if not a form duplicate
                    if (!$currentRow.hasClass('form-duplicate')) {
                        $currentRow.removeClass('duplicate-row');
                        $currentRow.find('.duplicate-warning').hide();
                    }
                }
                toggleSaveButton();
            },
            error: function (xhr, status, error) {
                console.error('✗ Row', currentRowIndex, '- AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                console.error('Status Code:', xhr.status);
            }
        });
    }

    function getRowKey($row) {
        // Try to get department
        const department = $row.find('.department-select').val();

        // Try to get supplier (can be select or input)
        let supplier = $row.find('.supplier-select').val();
        if (!supplier) {
            supplier = $row.find('.supplier-field').val();
        }

        // Try to get brand (can be select or input)
        let brand = $row.find('.brand-select').val();
        if (!brand) {
            brand = $row.find('.brand-field').val();
        }

        // Get model (always input)
        const model = $row.find('.model-field').val();

        // Trim and check
        const deptVal = (department || '').trim();
        const suppVal = (supplier || '').trim();
        const brandVal = (brand || '').trim();
        const modelVal = (model || '').trim();

        // Return null if any required field is empty
        if (!deptVal || !suppVal || !brandVal || !modelVal) {
            return null;
        }

        // Create unique key (case insensitive)
        return `${deptVal}|${suppVal}|${brandVal}|${modelVal}`.toLowerCase();
    }

// Toggle Save button based on duplicates
    function toggleSaveButton() {
        const duplicateCount = $('.duplicate-row').length;
        const $saveBtn = $('#save-btn');

        if (duplicateCount > 0) {
            $saveBtn.prop('disabled', true)
                    .removeClass('btn-success')
                    .addClass('btn-secondary')
                    .attr('title', 'Cannot save: Duplicate items detected');
        } else {
            $saveBtn.prop('disabled', false)
                    .removeClass('btn-secondary')
                    .addClass('btn-success')
                    .removeAttr('title');
        }
    }

// ---------------------- ROW TOTALS ----------------------
    function updateTotal(input) {
        const row = $(input).closest('tr');
        const qty = parseFloat(row.find('.quantity-input').val()) || 0;
        const unit = parseFloat(row.find('.unit-price-input').val()) || 0;
        const total = (qty * unit).toFixed(2);

        row.find('.total-price-input').val(total);

        const currency = row.find('.currency-select').val();
        if (currency)
            updateTotalAmount(currency);
    }

    function updateTotalAmount(currency) {
        let total = 0;
        const table = document.getElementById('item_table');
        const totalAmountCell = document.getElementById('totalAmount-' + currency.trim());

        if (totalAmountCell) {
            if ('<?= $isView ?>') {
                totalAmountCell.textContent = "0.00";
            } else {
                totalAmountCell.value = "0.00";
            }
        }

        table.querySelectorAll("#listTBody tr").forEach(row => {
            let currencyCell = '';
            if ('<?= $isView ?>') {
                currencyCell = (row.cells[8]?.textContent || "").trim();
            } else {
                const dropdown = row.querySelector('.currency-select');
                currencyCell = dropdown ? dropdown.value.trim() : "";
            }

            if (currencyCell === currency.trim()) {
                const val = parseFloat(row.querySelector('.total-price-input')?.value || 0);
                if (!isNaN(val))
                    total += val;
            }
        });

        if (totalAmountCell) {
            if ('<?= $isView ?>') {
                totalAmountCell.textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                totalAmountCell.value = total.toFixed(2);
            }
        }
    }

// ---------------------- ADD / REMOVE ROW ----------------------
    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-form-item']) ?>',
            dataType: 'html',
            data: {
                masterId: masterId,
                key: currentKey++,
                moduleIndex: moduleIndex,
                hasSuperiorUpdate: '<?= $hasSuperiorUpdate ?>',
            },
            success: function (response) {
                console.log('New row added - current key:', currentKey - 1);
                const $newRow = $(response);
                $("#listTBody").append($newRow);

                // Update totals for new row
                const input = $newRow.find('input[name*="quantity"], input[name*="unit_price"]').first();
                if (input.length) {
                    updateTotal(input[0]);
                }

                updateRowIndices();
                currencyNum();
            },
            error: function (xhr, status, error) {
                alert('Failed to add row. Please try again.');
            }
        });
    }

    function removeRow(rowNum) {
        if (!confirm("Remove this row?"))
            return;

        $("#tr_" + rowNum).remove();
        updateRowIndices();
        currencyNum();
        checkAllDuplicates();
    }

    function updateRowIndices() {
        $('#listTBody tr:visible').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

// ---------------------- CURRENCY TOTAL ROW ----------------------
    function currencyNum() {
        const table = document.getElementById('item_table');
        let currencies = new Set();

        table.querySelectorAll("#listTBody tr").forEach(row => {
            const dropdown = row.querySelector('.currency-select');
            const currencyCell = dropdown ? dropdown.value.trim() : "";
            if (currencyCell)
                currencies.add(currencyCell);
        });

        const tfoot = document.querySelector("#item_table tfoot");
        document.querySelectorAll('#total_amount').forEach(row => row.remove());

        currencies.forEach(value => {
            if (value) {
                tfoot.prepend(createTotalAmountRow(value.trim()));
                updateTotalAmount(value.trim());
            }
        });

        return [currencies.size, currencies];
    }

    function createTotalAmountRow(currency) {
        const tr = document.createElement("tr");
        tr.id = "total_amount";

        const tdLabel = document.createElement("td");
        tdLabel.colSpan = 4;
        tdLabel.style.textAlign = "right";
        tdLabel.textContent = "Total Amount: (" + currency + ")";
        tr.appendChild(tdLabel);

        const tdValue = document.createElement("td");
        tdValue.colSpan = 9;

        if (!'<?= $isView ?>') {
            const input = document.createElement("input");
            input.type = "number";
            input.id = "totalAmount-" + currency;
            input.readOnly = true;
            input.className = "form-control";
            tdValue.appendChild(input);
        } else {
            const span = document.createElement("span");
            span.id = "totalAmount-" + currency;
            tdValue.appendChild(span);
        }

        tr.appendChild(tdValue);
        return tr;
    }

// ---------------------- PREVENT SUBMIT IF DUPLICATES ----------------------
    $('form').on('submit', function (e) {
        const duplicateCount = $('.duplicate-row').length;
        if (duplicateCount > 0) {
            e.preventDefault();
            alert('Cannot submit form: Please resolve ' + duplicateCount + ' duplicate item(s) first.');
            return false;
        }
    });

</script>
