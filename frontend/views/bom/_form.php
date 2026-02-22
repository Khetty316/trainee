<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

$isLegacy = $isLegacy ?? false;
?>

<div class="bomdetails-form">
    <?php
    $form = ActiveForm::begin([
        'id' => 'bom-details-form',
        'options' => ['autocomplete' => 'off']
    ]);
    ?>

    <?php if ($isLegacy): ?>
        <!-- Legacy record section -->
        <div class="alert alert-info alert-dismissible" id="legacy-alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-info"></i> Legacy Record</h5>
            This record uses free-text values from the old system. You can continue using these values,
            or migrate to the new dropdown system for better inventory tracking.
        </div>

        <!-- Legacy fields (read-only) -->
        <div id="legacy-fields">
            <div class="form-group">
                <label class="control-label">Model Type (Legacy)</label>
                <input type="text" class="form-control bg-light" value="<?= Html::encode($model->model_type) ?>" readonly>
                <p class="help-block text-muted">This is a free-text value from the old system.</p>
            </div>

            <div class="form-group">
                <label class="control-label">Brand (Legacy)</label>
                <input type="text" class="form-control bg-light" value="<?= Html::encode($model->brand) ?>" readonly>
                <p class="help-block text-muted">This is a free-text value from the old system.</p>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-warning" id="migrate-to-dropdown">
                    <i class="fas fa-exchange-alt"></i> Migrate to New Dropdown System
                </button>
                <p class="help-block text-muted">
                    Click this button to select model and brand from the inventory system instead.
                </p>
            </div>
        </div>

        <!-- Hidden inputs for legacy values -->
        <input type="hidden" name="BomDetails[model_type]" id="model-type-legacy" value="<?= Html::encode($model->model_type) ?>">
        <input type="hidden" name="BomDetails[brand]" id="brand-legacy" value="<?= Html::encode($model->brand) ?>">

        <!-- Dropdown fields (hidden initially for legacy records) -->
        <div id="dropdown-fields" style="display: none;">
        <?php else: ?>
            <!-- New records or already migrated records use dropdown directly -->
            <div id="dropdown-fields">
            <?php endif; ?>

            <!-- Custom searchable dropdown for Model Type -->
            <div class="form-group field-bomdetails-model_type_input required">
                <label class="control-label" for="model-type-search">Model Type</label>
                <div class="searchable-dropdown-wrapper">
                    <div class="searchable-dropdown">
                        <input type="text" 
                               id="model-type-search" 
                               class="form-control search-input"
                               placeholder="Type to search..."
                               value="<?= !empty($model->inventoryModel) ? $model->inventoryModel->type . ' - ' . $model->inventoryBrand->name : '' ?>"
                               autocomplete="off">
                        <button type="button" class="clear-btn" id="clear-selection" style="display: none;" title="Clear and select different option">
                            <i class="fas fa-times"></i>
                        </button>
                        <input type="hidden" 
                               name="BomDetails[model_type_input]" 
                               id="model-type-hidden"
                               value="<?= Html::encode($model->model_type_input) ?>">
                        <input type="hidden" 
                               name="BomDetails[brand_input]" 
                               id="brand-hidden"
                               value="<?= Html::encode($model->brand_input) ?>">
                        <div class="dropdown-list" id="model-type-list">
                            <?php foreach ($modelBrandList as $combo): ?>
                                <div class="dropdown-item" 
                                     data-model-id="<?= $combo['model_id'] ?>" 
                                     data-brand-id="<?= $combo['brand_id'] ?>"
                                     data-model-name="<?= Html::encode($combo['model_name']) ?>"
                                     data-brand-name="<?= Html::encode($combo['brand_name']) ?>"
                                     data-description="<?= Html::encode($combo['description']) ?>">
                                         <?= Html::encode($combo['model_name'] . ' - ' . $combo['brand_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="help-block">
                    <small class="text-muted">Type to filter suggestions, then click to select. Use × button to change selection.</small>
                </div>
            </div>

            <!-- Brand field (readonly after selection) -->
            <div class="form-group field-bomdetails-brand required">
                <label class="control-label" for="brand-display">Brand</label>
                <input type="text" 
                       id="brand-display" 
                       class="form-control bg-light"
                       value="<?= !empty($model->inventoryBrand) ? Html::encode($model->inventoryBrand->name) : '' ?>"
                       readonly
                       placeholder="Select model type first...">
                <div class="help-block">
                    <small class="text-muted">Auto-filled based on model type selection</small>
                </div>
            </div>

        </div>

        <?=
        $form->field($model, 'description')->textInput([
            'maxlength' => true,
            'readonly' => true,
            'placeholder' => 'Description will be auto-filled when selecting from inventory',
            'id' => 'description-input'
        ])
        ?>

        <?=
        $form->field($model, 'qty')->textInput([
            'type' => 'number',
            'step' => '1',
            'min' => 1,
            'placeholder' => 'Enter quantity'
        ])->label('Quantity')
        ?>

        <?=
        $form->field($model, 'remark')->textInput([
            'maxlength' => true,
            'placeholder' => 'Enter remark (optional)'
        ])
        ?>

        <div class="form-group">
            <?=
            Html::submitButton('Save', [
                'class' => 'btn btn-success',
                'id' => 'submit-btn'
            ])
            ?>

            <?php if (!$model->isNewRecord && $model->active_status == 1): ?>
                <?=
                Html::a('Deactivate',
                        ['deactivate-bom-details', 'id' => $model->id],
                        [
                            'class' => 'btn btn-danger float-right',
                            'data' => [
                                'confirm' => 'Are you sure you want to deactivate this item?',
                                'method' => 'post',
                            ],
                        ]
                )
                ?>
            <?php endif; ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <script>
        $(document).ready(function () {
            var isLegacy = <?= $isLegacy ? 'true' : 'false' ?>;
            var selectedModelText = ''; // Track the exact selected value to prevent tampering

            // Migration button click handler
            $('#migrate-to-dropdown').on('click', function () {
                if (confirm('Are you sure you want to migrate this record to use the dropdown system?\n\nThe old text values will be replaced with selections from the inventory system.')) {
                    // Hide legacy alert and fields
                    $('#legacy-alert').fadeOut();
                    $('#legacy-fields').fadeOut(function () {
                        // Show dropdown fields
                        $('#dropdown-fields').fadeIn();
                        isLegacy = false;

                        // Clear legacy hidden inputs
                        $('#model-type-legacy').val('');
                        $('#brand-legacy').val('');

                        // Clear all fields
                        $('#model-type-search').val('');
                        $('#model-type-hidden').val('');
                        $('#brand-hidden').val('');
                        $('#brand-display').val('');
                        $('#description-input').val('');
                        selectedModelText = '';

                        // Initialize dropdown
                        initSearchableDropdown();
                    });
                }
            });

            // Initialize searchable dropdown
            function initSearchableDropdown() {
                var $input = $('#model-type-search');
                var $list = $('#model-type-list');
                var $modelHidden = $('#model-type-hidden');
                var $brandHidden = $('#brand-hidden');
                var $brandDisplay = $('#brand-display');
                var $description = $('#description-input');
                var $clearBtn = $('#clear-selection');

                // Show dropdown on focus (only if no selection)
                $input.on('focus', function () {
                    if (!selectedModelText) {
                        $list.show();
                        filterItems($input.val());
                    }
                });

                // Hide dropdown when clicking outside
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.searchable-dropdown').length) {
                        $list.hide();
                    }
                });

                // Filter items on input (only if no selection)
                $input.on('input', function () {
                    if (!selectedModelText) {
                        filterItems($(this).val());
                    } else {
                        // User is trying to type when there's a selection
                        // Restore the selected value and show alert
                        var current = $(this).val();
                        if (current !== selectedModelText) {
                            $(this).val(selectedModelText);
                            alert('To change the selection, please click the × button first.');
                        }
                    }
                });

                // Keyboard navigation
                $input.on('keydown', function (e) {
                    if (!$list.is(':visible'))
                        return;

                    var $visibleItems = $list.find('.dropdown-item:visible');
                    var $selected = $visibleItems.filter('.selected');
                    var currentIndex = $visibleItems.index($selected);

                    switch (e.keyCode) {
                        case 40: // Down arrow
                            e.preventDefault();
                            if (currentIndex < $visibleItems.length - 1) {
                                $selected.removeClass('selected');
                                $visibleItems.eq(currentIndex + 1).addClass('selected');
                            }
                            break;
                        case 38: // Up arrow
                            e.preventDefault();
                            if (currentIndex > 0) {
                                $selected.removeClass('selected');
                                $visibleItems.eq(currentIndex - 1).addClass('selected');
                            }
                            break;
                        case 13: // Enter
                            e.preventDefault();
                            if ($selected.length) {
                                $selected.click();
                            }
                            break;
                        case 27: // Escape
                            e.preventDefault();
                            $list.hide();
                            break;
                    }
                });

                // Handle item selection
                $list.on('click', '.dropdown-item', function () {
                    var modelId = $(this).data('model-id');
                    var brandId = $(this).data('brand-id');
                    var modelName = $(this).data('model-name');
                    var brandName = $(this).data('brand-name');
                    var description = $(this).data('description');
                    var displayText = modelName + ' - ' + brandName;

                    // Set values
                    $input.val(displayText);
                    $modelHidden.val(modelId);
                    $brandHidden.val(brandId);
                    $brandDisplay.val(brandName);
                    $description.val(description);

                    // Set the selected text for validation
                    selectedModelText = displayText;

                    // Add visual indicator and show clear button
                    $input.addClass('has-selection').attr('readonly', true);
                    $clearBtn.show();

                    // Hide dropdown
                    $list.hide();
                });

                // Clear button handler
                $clearBtn.on('click', function (e) {
                    e.stopPropagation();

                    // Clear all fields
                    $input.val('').removeClass('has-selection').attr('readonly', false);
                    $modelHidden.val('');
                    $brandHidden.val('');
                    $brandDisplay.val('');
                    $description.val('');
                    selectedModelText = '';
                    $clearBtn.hide();

                    // Focus and show dropdown
                    $input.focus();
                    $list.show();
                    filterItems('');
                });

                // Filter dropdown items
                function filterItems(searchTerm) {
                    var $items = $list.find('.dropdown-item');

                    if (!searchTerm) {
                        $items.show().removeClass('selected');
                        $items.first().addClass('selected');
                        return;
                    }

                    var term = searchTerm.toLowerCase();
                    var hasVisible = false;

                    $items.each(function () {
                        var modelName = $(this).data('model-name').toLowerCase();
                        var brandName = $(this).data('brand-name').toLowerCase();
                        var combinedText = modelName + ' ' + brandName;

                        if (combinedText.indexOf(term) !== -1) {
                            $(this).show();
                            if (!hasVisible) {
                                $(this).addClass('selected');
                                hasVisible = true;
                            } else {
                                $(this).removeClass('selected');
                            }
                        } else {
                            $(this).hide().removeClass('selected');
                        }
                    });
                }

                // Initialize if editing existing record
                if ($modelHidden.val() && $input.val()) {
                    selectedModelText = $input.val();
                    $input.addClass('has-selection').attr('readonly', true);
                    $clearBtn.show();
                }
            }

            // Initialize dropdown if not legacy
            if (!isLegacy) {
                initSearchableDropdown();
            }

            // Form validation before submit
            $('#bom-details-form').on('beforeSubmit', function (e) {
                if (!isLegacy) {
                    var modelValue = $('#model-type-search').val();
                    var modelHidden = $('#model-type-hidden').val();
                    var brandHidden = $('#brand-hidden').val();

                    if (!modelValue || modelValue.trim() === '') {
                        alert('Please select a Model Type.');
                        $('#model-type-search').focus();
                        return false;
                    }

                    if (!modelHidden) {
                        alert('Please select a Model Type from the dropdown list.');
                        return false;
                    }

                    if (!brandHidden) {
                        alert('Please select a valid Model Type with Brand.');
                        return false;
                    }

                    // CRITICAL: Verify the displayed text matches the selected item exactly
                    if (modelValue !== selectedModelText) {
                        alert('The Model Type field has been modified. Please use the × button to change selection.');
                        $('#model-type-search').val(selectedModelText);
                        return false;
                    }

                    // Double-check: Verify the model and brand IDs match an actual item in the dropdown
                    var $matchingItem = $('#model-type-list').find('.dropdown-item[data-model-id="' + modelHidden + '"][data-brand-id="' + brandHidden + '"]');
                    if ($matchingItem.length === 0) {
                        alert('Invalid selection. Please select a valid Model Type from the dropdown.');
                        $('#clear-selection').click();
                        return false;
                    }

                    // Triple-check: Verify the displayed text matches the dropdown item text
                    var expectedText = $matchingItem.data('model-name') + ' - ' + $matchingItem.data('brand-name');
                    if (modelValue !== expectedText) {
                        alert('The Model Type value does not match the selected item. Please use the × button to select again.');
                        $('#clear-selection').click();
                        return false;
                    }
                } else {
                    var legacyModel = $('#model-type-legacy').val();
                    var legacyBrand = $('#brand-legacy').val();

                    if (!legacyModel || legacyModel.trim() === '') {
                        alert('Legacy Model Type is required.');
                        return false;
                    }

                    if (!legacyBrand || legacyBrand.trim() === '') {
                        alert('Legacy Brand is required.');
                        return false;
                    }
                }

                return true;
            });
        });
    </script>

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
            background-color: white;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 0 0 4px 4px;
        }

        .searchable-dropdown .dropdown-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
            transition: background-color 0.2s;
        }

        .searchable-dropdown .dropdown-item:hover,
        .searchable-dropdown .dropdown-item.selected {
            background-color: #007bff;
            color: white;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        /* Visual indicator for selected value */
        #model-type-search.has-selection {
            background-color: #e7f3ff;
            border-color: #007bff;
            padding-right: 35px;
        }

        /* Clear button */
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
            transition: color 0.2s;
        }

        .clear-btn:hover {
            color: #c82333;
        }

        .clear-btn:focus {
            outline: none;
        }
    </style>