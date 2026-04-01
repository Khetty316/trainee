<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsWoMaterialRequestDetails */
/* @var $modelBrandList array */
/* @var $isLegacy bool */

$isLegacy = $isLegacy ?? false;
?>

<div class="material-request-form">

    <?php
    $form = ActiveForm::begin([
        'id' => 'material-request-details-form',
        'options' => ['autocomplete' => 'off'],
    ]);
    ?>

    <?php if ($isLegacy): ?>
        <!-- ===================== LEGACY RECORD SECTION ===================== -->
        <div class="alert alert-info alert-dismissible" id="legacy-alert">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-info"></i> Legacy Record</h5>
            This record uses free-text values from the old system. You can continue using these values,
            or migrate to the new dropdown system for better inventory tracking.
        </div>

        <!-- Legacy read-only fields -->
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

        <!-- Hidden inputs carrying legacy values on submit -->
        <input type="hidden" name="CmmsWoMaterialRequestDetails[model_type]" id="model-type-legacy"  value="<?= Html::encode($model->model_type) ?>">
        <input type="hidden" name="CmmsWoMaterialRequestDetails[brand]"       id="brand-legacy"       value="<?= Html::encode($model->brand) ?>">

        <!-- Dropdown fields — hidden until migration -->
        <div id="dropdown-fields" style="display: none;">

        <?php else: ?>
            <!-- ================ NEW / ALREADY-MIGRATED RECORD ================= -->
            <div id="dropdown-fields">

            <?php endif; ?>
            <?=
            $form->field($model, 'part_or_tool')->dropdownList(
                    [
                        '1' => 'Part',
                        '2' => 'Tool',
                    ],
                    [
                        'prompt' => '-- Select Part or Tool --',
                    ]
            )->label('Part or Tool')
            ?>

            <!-- Searchable Model + Brand combo dropdown -->
            <div class="form-group field-material-model_type_input required">
                <label class="control-label" for="model-type-search">Model Type</label>
                <div class="searchable-dropdown-wrapper">
                    <div class="searchable-dropdown">
                        <input type="text"
                               id="model-type-search"
                               class="form-control search-input"
                               placeholder="Type to search..."
                               value="<?=
                               !empty($model->inventoryModel) ? Html::encode($model->inventoryModel->type . ' - ' . $model->inventoryBrand->name) : ''
                               ?>"
                               autocomplete="off">

                        <button type="button" class="clear-btn" id="clear-selection"
                                style="display: none;" title="Clear and select a different option">
                            <i class="fas fa-times"></i>
                        </button>

                        <!-- These two hidden fields carry the real IDs to the server -->
                        <input type="hidden"
                               name="CmmsWoMaterialRequestDetails[model_type_input]"
                               id="model-type-hidden"
                               value="<?= Html::encode($model->model_type_input ?? '') ?>">
                        <input type="hidden"
                               name="CmmsWoMaterialRequestDetails[brand_input]"
                               id="brand-hidden"
                               value="<?= Html::encode($model->brand_input ?? '') ?>">

                        <div class="dropdown-list" id="model-type-list">
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
                <div class="help-block">
                    <small class="text-muted">Type to filter, then click to select. Use × to change selection.</small>
                </div>
            </div>

            <!-- Brand — auto-filled and read-only after selection -->
            <div class="form-group field-material-brand required">
                <label class="control-label" for="brand-display">Brand</label>
                <input type="text"
                       id="brand-display"
                       class="form-control bg-light"
                       value="<?= !empty($model->inventoryBrand) ? Html::encode($model->inventoryBrand->name) : '' ?>"
                       readonly
                       placeholder="Auto-filled after model selection">
                <div class="help-block">
                    <small class="text-muted">Auto-filled based on model type selection.</small>
                </div>
            </div>

        </div><!-- /#dropdown-fields -->

        <!-- Description (auto-filled from inventory) -->
        <?=
        $form->field($model, 'descriptions')->textInput([
            'maxlength' => true,
            'readonly' => true,
            'placeholder' => 'Auto-filled when model is selected',
            'id' => 'description-input',
        ])
        ?>

        <!-- Quantity -->
        <?=
        $form->field($model, 'qty')->textInput([
            'type' => 'number',
            'step' => '1',
            'min' => 1,
            'placeholder' => 'Enter quantity',
        ])->label('Quantity')
        ?>

        <!-- Remark -->
        <?=
        $form->field($model, 'remark')->textInput([
            'maxlength' => true,
            'placeholder' => 'Enter remark (optional)',
        ])
        ?>

        <!-- Submit + Deactivate -->
        <div class="form-group">
            <?=
            Html::submitButton('Save', [
                'class' => 'btn btn-success',
                'id' => 'submit-btn',
            ])
            ?>

            <?php if (!$model->isNewRecord && $model->active_sts == 1): ?>
                <?=
                Html::a('Deactivate',
                        ['deactivate-material-details', 'id' => $model->id],
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

    <!-- ============================= JS ============================= -->
    <script>
        $(document).ready(function () {
            var isLegacy = <?= $isLegacy ? 'true' : 'false' ?>;
            var selectedModelText = '';   // exact text of the chosen combo — prevents tampering

            /* ------------------------------------------------------------------ */
            /*  Migration button                                                    */
            /* ------------------------------------------------------------------ */
            $('#migrate-to-dropdown').on('click', function () {
                if (!confirm(
                        'Are you sure you want to migrate this record to the dropdown system?\n\n' +
                        'The old text values will be replaced with selections from the inventory system.'
                        ))
                    return;

                $('#legacy-alert').fadeOut();
                $('#legacy-fields').fadeOut(function () {
                    $('#dropdown-fields').fadeIn();
                    isLegacy = false;

                    // Clear legacy hidden inputs so they are not submitted
                    $('#model-type-legacy').val('');
                    $('#brand-legacy').val('');

                    // Reset dropdown fields
                    $('#model-type-search').val('');
                    $('#model-type-hidden').val('');
                    $('#brand-hidden').val('');
                    $('#brand-display').val('');
                    $('#description-input').val('');
                    selectedModelText = '';

                    initSearchableDropdown();
                });
            });

            /* ------------------------------------------------------------------ */
            /*  Searchable dropdown                                                 */
            /* ------------------------------------------------------------------ */
            function initSearchableDropdown() {
                var $input = $('#model-type-search');
                var $list = $('#model-type-list');
                var $modelHidden = $('#model-type-hidden');
                var $brandHidden = $('#brand-hidden');
                var $brandDisplay = $('#brand-display');
                var $description = $('#description-input');
                var $clearBtn = $('#clear-selection');

                // Show list on focus (only if nothing is selected yet)
                $input.on('focus', function () {
                    if (!selectedModelText) {
                        filterItems($input.val());
                        $list.show();
                    }
                });

                // Hide list when clicking outside the widget
                $(document).on('click', function (e) {
                    if (!$(e.target).closest('.searchable-dropdown').length) {
                        $list.hide();
                    }
                });

                // Typing — filter or block when a selection is already made
                $input.on('input', function () {
                    if (!selectedModelText) {
                        filterItems($(this).val());
                    } else {
                        // Prevent the user from editing the selected text directly
                        if ($(this).val() !== selectedModelText) {
                            $(this).val(selectedModelText);
                            alert('To change the selection, please click the × button first.');
                        }
                    }
                });

                // Keyboard navigation (↑ ↓ Enter Esc)
                $input.on('keydown', function (e) {
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
                            e.preventDefault();
                            $list.hide();
                            break;
                    }
                });

                // Item clicked — populate all fields
                $list.on('click', '.dropdown-item', function () {
                    var modelId = $(this).data('model-id');
                    var brandId = $(this).data('brand-id');
                    var modelName = $(this).data('model-name');
                    var brandName = $(this).data('brand-name');
                    var description = $(this).data('description');
                    var displayText = modelName + ' - ' + brandName;

                    $input.val(displayText).addClass('has-selection').attr('readonly', true);
                    $modelHidden.val(modelId);
                    $brandHidden.val(brandId);
                    $brandDisplay.val(brandName);
                    $description.val(description);

                    selectedModelText = displayText;
                    $clearBtn.show();
                    $list.hide();
                });

                // Clear button — resets everything
                $clearBtn.on('click', function (e) {
                    e.stopPropagation();
                    $input.val('').removeClass('has-selection').attr('readonly', false);
                    $modelHidden.val('');
                    $brandHidden.val('');
                    $brandDisplay.val('');
                    $description.val('');
                    selectedModelText = '';
                    $clearBtn.hide();
                    $input.focus();
                    filterItems('');
                    $list.show();
                });

                // Filter helper
                function filterItems(term) {
                    var $items = $list.find('.dropdown-item');
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

                // If editing an existing record, lock the input immediately
                if ($modelHidden.val() && $input.val()) {
                    selectedModelText = $input.val();
                    $input.addClass('has-selection').attr('readonly', true);
                    $clearBtn.show();
                }
            }

            // Boot the dropdown for non-legacy records
            if (!isLegacy) {
                initSearchableDropdown();
            }

            /* ------------------------------------------------------------------ */
            /*  Form validation before Yii's AJAX submit                           */
            /* ------------------------------------------------------------------ */
            // NOTE: form id is 'material-request-details-form' — make sure this matches
            $('#material-request-details-form').on('beforeSubmit', function () {
                if (!isLegacy) {
                    var modelValue = $('#model-type-search').val().trim();
                    var modelHidden = $('#model-type-hidden').val();
                    var brandHidden = $('#brand-hidden').val();

                    if (!modelValue) {
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
                    // Guard against tampered display text
                    if (modelValue !== selectedModelText) {
                        alert('The Model Type field has been modified. Please use the × button to change selection.');
                        $('#model-type-search').val(selectedModelText);
                        return false;
                    }
                    // Verify IDs exist in the actual list
                    var $match = $('#model-type-list').find(
                            '.dropdown-item[data-model-id="' + modelHidden + '"][data-brand-id="' + brandHidden + '"]'
                            );
                    if (!$match.length) {
                        alert('Invalid selection. Please select a valid Model Type from the dropdown.');
                        $('#clear-selection').trigger('click');
                        return false;
                    }
                    // Verify display text matches list item
                    var expected = $match.data('model-name') + ' - ' + $match.data('brand-name');
                    if (modelValue !== expected) {
                        alert('The Model Type value does not match the selected item. Please use the × button to select again.');
                        $('#clear-selection').trigger('click');
                        return false;
                    }
                } else {
                    // Legacy validation
                    if (!$('#model-type-legacy').val().trim()) {
                        alert('Legacy Model Type is required.');
                        return false;
                    }
                    if (!$('#brand-legacy').val().trim()) {
                        alert('Legacy Brand is required.');
                        return false;
                    }
                }

                return true;
            });
        });
    </script>

    <!-- ============================= CSS ============================= -->
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

        #model-type-search.has-selection {
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
        .clear-btn:hover  {
            color: #c82333;
        }
        .clear-btn:focus  {
            outline: none;
        }
    </style>