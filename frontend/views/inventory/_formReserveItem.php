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
}

$this->title = 'Add New Reserve Item';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => 'Reserved Items', 'url' => ['reserved-item-list', 'type' => $moduleIndex]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="add-new-reserve-item-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row mt-3 mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex justify-content-left align-items-center">
                <h6 class="mb-0 mr-2 text-nowrap">Reserved For: </h6>
                <span>
                    <?=
                    $form->field($model, 'user_id')->dropDownList(
                            $staffList,
                            ['prompt' => 'Select Staff']
                    )->label(false)
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
                        <th style="min-width: 130px;">Model Type</th>
                        <th style="min-width: 120px;">Brand</th>
                        <th style="min-width: 180px;">Item Description</th>
                        <th style="min-width: 200px;">Supplier</th>
                        <th style="min-width: 50px;">Available Qty</th>
                        <th style="min-width: 50px;">Reserve Qty</th>
                        <th style="min-width: 80px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="listTBody">
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                    <input type="hidden" name="ReserveItem[<?= $index ?>][inventory_detail_id]" class="detail-id">
                    <td class="text-center">
                        <?= $index + 1 ?>
                    </td>
                    <td>
                        <select class="form-control model-select">
                            <option value="">Select Model</option>
                            <?php foreach ($modelBrandList as $combo): ?>
                                <option 
                                    value="<?= $combo['model_id'] ?>"
                                    data-brand="<?= Html::encode($combo['brand_name']) ?>"
                                    data-brand-id="<?= $combo['brand_id'] ?>"
                                    data-description="<?= Html::encode($combo['description']) ?>"
                                    >
                                        <?= Html::encode($combo['model_name'] . ' - ' . $combo['brand_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control brand-input" readonly>
                    </td>
                    <td>
                        <input type="text" class="form-control description-input" readonly>
                    </td>
                    <td>
                        <select class="form-control supplier-select" disabled>
                            <option value="">Select Model First</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control available-qty-input text-center" readonly value="0"> 
                    </td>
                    <td>
                        <?=
                        Html::input(
                                'number',
                                "ReserveItem[$index][reserved_qty]",
                                $model->reserved_qty ?? '',
                                [
                                    'class' => 'form-control text-center quantity-input',
                                    'min' => '1',
                                    'required' => true,
                                    'disabled' => true
                                ]
                        )
                        ?>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fas fa-minus-circle"></i>
                        </button>
                    </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="8">
                            <div class="row">
                                <div class="col-2 col-sm-1 col-md-1 col-lg-1">
                                    <button type="button" class="btn btn-primary btn-block" onclick="addRow()">
                                        Add Row <i class="fas fa-plus-circle"></i>
                                    </button>
                                </div>
                            </div>
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

    $(document).on('change', '.model-select', function () {
        let row = $(this).closest('tr');
        let selected = $(this).find(':selected');
        let modelId = $(this).val();

        // Get references to fields in this row
        let supplierSelect = row.find('.supplier-select');
        let availableQtyInput = row.find('.available-qty-input');
        let quantityInput = row.find('.quantity-input');
        let detailIdInput = row.find('.detail-id');
        let brandInput = row.find('.brand-input');
        let descriptionInput = row.find('.description-input');

        // Reset all fields
        supplierSelect.empty().append('<option value="">Loading suppliers...</option>');
        supplierSelect.prop('disabled', true);
        availableQtyInput.val('0');
        quantityInput.prop('disabled', true).val('');
        detailIdInput.val('');

        // Set brand and description from selected option data attributes
        let brand = selected.data('brand');
        let description = selected.data('description');
        brandInput.val(brand || '');
        descriptionInput.val(description || '');

        // If no model selected, show appropriate message and exit
        if (!modelId) {
            supplierSelect.empty().append('<option value="">Select Model First</option>');
            return;
        }

        $.ajax({
            url: getModelSuppliersUrl,
            method: 'GET',
            data: {modelId: modelId},
            dataType: 'json',
            cache: false,
            success: function (data) {
                // Clear supplier dropdown
                supplierSelect.empty();

                // Check if we have data
                if (!data || data.length === 0) {
                    supplierSelect.append('<option value="" disabled selected>No suppliers with stock</option>');
                    supplierSelect.prop('disabled', true);
                    supplierSelect.css('background-color', '#fff3cd');
                    return;
                }

                // Add default option
                supplierSelect.append('<option value="" selected disabled>-- Select a Supplier --</option>');

                // Add each supplier as an option
                $.each(data, function (index, supplier) {
                    let optionText = supplier.supplier_name;
                    if (supplier.available_qty !== undefined) {
                        optionText += ' (Available: ' + supplier.available_qty + ')';
                    }

                    let option = $('<option></option>')
                            .val(supplier.id)
                            .text(optionText)
                            .attr('data-available-qty', supplier.available_qty || 0)
                            .attr('data-supplier-id', supplier.supplier_id || '');

                    supplierSelect.append(option);
                });

                // Enable the supplier dropdown
                supplierSelect.prop('disabled', false);
                supplierSelect.css('background-color', '');
                supplierSelect.focus();
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);

                supplierSelect.empty().append('<option value="">Error loading suppliers</option>');
                supplierSelect.prop('disabled', true);

                alert('Failed to load suppliers. Please refresh the page and try again.');
            }
        });
    });

    // Handle supplier selection change
    $(document).on('change', '.supplier-select', function () {
        let row = $(this).closest('tr');
        let selectedOption = $(this).find(':selected');
        let inventoryDetailId = $(this).val();
        let modelId = row.find('.model-select').val();

        if (!inventoryDetailId) {
            return;
        }

        // Check for duplicate items (same model and supplier)
        let isDuplicate = false;
        let duplicateRow = null;
        
        $('#listTBody tr').each(function() {
            if ($(this)[0] === row[0]) return true; // Skip current row
            
            let rowModelId = $(this).find('.model-select').val();
            let rowSupplierId = $(this).find('.supplier-select').val();
            
            if (rowModelId === modelId && rowSupplierId === inventoryDetailId) {
                isDuplicate = true;
                duplicateRow = $(this);
                return false; // Break the loop
            }
        });

        if (isDuplicate) {
            alert('This item (same model and supplier) has already been added. Please choose a different supplier.');
            
            // Reset the current selection
            $(this).val('');
            row.find('.available-qty-input').val('0');
            row.find('.quantity-input').prop('disabled', true).val('');
            row.find('.detail-id').val('');
            
            // Highlight the duplicate row to show user where it exists
            duplicateRow.css('background-color', '#fff3cd');
            setTimeout(function() {
                duplicateRow.css('background-color', '');
            }, 3000);
            
            return;
        }

        // Get available quantity from data attribute
        let availableQty = selectedOption.data('available-qty') || 0;

        // Update available quantity field
        let availableQtyInput = row.find('.available-qty-input');
        availableQtyInput.val(availableQty);

        // Update hidden inventory_detail_id field
        let detailIdInput = row.find('.detail-id');
        detailIdInput.val(inventoryDetailId);

        // Enable quantity input and set attributes
        let quantityInput = row.find('.quantity-input');
        if (availableQty > 0) {
            quantityInput.prop('disabled', false);
            quantityInput.attr('max', availableQty);
            quantityInput.attr('placeholder', 'Enter quantity (max: ' + availableQty + ')');
            quantityInput.val(''); // Clear any previous value
            quantityInput.focus(); // Focus on quantity input
        } else {
            quantityInput.prop('disabled', true).val('');
            alert('This supplier has no available stock for this item');
        }
    });

    // Validate quantity input against available stock
    $(document).on('input', '.quantity-input', function () {
        let row = $(this).closest('tr');
        let availableQty = parseInt(row.find('.available-qty-input').val()) || 0;
        let requestedQty = parseInt($(this).val()) || 0;

        // Ensure requested quantity doesn't exceed available stock
        if (requestedQty > availableQty) {
            $(this).val(availableQty);
            alert('Requested quantity cannot exceed available stock (' + availableQty + ')');
        }

        // Ensure minimum quantity is at least 1
        if (requestedQty < 1 && $(this).val() !== '') {
            $(this).val(1);
        }
    });

    // Function to add a new row
    function addRow() {
        // Check if there's an incomplete row
        let hasIncompleteRow = false;
        $('#listTBody tr').each(function() {
            let modelSelected = $(this).find('.model-select').val();
            let supplierSelected = $(this).find('.supplier-select').val();
            
            // If model is selected but no supplier, or if model is empty but row exists
            if ((modelSelected && !supplierSelected) || (!modelSelected && $(this).find('.model-select').length > 0)) {
                hasIncompleteRow = true;
                return false;
            }
        });

        if (hasIncompleteRow) {
            alert('Please complete the current row before adding a new one.');
            return;
        }

        let index = $('#listTBody tr').length;
        let modelOptions = '<option value="">Select Model</option>';

        // Generate model options from PHP data
        <?php foreach ($modelBrandList as $combo): ?>
            modelOptions += `<option 
                    value="<?= $combo['model_id'] ?>"
                    data-brand="<?= Html::encode($combo['brand_name']) ?>"
                    data-brand-id="<?= $combo['brand_id'] ?>"
                    data-description="<?= Html::encode($combo['description']) ?>"
                >
    <?= Html::encode($combo['model_name'] . ' - ' . $combo['brand_name']) ?>
                </option>`;
        <?php endforeach; ?>

        // Create new row HTML
        let newRow = `
        <tr>
            <input type="hidden" name="ReserveItem[${index}][inventory_detail_id]" class="detail-id">
            <td class="text-center">${index + 1}</td>
            <td>
                <select class="form-control model-select">
                    ${modelOptions}
                </select>
            </td>
            <td>
                <input type="text" class="form-control brand-input" readonly>
            </td>
            <td>
                <input type="text" class="form-control description-input" readonly>
            </td>
            <td>
                <select class="form-control supplier-select" disabled>
                    <option value="">Select Model First</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control available-qty-input text-center" readonly value="0">
            </td>
            <td>
                <input type="number"
                    name="ReserveItem[${index}][reserved_qty]"
                    class="form-control text-center quantity-input"
                    min="1"
                    step="1"
                    disabled
                    required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </td>
        </tr>
    `;

        // Append new row to table
        $('#listTBody').append(newRow);
    }

    // Handle row removal
    $(document).on('click', '.remove-row', function () {
        if (confirm('Are you sure you want to remove this item?')) {
            let row = $(this).closest('tr');
            row.remove();

            // Update row numbers and name attributes
            $('#listTBody tr').each(function (index) {
                $(this).find('td:first').text(index + 1);

                // Update the name attributes to maintain correct indexing
                let hiddenInput = $(this).find('.detail-id');
                let quantityInput = $(this).find('.quantity-input');

                if (hiddenInput.length) {
                    hiddenInput.attr('name', `ReserveItem[${index}][inventory_detail_id]`);
                }

                if (quantityInput.length) {
                    quantityInput.attr('name', `ReserveItem[${index}][reserved_qty]`);
                }
            });
        }
    });

    // Form validation before submission
    $('form').on('submit', function (e) {
        let hasErrors = false;
        let errorMessages = [];
        let itemCombinations = []; // Track model+supplier combinations

        // Check if reserved for staff is selected
        let staffSelected = $('#inventoryreserveitem-user_id').val();

        if (!staffSelected) {
            hasErrors = true;
            errorMessages.push('Please select a staff member to reserve for');
        }

        // Check each row for valid data
        $('#listTBody tr').each(function (index) {
            let row = $(this);
            let modelSelected = row.find('.model-select').val();
            let supplierSelected = row.find('.supplier-select').val();
            let reservedQty = row.find('.quantity-input').val();
            let availableQty = parseInt(row.find('.available-qty-input').val()) || 0;
            let modelText = row.find('.model-select option:selected').text().trim();
            let supplierText = row.find('.supplier-select option:selected').text().trim();

            if (!modelSelected) {
                hasErrors = true;
                errorMessages.push(`Row ${index + 1}: Please select a model`);
            } else if (!supplierSelected) {
                hasErrors = true;
                errorMessages.push(`Row ${index + 1}: Please select a supplier`);
                row.find('.supplier-select').css('border', '2px solid red');
            } else {
                // Check for duplicate model+supplier combination
                let combination = modelSelected + '_' + supplierSelected;
                if (itemCombinations.indexOf(combination) !== -1) {
                    hasErrors = true;
                    errorMessages.push(`Row ${index + 1}: Duplicate item (${modelText} with ${supplierText})`);
                    row.find('.supplier-select').css('border', '2px solid orange');
                } else {
                    itemCombinations.push(combination);
                }

                // Check quantity
                if (!reservedQty || parseInt(reservedQty) <= 0) {
                    hasErrors = true;
                    errorMessages.push(`Row ${index + 1}: Please enter a valid reserve quantity`);
                    row.find('.quantity-input').css('border', '2px solid red');
                } else if (parseInt(reservedQty) > availableQty) {
                    hasErrors = true;
                    errorMessages.push(`Row ${index + 1}: Reserved quantity (${reservedQty}) exceeds available stock (${availableQty})`);
                } else {
                    // Remove highlighting if valid
                    row.find('.supplier-select, .quantity-input').css('border', '');
                }
            }
        });

        // If no items added, show error
        if ($('#listTBody tr').length === 0) {
            hasErrors = true;
            errorMessages.push('Please add at least one item to reserve');
        }

        if (hasErrors) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
            return false;
        }

        return true;
    });
</script>