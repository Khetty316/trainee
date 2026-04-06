<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
?>

<style>
    .searchable-dropdown-wrapper {
        position: relative;
    }

    .searchable-dropdown {
        position: relative;
        width: 100%;
    }

    .searchable-dropdown .dropdown-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 250px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-top: none;
        background: #fff;
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0,0,0,.1);
        border-radius: 0 0 4px 4px;
    }

    .searchable-dropdown .dropdown-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
        transition: background-color .2s;
    }

    .searchable-dropdown .dropdown-item:hover,
    .searchable-dropdown .dropdown-item.selected {
        background-color: #007bff;
        color: #fff;
    }

    .model-type-search.has-selection {
        background-color: #e7f3ff;
        border-color: #007bff;
        padding-right: 35px;
    }

    .clear-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #dc3545;
        font-size: 18px;
        cursor: pointer;
        padding: 0 5px;
        z-index: 10;
        transition: color .2s;
    }
    .clear-btn:hover {
        color: #c82333;
    }
    .clear-btn:focus {
        outline: none;
    }
</style>

<div class="inventory-item-form">

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
                    <th>Model Type</th>
                    <th>Brand</th>
                    <th>Currency</th>
                    <th>Unit Price</th>
                    <th class="text-right">Opening Balance</th>
                    <th>Active</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="item-table-body">
                <?php foreach ($itemList as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>

                        <!-- Department -->
                        <td>
                            <?=
                            $form->field($item, "[$index]department_code")->dropDownList(
                                    $departmentList,
                                    [
                                        'class' => 'form-control department-select',
                                        'prompt' => 'Select Department',
                                        'data-row-index' => $index,
                                    ]
                            )->label(false)
                            ?>
                        </td>

                        <!-- Supplier -->
                        <td>
                            <?=
                                    $form->field($item, "[$index]supplier_id", ['options' => ['class' => 'mb-0']])
                                    ->dropDownList($supplierList, [
                                        'class' => 'form-control supplier-select mb-0',
                                        'prompt' => 'Select Supplier',
                                        'data-row-index' => $index,
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <!-- Model Type (searchable dropdown) -->
                        <td>
                            <div class="searchable-dropdown-wrapper">
                                <div class="searchable-dropdown">
                                    <?php
                                    // Repopulate display text if model_id is already set (after failed validation)
                                    $selectedDisplayText = '';
                                    $selectedBrandName = '';
                                    if (!empty($item->model_id) && !empty($item->brand_id)) {
                                        foreach ($modelBrandList as $combo) {
                                            if ($combo['model_id'] == $item->model_id && $combo['brand_id'] == $item->brand_id) {
                                                $selectedDisplayText = $combo['model_name'] . ' - ' . $combo['brand_name'];
                                                $selectedBrandName = $combo['brand_name'];
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <input type="text"
                                           class="form-control search-input model-type-search <?= $item->hasErrors('model_id') ? 'is-invalid' : '' ?> <?= !empty($selectedDisplayText) ? 'has-selection' : '' ?>"
                                           data-row-index="<?= $index ?>"
                                           placeholder="Type to search..."
                                           autocomplete="off"
                                           value="<?= Html::encode($selectedDisplayText) ?>"
                                           <?= !empty($selectedDisplayText) ? 'readonly' : '' ?>>

                                    <button type="button" class="clear-btn" style="<?= !empty($selectedDisplayText) ? '' : 'display:none;' ?>" title="Clear selection">
                                        <i class="fas fa-times"></i>
                                    </button>

                                    <input type="hidden"
                                           name="InventoryDetail[<?= $index ?>][model_id]"
                                           class="model-id-hidden"
                                           value="<?= Html::encode($item->model_id ?? '') ?>">

                                    <input type="hidden"
                                           name="InventoryDetail[<?= $index ?>][brand_id]"
                                           class="brand-id-hidden"
                                           value="<?= Html::encode($item->brand_id ?? '') ?>">

                                    <div class="dropdown-list">
                                        <?php foreach ($modelBrandList as $combo): ?>
                                            <div class="dropdown-item"
                                                 data-model-id="<?= Html::encode($combo['model_id']) ?>"
                                                 data-brand-id="<?= Html::encode($combo['brand_id']) ?>"
                                                 data-model-name="<?= Html::encode($combo['model_name']) ?>"
                                                 data-brand-name="<?= Html::encode($combo['brand_name']) ?>"
                                                 data-description="<?= Html::encode($combo['description']) ?>">
                                                     <?= Html::encode($combo['model_name'] . ' - ' . $combo['brand_name']) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Server-side validation errors -->
                            <?php if ($item->hasErrors('model_id')): ?>
                                <div class="text-danger" style="font-size: 0.875em;">
                                    <?= Html::encode($item->getFirstError('model_id')) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Client-side duplicate alert -->
                            <div class="duplicate-alert duplicate-alert-<?= $index ?>"
                                 style="display:none;"
                                 data-has-error="0"
                                 data-row-index="<?= $index ?>">
                                <small class="text-danger"></small>
                            </div>
                        </td>

                        <!-- Brand (auto-filled) -->
                        <td>
                            <input type="text"
                                   class="form-control bg-light brand-display"
                                   readonly
                                   placeholder="Auto-filled"
                                   value="<?= Html::encode($selectedBrandName) ?>">
                            <small class="text-muted">Auto-filled based on model type.</small>
                        </td>

                        <!-- Currency -->
                        <td>
                            <?=
                            $form->field($item, "[$index]currency_id")->dropDownList(
                                    $currencyList,
                                    [
                                        'class' => 'form-control currency-select',
                                        'prompt' => 'Select Currency',
                                        'data-row-index' => $index,
                                    ]
                            )->label(false)
                            ?>
                        </td>

                        <!-- Unit Price -->
                        <td>
                            <?=
                                    $form->field($item, "[$index]unit_price", ['options' => ['class' => 'mb-0']])
                                    ->input('number', [
                                        'class' => 'form-control text-right',
                                        'step' => '0.01', // ✅ allow decimal (2 decimal places)
                                        'min' => '0', // ✅ prevent negative
                                        'required' => true,
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <!-- Opening Balance -->
                        <td>
                            <?=
                                    $form->field($item, "[$index]stock_in", ['options' => ['class' => 'mb-0']])
                                    ->input('number', [
                                        'class' => 'form-control text-right',
                                        'step' => '1',
                                        'min' => '0',
                                        'required' => true,
                                    ])
                                    ->label(false)
                            ?>
                        </td>

                        <!-- Active -->
                        <td>
                            <?=
                                    $form->field($item, "[$index]active_sts", ['options' => ['class' => 'mb-0']])
                                    ->dropDownList([2 => 'Yes', 1 => 'No'], ['class' => 'form-control'])
                                    ->label(false)
                            ?>
                        </td>

                        <!-- Remove -->
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

        // ----------------------------------------------------------------
        // Searchable dropdown — delegated for dynamic rows
        // ----------------------------------------------------------------

        // Show dropdown on focus
        $(document).on('focus', '.model-type-search', function () {
            var $wrapper = $(this).closest('.searchable-dropdown');
            var $hidden = $wrapper.find('.model-id-hidden');
            if (!$hidden.val()) {
                filterItems($wrapper, $(this).val());
                $wrapper.find('.dropdown-list').show();
            }
        });

        // Hide dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.searchable-dropdown').length) {
                $('.dropdown-list').hide();
            }
        });

        // Typing — filter or block if already selected
        $(document).on('input', '.model-type-search', function () {
            var $wrapper = $(this).closest('.searchable-dropdown');
            var $hidden = $wrapper.find('.model-id-hidden');
            var selectedText = $wrapper.data('selected-text') || '';

            if (!$hidden.val()) {
                filterItems($wrapper, $(this).val());
                $wrapper.find('.dropdown-list').show();
            } else {
                if ($(this).val() !== selectedText) {
                    $(this).val(selectedText);
                }
            }
        });

        // Keyboard navigation
        $(document).on('keydown', '.model-type-search', function (e) {
            var $wrapper = $(this).closest('.searchable-dropdown');
            var $list = $wrapper.find('.dropdown-list');
            if (!$list.is(':visible'))
                return;

            var $visible = $list.find('.dropdown-item:visible');
            var $selected = $visible.filter('.selected');
            var idx = $visible.index($selected);

            switch (e.keyCode) {
                case 40: // Down
                    e.preventDefault();
                    if (idx < $visible.length - 1) {
                        $selected.removeClass('selected');
                        $visible.eq(idx + 1).addClass('selected');
                    }
                    break;
                case 38: // Up
                    e.preventDefault();
                    if (idx > 0) {
                        $selected.removeClass('selected');
                        $visible.eq(idx - 1).addClass('selected');
                    }
                    break;
                case 13: // Enter
                    e.preventDefault();
                    if ($selected.length)
                        $selected.trigger('click');
                    break;
                case 27: // Escape
                    $list.hide();
                    break;
            }
        });

        // Item selected from dropdown
        $(document).on('click', '.searchable-dropdown .dropdown-item', function () {
            var $wrapper = $(this).closest('.searchable-dropdown');
            var $input = $wrapper.find('.model-type-search');
            var $modelHidden = $wrapper.find('.model-id-hidden');
            var $brandHidden = $wrapper.find('.brand-id-hidden');
            var $row = $wrapper.closest('tr');
            var $brandDisplay = $row.find('.brand-display');

            var modelId = $(this).data('model-id');
            var brandId = $(this).data('brand-id');
            var modelName = $(this).data('model-name');
            var brandName = $(this).data('brand-name');
            var displayText = modelName + ' - ' + brandName;

            $input.val(displayText).addClass('has-selection').attr('readonly', true);
            $modelHidden.val(modelId);
            $brandHidden.val(brandId);
            $brandDisplay.val(brandName);
            $wrapper.data('selected-text', displayText);
            $wrapper.find('.clear-btn').show();
            $wrapper.find('.dropdown-list').hide();

            checkDuplicate($row);
        });

        // Clear button
        $(document).on('click', '.clear-btn', function (e) {
            e.stopPropagation();
            var $wrapper = $(this).closest('.searchable-dropdown');
            var $input = $wrapper.find('.model-type-search');
            var $modelHidden = $wrapper.find('.model-id-hidden');
            var $brandHidden = $wrapper.find('.brand-id-hidden');
            var $row = $wrapper.closest('tr');
            var $brandDisplay = $row.find('.brand-display');
            var rowIndex = $row.find('.department-select').data('row-index');

            $input.val('').removeClass('has-selection').attr('readonly', false);
            $modelHidden.val('');
            $brandHidden.val('');
            $brandDisplay.val('');
            $wrapper.data('selected-text', '');
            $(this).hide();
            filterItems($wrapper, '');
            $wrapper.find('.dropdown-list').show();
            $input.focus();

            // Clear duplicate error when model cleared
            $('.duplicate-alert-' + rowIndex).hide().attr('data-has-error', '0');
            updateSubmitButton();
        });

        function filterItems($wrapper, term) {
            var $items = $wrapper.find('.dropdown-item');
            $items.removeClass('selected');

            if (!term) {
                $items.show();
                $items.first().addClass('selected');
                return;
            }

            var lc = term.toLowerCase();
            var first = true;

            $items.each(function () {
                var combined = ($(this).data('model-name') + ' ' + $(this).data('brand-name')).toLowerCase();
                if (combined.indexOf(lc) !== -1) {
                    $(this).show();
                    if (first) {
                        $(this).addClass('selected');
                        first = false;
                    }
                } else {
                    $(this).hide();
                }
            });
        }

        // ----------------------------------------------------------------
        // Duplicate check
        // ----------------------------------------------------------------
        $(document).on('change', '.department-select, .supplier-select', function () {
            checkDuplicate($(this).closest('tr'));
        });

        function checkDuplicate(row) {
            var rowIndex = row.find('.department-select').data('row-index');
            var departmentCode = row.find('.department-select').val();
            var supplierId = row.find('.supplier-select').val();
            var brandId = row.find('.brand-id-hidden').val();
            var modelId = row.find('.model-id-hidden').val();

            if (!departmentCode || !supplierId || !brandId || !modelId) {
                $('.duplicate-alert-' + rowIndex).hide().attr('data-has-error', '0');
                updateSubmitButton();
                return;
            }

            $.ajax({
                url: checkDuplicateUrl,
                type: 'POST',
                data: {
                    department_code: departmentCode,
                    supplier_id: supplierId,
                    brand_id: brandId,
                    model_id: modelId,
                },
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                },
                success: function (response) {
                    if (response.exists) {
                        $('.duplicate-alert-' + rowIndex + ' small').text(response.message);
                        $('.duplicate-alert-' + rowIndex).show().attr('data-has-error', '1');
                    } else {
                        $('.duplicate-alert-' + rowIndex).hide().attr('data-has-error', '0');
                    }
                    updateSubmitButton();
                },
                error: function (xhr) {
                    console.error('Duplicate check error — Status:', xhr.status, 'Response:', xhr.responseText);
                }
            });
        }

        // ----------------------------------------------------------------
        // Submit guard
        // ----------------------------------------------------------------
        $('#inventory-detail-form').on('beforeSubmit', function () {
            var hasErrors = false;
            $('.duplicate-alert').each(function () {
                if ($(this).attr('data-has-error') === '1') {
                    hasErrors = true;
                    return false;
                }
            });
            if (hasErrors) {
                alert('Please resolve duplicate item errors before submitting.');
                return false;
            }
            return true;
        });

        function updateSubmitButton() {
            var hasErrors = false;
            $('.duplicate-alert').each(function () {
                if ($(this).attr('data-has-error') === '1') {
                    hasErrors = true;
                    return false;
                }
            });
            $('#submit-btn').prop('disabled', hasErrors).toggleClass('disabled', hasErrors);
        }

        // ----------------------------------------------------------------
        // Remove row
        // ----------------------------------------------------------------
        $(document).on('click', '.remove-row-btn', function (e) {
            e.preventDefault();
            var rows = $('#item-table-body tr');
            if (rows.length > 1) {
                $(this).closest('tr').remove();
                reindexRows();
                updateSubmitButton();
            } else {
                alert('At least one item is required.');
            }
        });

        // ----------------------------------------------------------------
        // Add row
        // ----------------------------------------------------------------
        $('#add-row-btn').on('click', function () {
            var tableBody = $('#item-table-body');
            var newIndex = tableBody.find('tr').length;
            var newRow = tableBody.find('tr').last().clone();

            newRow.find('input, select').each(function () {
                var name = $(this).attr('name');
                var id = $(this).attr('id');

                if (name)
                    $(this).attr('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
                if (id)
                    $(this).attr('id', id.replace(/-\d+-/, '-' + newIndex + '-'));

                if ($(this).hasClass('department-select') || $(this).hasClass('supplier-select')) {
                    $(this).attr('data-row-index', newIndex);
                }

                if (this.tagName === 'INPUT') {
                    $(this).val('').removeAttr('value aria-invalid readonly');
                    $(this).removeClass('has-selection');
                }
                if (this.tagName === 'SELECT') {
                    $(this).prop('selectedIndex', 0).removeAttr('aria-invalid');
                }
            });

            // Reset searchable dropdown
            newRow.find('.searchable-dropdown').data('selected-text', '');
            newRow.find('.clear-btn').hide();
            newRow.find('.dropdown-list').hide();

            // Reset validation states
            newRow.find('.has-error, .has-success').removeClass('has-error has-success');
            newRow.find('.help-block, .invalid-feedback').remove();
            newRow.find('.form-control').removeClass('is-invalid is-valid');

            // Reset duplicate alert
            newRow.find('.duplicate-alert')
                    .attr('class', 'duplicate-alert duplicate-alert-' + newIndex)
                    .attr('data-has-error', '0')
                    .attr('data-row-index', newIndex)
                    .hide();
            newRow.find('.duplicate-alert small').text('');

            // Update labels
            newRow.find('label').each(function () {
                var forAttr = $(this).attr('for');
                if (forAttr)
                    $(this).attr('for', forAttr.replace(/-\d+-/, '-' + newIndex + '-'));
            });

            tableBody.append(newRow);
            reindexRows();
            updateSubmitButton();
        });

        // ----------------------------------------------------------------
        // Reindex rows
        // ----------------------------------------------------------------
        function reindexRows() {
            $('#item-table-body tr').each(function (newIndex) {
                $(this).find('td:first').text(newIndex + 1);

                $(this).find('input, select, textarea, label').each(function () {
                    var name = $(this).attr('name');
                    var id = $(this).attr('id');
                    var forAttr = $(this).attr('for');

                    if (name)
                        $(this).attr('name', name.replace(/\[\d+\]/, '[' + newIndex + ']'));
                    if (id)
                        $(this).attr('id', id.replace(/-\d+-/, '-' + newIndex + '-'));
                    if (forAttr)
                        $(this).attr('for', forAttr.replace(/-\d+-/, '-' + newIndex + '-'));

                    if ($(this).hasClass('department-select') || $(this).hasClass('supplier-select')) {
                        $(this).attr('data-row-index', newIndex);
                    }
                });

                $(this).find('.duplicate-alert')
                        .attr('class', 'duplicate-alert duplicate-alert-' + newIndex)
                        .attr('data-row-index', newIndex);
            });
        }

        $('.searchable-dropdown').each(function () {
            var $input = $(this).find('.model-type-search');
            if ($input.val()) {
                $(this).data('selected-text', $input.val());
            }
        });
        // Initial state
        updateSubmitButton();
    });
</script>