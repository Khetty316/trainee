<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
?>

<div class="inventory-supplier-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'inventory-detail-form',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
    ]);
    ?>

    <div class="card-body p-2 table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Department</th>
                    <th>Supplier</th>
                    <th>Brand</th>
                    <th>Model Type</th>
                    <th>Currency</th>
                    <th>Unit Price</th>
                    <th class="text-right">Stock On Hand</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="item-table-body">
                <?php foreach ($itemList as $index => $item): ?>
                    <tr>
                        <td>
                            <?= $index + 1 ?>
                        </td>

                        <td>
                            <?=
                            $form->field($item, "[$index]department_code")->dropDownList(
                                    $departmentList,
                                    [
                                        'class' => 'form-control department-select',
                                        'prompt' => 'Select Department',
                                        'data-row-index' => $index
                                    ]
                            )->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                                    $form->field($item, "[$index]supplier_id", ['options' => ['class' => 'mb-0']])
                                    ->dropDownList($supplierList, [
                                        'class' => 'form-control supplier-select mb-0',
                                        'prompt' => 'Select Supplier',
                                        'data-row-index' => $index
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                                    $form->field($item, "[$index]brand_id", ['options' => ['class' => 'mb-0']])
                                    ->dropDownList($brandList, [
                                        'class' => 'form-control brand-select mb-0',
                                        'prompt' => 'Select Brand',
                                        'data-row-index' => $index
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                                    $form->field($item, "[$index]model_id", ['options' => ['class' => 'mb-0']])
                                    ->dropDownList($modelList, [
                                        'class' => 'form-control model-select mb-0',
                                        'prompt' => 'Select Model',
                                        'data-row-index' => $index
                                    ])
                                    ->label(false)
                            ?>
                            <div class="duplicate-alert-<?= $index ?>" style="display: none;" data-has-error="0">
                                <small class="text-danger"></small>
                            </div>
                        </td>

                        <td>
                            <?=
                            $form->field($item, "[$index]currency")->dropDownList(
                                    $currencyList,
                                    [
                                        'class' => 'form-control currency-select',
                                        'prompt' => 'Select Currency',
                                        'data-row-index' => $index
                                    ]
                            )->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                                    $form->field($item, "[$index]unit_price", ['options' => ['class' => 'mb-0']])
                                    ->input('number', [
                                        'class' => 'form-control text-right',
                                        'step' => '1',
                                        'min' => '0',
                                        'required' => true,
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                                    $form->field($item, "[$index]stock_on_hand", ['options' => ['class' => 'mb-0']])
                                    ->input('number', [
                                        'class' => 'form-control text-right',
                                        'step' => '1',
                                        'min' => '0',
                                        'required' => true,
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                                    $form->field($item, "[$index]active_sts", ['options' => ['class' => 'mb-0']])
                                    ->dropDownList([2 => 'Yes', 1 => 'No'], ['class' => 'form-control'])
                                    ->label(false)
                            ?>
                        </td>

                        <td>
                            <a href="javascript:void(0)" class="btn btn-danger btn-sm remove-row-btn">
                                <i class="fas fa-minus-circle"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary mt-1" id="add-row-btn">
            Add Item <i class="fas fa-plus-circle"></i>
        </button>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right', 'id' => 'submit-btn']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        const checkDuplicateUrl = '<?= Url::to(['/inventory/inventory/check-duplicate']) ?>';

        // Check for duplicate when any of the four fields change
        $(document).on('change', '.department-select, .supplier-select, .brand-select, .model-select', function () {
            let row = $(this).closest('tr');
            checkDuplicate(row);
        });

        function checkDuplicate(row) {
            let rowIndex = row.find('.department-select').data('row-index');

            let departmentCode = row.find('.department-select').val();
            let supplierId = row.find('.supplier-select').val();
            let brandId = row.find('.brand-select').val();
            let modelId = row.find('.model-select').val();

            // Only check if all four fields are selected
            if (!departmentCode || !supplierId || !brandId || !modelId) {
                $('.duplicate-alert-' + rowIndex).hide().attr('data-has-error', '0');
                return;
            }

            $.ajax({
                url: checkDuplicateUrl,
                type: 'POST',
                data: {
                    department_code: departmentCode,
                    supplier_id: supplierId,
                    brand_id: brandId,
                    model_id: modelId
                },
                success: function (response) {
                    if (response.exists) {
                        $('.duplicate-alert-' + rowIndex + ' small').text(response.message);
                        $('.duplicate-alert-' + rowIndex).show().attr('data-has-error', '1');
                    } else {
                        $('.duplicate-alert-' + rowIndex).hide().attr('data-has-error', '0');
                    }
                },
                error: function () {
                    console.error('Error checking duplicate');
                }
            });
        }

        // Prevent form submission if there are duplicate errors
        $('#inventory-detail-form').on('beforeSubmit', function (e) {
            let hasErrors = false;

            // Check if any duplicate alerts are visible
            $('[class^="duplicate-alert-"]').each(function () {
                if ($(this).attr('data-has-error') === '1') {
                    hasErrors = true;
                    return false; // break the loop
                }
            });

            if (hasErrors) {
                alert('Please resolve duplicate item errors before submitting.');
                return false; // prevent form submission
            }

            return true; // allow form submission
        });

        // Alternative: Disable submit button when errors exist
        function updateSubmitButton() {
            let hasErrors = false;

            $('[class^="duplicate-alert-"]').each(function () {
                if ($(this).attr('data-has-error') === '1') {
                    hasErrors = true;
                    return false;
                }
            });

            if (hasErrors) {
                $('#submit-btn').prop('disabled', true).addClass('disabled');
            } else {
                $('#submit-btn').prop('disabled', false).removeClass('disabled');
            }
        }

        // Call updateSubmitButton after each duplicate check
        $(document).on('change', '.department-select, .supplier-select, .brand-select, .model-select', function () {
            setTimeout(updateSubmitButton, 500); // Wait for AJAX to complete
        });

        // Remove row
        $(document).on('click', '.remove-row-btn', function (e) {
            e.preventDefault();
            let tableBody = $('#item-table-body');
            let rows = tableBody.find('tr');

            if (rows.length > 1) {
                $(this).closest('tr').remove();
                reindexRows();
                updateSubmitButton(); // Update submit button state after removing row
            } else {
                alert('At least one item is required');
            }
        });

        // Add row
        $('#add-row-btn').on('click', function () {
            addRow();
        });

        function addRow() {
            let tableBody = $('#item-table-body');
            let rows = tableBody.find('tr');
            let lastRow = rows.last();
            let newRow = lastRow.clone();
            let newIndex = rows.length;

            // Update all name/id attributes
            newRow.find('input, select').each(function () {
                let name = $(this).attr('name');
                let id = $(this).attr('id');

                if (name) {
                    name = name.replace(/\[\d+\]/, '[' + newIndex + ']');
                    $(this).attr('name', name);
                }

                if (id) {
                    id = id.replace(/-\d+-/, '-' + newIndex + '-');
                    $(this).attr('id', id);
                }

                // Update data-row-index
                if ($(this).hasClass('department-select') ||
                        $(this).hasClass('supplier-select') ||
                        $(this).hasClass('brand-select') ||
                        $(this).hasClass('model-select')) {
                    $(this).attr('data-row-index', newIndex);
                }

                // Clear inputs
                if (this.tagName === 'INPUT') {
                    if ($(this).attr('type') !== 'hidden') {
                        $(this).val('');
                    }
                    $(this).removeAttr('value');
                    $(this).removeAttr('aria-invalid');
                }

                if (this.tagName === 'SELECT') {
                    $(this).prop('selectedIndex', 0);
                    $(this).removeAttr('aria-invalid');
                }
            });

            // Remove ALL Yii validation states
            newRow.find('.has-error, .has-success, .field-error').removeClass('has-error has-success field-error');
            newRow.find('.help-block, .invalid-feedback').remove();
            newRow.find('.form-control').removeClass('is-invalid is-valid');

            // Update duplicate alert div class and reset error state
            newRow.find('[class^="duplicate-alert-"]')
                    .attr('class', 'duplicate-alert-' + newIndex)
                    .attr('data-has-error', '0')
                    .hide();

            // Update for attributes in labels
            newRow.find('label').each(function () {
                let forAttr = $(this).attr('for');
                if (forAttr) {
                    forAttr = forAttr.replace(/-\d+-/, '-' + newIndex + '-');
                    $(this).attr('for', forAttr);
                }
            });

            tableBody.append(newRow);
            reindexRows();
            updateSubmitButton();

        }

        function reindexRows() {
            let tableBody = $('#item-table-body');
            let rows = tableBody.find('tr');

            rows.each(function (newIndex) {

                // ✅ UPDATE "No." COLUMN
                $(this).find('td:first').text(newIndex + 1);

                $(this).find('input, select, textarea, label').each(function () {
                    let name = $(this).attr('name');
                    let id = $(this).attr('id');
                    let forAttr = $(this).attr('for');

                    if (name) {
                        name = name.replace(/\[\d+\]/, '[' + newIndex + ']');
                        $(this).attr('name', name);
                    }

                    if (id) {
                        id = id.replace(/-\d+-/, '-' + newIndex + '-');
                        $(this).attr('id', id);
                    }

                    if (forAttr) {
                        forAttr = forAttr.replace(/-\d+-/, '-' + newIndex + '-');
                        $(this).attr('for', forAttr);
                    }

                    if ($(this).hasClass('department-select') ||
                            $(this).hasClass('supplier-select') ||
                            $(this).hasClass('brand-select') ||
                            $(this).hasClass('model-select')) {
                        $(this).attr('data-row-index', newIndex);
                    }
                });

                $(this).find('[class^="duplicate-alert-"]')
                        .attr('class', 'duplicate-alert-' + newIndex);
            });
        }


        // Initial check on page load
        updateSubmitButton();
    });
</script>