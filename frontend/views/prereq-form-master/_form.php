<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\VPrereqFormMasterDetail */
/* @var $form yii\widgets\ActiveForm */
?>
<meta name="csrf-token" content="<?= Yii::$app->request->csrfToken ?>">
<style>
    .date-of-material-required-row{
        display: block;
        margin-left: 80%;
    }
</style>

<?php // $form = ActiveForm::begin(); ?>
<?php
$form = ActiveForm::begin([
//    'id' => 'your-form-id',
    'enableClientValidation' => false, // Temporarily disable
    'validateOnSubmit' => true,
        ]);
?>
<!--<div class="form-row m-3">-->
<div class="date-of-material-required-row">
    <?php if (!$isView): ?>
        <?=
                $form->field($master, 'date_of_material_required')
                ->input('date', [
                    'class' => 'form-control',
                    'required' => true,
                    'readonly' => $isView
                ])
                ->label('Date Of Material Required:')
        ?>
    <?php endif; ?>
</div>
<table class="table table-bordered mb-0" id="item_table">
    <thead class="table-dark">
        <tr>
            <th class="text-center" width="3%">No.</th>
            <th width="8%">Department</th>
            <th width="8%">Supplier</th>
            <th width="8%">Brand</th>
            <th width="8%">Model Type</th>
            <th width="18%">Item Description</th>
            <th width="5%">Quantity</th>
            <th class="text-center" width="6%">Currency</th>
            <th class="text-right" width="8%">Unit Price</th>
            <th class="text-right" width="8%">Total Price</th>
            <th width="15%">Purpose</th>
            <th class="text-left" width="5%">Remark</th>
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
            $this->render('_form_row', [
                'key' => $i,
                'form' => $form,
                'model' => $vmodelMap[$item->id] ?? $item,
                'master' => $master,
                'isUpdate' => $isUpdate,
                'isView' => $isView,
                'moduleIndex' => $moduleIndex,
                'worklists' => $worklists,
                'hasSuperiorUpdate' => $hasSuperiorUpdate,
                'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist(),
                'departmentList' => $departmentList,
//                'supplierList' => $supplierList,
//                'brandList' => $brandList,
//                'modelList' => $modelList
            ])
            ?>
        <?php endforeach; ?>
    </tbody>

    <tfoot>
        <?php if (!$isView || $moduleIndex === 'superior'): ?>
            <tr>
                <td colspan="12">      
                    <?php if ($moduleIndex === 'personal'): ?>
                        <a class='btn btn-primary' href='javascript:addRow()'> 
                            <i class="fas fa-plus-circle"></i></a>
                    <?php endif; ?>
                    <?=
                    Html::submitButton('Save', [
                        'id' => 'save-btn',
                        'class' => 'float-right btn btn-success'
                    ])
                    ?>
                </td>
            </tr>
        </tfoot>
    <?php endif; ?>
</table>
<?php ActiveForm::end(); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php
$firstVModel = is_array($vmodel) ? reset($vmodel) : $vmodel;
?>
<script>
    var masterId = "<?=
$firstVModel && isset($firstVModel->master_id) ?
        $firstVModel->master_id : ''
?>";
    var currentKey = $('tr[id^="tr_"]').length; // counts existing rows on load

    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        $("#listTBody tr").each(function () {
            const input = $(this).find('input[name*="quantity"], input[name*="unit_price"]').first();
            updateTotal(input);
        });

        // then update the total amounts by currency
        let currencies = currencyNum()[1];
        currencies.forEach(currency => updateTotalAmount(currency));
    });

    function removeRow(rowNum) {
        let ans = confirm("Remove row?");
        if (ans) {
            $("#tr_" + rowNum).remove();
            $(".functionality-input").removeAttr("required");
            $("#toDelete-" + rowNum).val("1");
            $("#tr_" + rowNum + " .functionality-input").removeAttr("required");
            let currencies = currencyNum()[1];
            currencies.forEach(function (currency) {
                updateTotalAmount(currency.trim());
            });
            updateRowIndices();

            let num = currencyNum()[0];
        }
    }

    function addRow() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['ajax-add-form-item']) ?>',
            dataType: 'html',
            data: {
                masterId: masterId,
                key: currentKey++,
                moduleIndex: '<?= $moduleIndex ?>',
                hasSuperiorUpdate: '<?= $hasSuperiorUpdate ?>',
            }
        }).done(function (response) {
            const $newRow = $(response);
            $("#listTBody").append($newRow);

            const input = $newRow.find('input[name*="quantity"], input[name*="unit_price"]').first();
            updateTotal(input);
            updateRowIndices();   // added this function for proper updates on row indices
            let num = currencyNum()[0];
        });
    }

    function markDelete(itemID, rowKey) {
        if (!itemID || itemID === 'null') {
            if (rowKey !== 0) {
                removeRow(rowKey);
            }
            return;
        }

        let ans = confirm('Are you sure you want to delete this item?');
        if (ans) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['ajax-delete-item']) ?>?id=' + itemID,
                method: 'POST',
                data: {id: itemID},
//            headers: {
//                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
//            },
                success: function (response) {
                    if (response.success) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            $("#tr_" + rowKey).remove();
                            let currencies = currencyNum()[1];
                            currencies.forEach(function (currency) {
                                updateTotalAmount(currency);
                            });
                            updateTotal();
                            updateRowIndices();
                        }
                    } else {
                        alert("Failed to delete item: " + (response.error || "Unknown error"));
                    }
                },
                error: function () {
                    alert("Server error while deleting item.");
                }
            });
        }
    }

    function updateTotal(input) {
        const row = $(input).closest('tr');
        const qty = parseFloat(row.find('input[name*="quantity"]').val()) || 0;
        const unit = parseFloat(row.find('input[name*="unit_price"]').val()) || 0;
        row.find('input[name*="total_price"]').val((qty * unit).toFixed(2));

        const currencySelect = row.find('select[id*="currency"]');
        const currency = currencySelect.val();

        if (currency)
            updateTotalAmount(currency);
    }

    function updateTotalAmount(currency) {
        let total = 0;
        const table = document.getElementById('item_table');

        const totalAmountCell = document.getElementById('totalAmount-' + currency.trim());
        if (totalAmountCell) {
            if ('<?= $isView ?>')
                totalAmountCell.textContent = "0.00";
            else
                totalAmountCell.value = "0.00";
        }

        table.querySelectorAll("#listTBody tr").forEach(row => {
            let currencyCell;

            if ('<?= $isView ?>')
                currencyCell = (row.cells[7]?.textContent || "").trim();
            else {
                const dropdown = row.cells[7]?.querySelector('select');
                currencyCell = dropdown ? dropdown.value.trim() : "";
            }

            if (currencyCell === currency.trim()) {
                if ('<?= $isView ?>') {
                    const amountCell = row.cells[9];
                    const amount = parseFloat(amountCell.textContent.replace(/,/g, ""));
                    if (!isNaN(amount))
                        total += amount;
                } else {
                    const input = row.cells[9]?.querySelector('input');
                    const val = parseFloat(input?.value) || 0;
                    if (!isNaN(val))
                        total += val;
                }
            }
        });

        if (totalAmountCell) {
            if ('<?= $isView ?>') {
                totalAmountCell.textContent = total.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            } else
                totalAmountCell.value = total.toFixed(2);
        }
    }

    function updateRowIndices() {
        $('#listTBody tr:visible').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

    function addRejectTextArea() {
        document.addEventListener('DOMContentLoaded', function () {
            const rejectBtn = document.querySelector('.reject-btn');
            const approveBtn = document.querySelector('.approve-btn');
            const textareaDiv = document.getElementById('reject-textarea');

            rejectBtn.addEventListener('click', function () {
                textareaDiv.style.display = 'block';
            });

            approveBtn.addEventListener('click', function () {
                textareaDiv.style.display = 'none';
            });
        });
    }

    if ('<?= $moduleIndex ?>' === 'superior')
        addRejectTextArea();

    function currencyNum() {
        const table = document.getElementById('item_table');
        let diffCurrencies = 0;
        var currencies = new Set();
        let defaultCurrency = "";

        table.querySelectorAll("#listTBody tr").forEach(row => {
            let currencyCell;

            if ('<?= $isView ?>')
                currencyCell = (row.cells[7]?.textContent || "").trim();
            else {
                const dropdown = row.cells[7]?.querySelector('select');
                currencyCell = dropdown ? dropdown.value.trim() : "";
            }

            if (currencyCell !== defaultCurrency) {
                diffCurrencies++;
                defaultCurrency = currencyCell;
                currencies.add(currencyCell);
            }
        });
        const tfoot = document.querySelector("#item_table tfoot");
        document.querySelectorAll('#total_amount').forEach(row => row.remove());
        currencies.forEach(value => {
            tfoot.prepend(createTotalAmountRow(value.trim()));
            updateTotal();
            updateTotalAmount(value.trim());
        });

        return [diffCurrencies, currencies];
    }

    document.getElementById('item_table').addEventListener('change', function (e) {
        if (e.target && e.target.matches('select[id*="currency"]')) {
            let currencies = currencyNum()[1];
            const tfoot = document.querySelector("#item_table tfoot");
            document.querySelectorAll('#total_amount').forEach(row => row.remove());
            currencies.forEach(value => {
                tfoot.prepend(createTotalAmountRow(value.trim()));
                updateTotal();
                updateTotalAmount(value.trim());
            });
        }
    });

    function createTotalAmountRow(currency) {
        currency = currency.trim();
        const tr = document.createElement("tr");
        tr.id = "total_amount";

        const tdLabel = document.createElement("td");
        tdLabel.colSpan = 4;
        tdLabel.style.textAlign = "right";
        tdLabel.textContent = "Total Amount: (" + currency + ")";
        tr.appendChild(tdLabel);

        const tdValue = document.createElement("td");
        tdValue.colSpan = 8;

        if (!'<?= $isView ?>') {
            const input = document.createElement("input");
            input.type = "number";
            input.id = "totalAmount-" + currency.trim();
            input.readOnly = true;
            tdValue.appendChild(input);
        } else {
            const span = document.createElement("span");
            span.id = "totalAmount-" + currency.trim();
            tdValue.appendChild(span);
        }

        tr.appendChild(tdValue);

        return tr;
    }

</script>
<script>
//    var supplierListData = <?php //= json_encode($supplierList) ?>;
//    var brandListData = <?php //= json_encode($brandList) ?>;
//    var modelListData = <?php //= json_encode($modelList) ?>;

    function initializeDepartmentChange() {
        console.log('Initializing department change');

        // Initialize on page load for existing rows
        $('select[name*="[department_code]"]').each(function () {
            handleDepartmentChange($(this));
        });

        // Bind change event
        $(document).on('change', 'select[name*="[department_code]"]', function () {
            handleDepartmentChange($(this));
        });
    }

//    function handleDepartmentChange($departmentSelect) {
//        var $row = $departmentSelect.closest('tr');
//        var rowIndex = $row.data('index');
//        var selectedDept = $departmentSelect.val();
//        var deptText = $departmentSelect.find('option:selected').text().trim().toLowerCase();
//
//        var $supplierCell = $row.find('.supplier-cell');
//        var $brandCell = $row.find('.brand-cell');
//        var $modelCell = $row.find('.model-cell');
//
//        console.log('Department selected:', selectedDept, deptText);
//
//        if (!selectedDept) {
//            console.log('No department selected, converting to text inputs');
//            convertToTextInput($supplierCell, 'supplier', rowIndex);
//            convertToTextInput($brandCell, 'brand', rowIndex);
//            convertToTextInput($modelCell, 'model', rowIndex);
//            clearInventoryId($row);
//            return;
//        }
//
//        if (deptText === 'maintenance department' || deptText === 'maintenance') {
//            console.log('Maintenance department - fetching filtered inventory');
//
//            // Fetch inventory items filtered by department
//            $.ajax({
//                url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-suppliers"]) ?>',
//                type: 'GET',
//                data: {department_code: selectedDept},
//                dataType: 'json',
//                success: function (suppliers) {
//                    console.log('Suppliers for department received:', suppliers);
//
//                    // Convert to dropdowns with department-filtered data
//                    convertToDropdown($supplierCell, 'supplier', rowIndex, suppliers);
//
//                    // Fetch brands for this department
//                    $.ajax({
//                        url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-brands"]) ?>',
//                        type: 'GET',
//                        data: {department_code: selectedDept},
//                        dataType: 'json',
//                        success: function (brands) {
//                            console.log('Brands for department received:', brands);
//                            convertToDropdown($brandCell, 'brand', rowIndex, brands);
//
//                            // Fetch models for this department
//                            $.ajax({
//                                url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-models"]) ?>',
//                                type: 'GET',
//                                data: {department_code: selectedDept},
//                                dataType: 'json',
//                                success: function (models) {
//                                    console.log('Models for department received:', models);
//                                    convertToDropdown($modelCell, 'model', rowIndex, models);
//                                },
//                                error: function (xhr, status, error) {
//                                    console.error('Error fetching models:', error);
//                                    convertToDropdown($modelCell, 'model', rowIndex, modelListData);
//                                }
//                            });
//                        },
//                        error: function (xhr, status, error) {
//                            console.error('Error fetching brands:', error);
//                            convertToDropdown($brandCell, 'brand', rowIndex, brandListData);
//                        }
//                    });
//                },
//                error: function (xhr, status, error) {
//                    console.error('Error fetching suppliers:', error);
//                    convertToDropdown($supplierCell, 'supplier', rowIndex, supplierListData);
//                }
//            });
//
//        } else {
//            console.log('Non-maintenance department, converting to text inputs');
//            convertToTextInput($supplierCell, 'supplier', rowIndex);
//            convertToTextInput($brandCell, 'brand', rowIndex);
//            convertToTextInput($modelCell, 'model', rowIndex);
//            clearInventoryId($row);
//        }
//    }

    function convertToDropdown($cell, type, rowIndex, dataList) {
        var $field = $cell.find('input, select').first();

        if ($field.length === 0) {
            console.error('Field not found for:', type);
            return;
        }

        if ($field.prop('tagName') === 'SELECT') {
            console.log('Already a dropdown:', type);
            // Update options even if already dropdown
            var $select = $field;
            var currentValue = $select.val();

            $select.empty();
            $select.append('<option value="">Select ' + type.charAt(0).toUpperCase() + type.slice(1) + '</option>');

            $.each(dataList, function (key, value) {
                var selected = (key == currentValue) ? 'selected' : '';
                $select.append('<option value="' + key + '" ' + selected + '>' + value + '</option>');
            });

            return;
        }

        var currentValue = $field.val();
        var fieldName = $field.attr('name');
        var fieldId = $field.attr('id');

        console.log('Converting to dropdown:', type, 'Field name:', fieldName);

        // Create select element
        var $select = $('<select>', {
            name: fieldName,
            id: fieldId,
            class: 'form-control ' + type + '-select'
        });

        $select.append('<option value="">Select ' + type.charAt(0).toUpperCase() + type.slice(1) + '</option>');

        // Add options from dataList
        $.each(dataList, function (key, value) {
            var selected = (value === currentValue) ? 'selected' : '';
            $select.append('<option value="' + key + '" ' + selected + '>' + value + '</option>');
        });

        // Replace the input
        $field.replaceWith($select);

        console.log('Dropdown created for:', type, 'with', Object.keys(dataList).length, 'options');

        // Rebind events
        if (type === 'supplier') {
            $select.on('change', function () {
                console.log('Supplier changed to:', $(this).val());
                handleSupplierChange($(this));
            });
        } else if (type === 'brand') {
            $select.on('change', function () {
                console.log('Brand changed to:', $(this).val());
                handleBrandChange($(this));
            });
        } else if (type === 'model') {
            $select.on('change', function () {
                console.log('Model changed to:', $(this).val());
                setInventoryId($(this).closest('tr'));
            });
        }
    }

    function convertToTextInput($cell, type, rowIndex) {
        var $field = $cell.find('input, select').first();

        if ($field.length === 0) {
            console.error('Field not found for:', type);
            return;
        }

        if ($field.prop('tagName') === 'INPUT') {
            console.log('Already a text input:', type);
            return;
        }

        var currentValue = $field.find('option:selected').text();
        if (currentValue.includes('Select')) {
            currentValue = '';
        }
        var fieldName = $field.attr('name');
        var fieldId = $field.attr('id');

        // Create input element
        var $input = $('<input>', {
            type: 'text',
            name: fieldName,
            id: fieldId,
            class: 'form-control ' + type + '-field',
            value: currentValue
        });

        // Replace select with input
        $field.replaceWith($input);

        console.log('Text input created for:', type);
    }

    function handleSupplierChange($supplierSelect) {
        var $row = $supplierSelect.closest('tr');
        var supplierId = $supplierSelect.val();
        var departmentCode = $row.find('.department-select').val();

        console.log('Supplier change handler, ID:', supplierId, 'Department:', departmentCode);

        if (!supplierId) {
            // Reset brand and model based on department
            var $brandCell = $row.find('.brand-cell');
            var $modelCell = $row.find('.model-cell');

            if (departmentCode) {
                // Fetch brands for department only
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-brands"]) ?>',
                    type: 'GET',
                    data: {department_code: departmentCode},
                    dataType: 'json',
                    success: function (brands) {
                        convertToDropdown($brandCell, 'brand', $row.data('index'), brands);
                    }
                });

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-models"]) ?>',
                    type: 'GET',
                    data: {department_code: departmentCode},
                    dataType: 'json',
                    success: function (models) {
                        convertToDropdown($modelCell, 'model', $row.data('index'), models);
                    }
                });
            }

            clearInventoryId($row);
            return;
        }

        var $brandCell = $row.find('.brand-cell');
        var $modelCell = $row.find('.model-cell');

        // Fetch brands for selected department and supplier
        $.ajax({
            url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-brands"]) ?>',
            type: 'GET',
            data: {
                department_code: departmentCode,
                supplier_id: supplierId
            },
            dataType: 'json',
            success: function (data) {
                console.log('Brands received:', data);

                var $brandSelect = $brandCell.find('select');
                $brandSelect.empty();
                $brandSelect.append('<option value="">Select Brand</option>');

                $.each(data, function (key, value) {
                    $brandSelect.append('<option value="' + key + '">' + value + '</option>');
                });

                // Reset model based on department
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-models"]) ?>',
                    type: 'GET',
                    data: {department_code: departmentCode},
                    dataType: 'json',
                    success: function (models) {
                        var $modelSelect = $modelCell.find('select');
                        $modelSelect.empty();
                        $modelSelect.append('<option value="">Select Model</option>');
                        $.each(models, function (key, value) {
                            $modelSelect.append('<option value="' + key + '">' + value + '</option>');
                        });
                    }
                });

                clearInventoryId($row);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching brands:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    function handleBrandChange($brandSelect) {
        var $row = $brandSelect.closest('tr');
        var brandId = $brandSelect.val();
        var supplierId = $row.find('.supplier-select').val();
        var departmentCode = $row.find('.department-select').val();

        console.log('Brand change handler - Brand:', brandId, 'Supplier:', supplierId, 'Department:', departmentCode);

        if (!brandId) {
            // Reset model based on department and supplier
            var $modelCell = $row.find('.model-cell');

            $.ajax({
                url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-models"]) ?>',
                type: 'GET',
                data: {
                    department_code: departmentCode,
                    supplier_id: supplierId
                },
                dataType: 'json',
                success: function (models) {
                    convertToDropdown($modelCell, 'model', $row.data('index'), models);
                }
            });

            clearInventoryId($row);
            return;
        }

        if (!supplierId) {
            alert('Please select a supplier first');
            $brandSelect.val('');
            return;
        }

        var $modelCell = $row.find('.model-cell');

        // Fetch models for selected department, supplier and brand
        $.ajax({
            url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-models"]) ?>',
            type: 'GET',
            data: {
                department_code: departmentCode,
                supplier_id: supplierId,
                brand_id: brandId
            },
            dataType: 'json',
            success: function (data) {
                console.log('Models received:', data);

                var $modelSelect = $modelCell.find('select');
                $modelSelect.empty();
                $modelSelect.append('<option value="">Select Model</option>');

                $.each(data, function (key, value) {
                    $modelSelect.append('<option value="' + key + '">' + value + '</option>');
                });

                clearInventoryId($row);
            },
            error: function (xhr, status, error) {
                console.error('Error fetching models:', error);
                console.error('Response:', xhr.responseText);
            }
        });
    }

    function setInventoryId($row) {
        var departmentCode = $row.find('.department-select').val();
        var supplierId = $row.find('.supplier-select').val();
        var brandId = $row.find('.brand-select').val();
        var modelId = $row.find('.model-select').val();

        console.log('Setting inventory ID - Department:', departmentCode, 'Supplier:', supplierId, 'Brand:', brandId, 'Model:', modelId);

        if (!departmentCode || !supplierId || !brandId || !modelId) {
            console.log('Missing required values for inventory ID');
            clearInventoryId($row);
            return;
        }

        var $inventoryField = $row.find('input[name*="[inventory_id]"]');
        console.log('Inventory field found:', $inventoryField.length);

        // Fetch inventory_id
        $.ajax({
            url: '<?= \yii\helpers\Url::to(["/office/prereq-form-master/get-inventory-id"]) ?>',
            type: 'GET',
            data: {
                department_code: departmentCode,
                supplier_id: supplierId,
                brand_id: brandId,
                model_id: modelId
            },
            dataType: 'json',
            success: function (data) {
                console.log('Inventory ID response:', data);

                if (data.success && data.inventory_id) {
                    $inventoryField.val(data.inventory_id);
                    console.log('✓ Inventory ID set successfully:', data.inventory_id);

                    showInventoryIdFeedback($row, 'success', 'Inventory ID: ' + data.inventory_id);
                } else {
                    console.warn('Inventory not found for the selected combination');
                    $inventoryField.val('');
                    showInventoryIdFeedback($row, 'warning', 'No inventory found');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching inventory ID:', error);
                console.error('Response:', xhr.responseText);
                clearInventoryId($row);
                showInventoryIdFeedback($row, 'error', 'Error fetching inventory');
            }
        });
    }

    function clearInventoryId($row) {
        var $inventoryField = $row.find('input[name*="[inventory_id]"]');
        $inventoryField.val('');
        console.log('Inventory ID cleared');
    }

    function showInventoryIdFeedback($row, type, message) {
        var $modelCell = $row.find('.model-cell');
        var $feedback = $modelCell.find('.inventory-feedback');

        if ($feedback.length === 0) {
            $feedback = $('<small class="inventory-feedback" style="display:block; margin-top:5px;"></small>');
            $modelCell.append($feedback);
        }

        $feedback.removeClass('text-success text-warning text-danger');

        if (type === 'success') {
            $feedback.addClass('text-success');
        } else if (type === 'warning') {
            $feedback.addClass('text-warning');
        } else {
            $feedback.addClass('text-danger');
        }

        $feedback.text(message).fadeIn();

        setTimeout(function () {
            $feedback.fadeOut();
        }, 3000);
    }

    // Initialize on document ready
    $(document).ready(function () {
        initializeDepartmentChange();
    });
</script>