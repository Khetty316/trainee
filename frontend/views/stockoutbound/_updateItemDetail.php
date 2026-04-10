<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$bomDetail = $model->bomDetail;
$isLegacy = $isLegacy ?? false;
?>

<div class="stock-detail">
    <?php
    $form = ActiveForm::begin([
        'id' => 'stockoutbound-details-form',
        'options' => ['autocomplete' => 'off']
    ]);
    ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Material Detail:</legend>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered">
                        <tr>
                            <td class="col-3">Model Type</td>
                            <td class="col-9"><?= isset($bomDetail->model_type) ? $bomDetail->model_type : $model->model_type ?></td>
                        </tr>
                        <tr>
                            <td>Brand</td>
                            <td><?= isset($bomDetail->brand) ? $bomDetail->brand : $model->brand ?></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><?= isset($bomDetail->description) ? $bomDetail->description : $model->descriptions ?></td>
                        </tr>
                        <tr>
                            <td>Total Quantity</td>
                            <td><?= isset($bomDetail->qty) ? $bomDetail->qty : $model->qty ?></td>
                        </tr>
                        <tr>
                            <td>Remark</td>
                            <td><?= isset($bomDetail->remark) ? $bomDetail->remark : $model->engineer_remark ?></td>
                        </tr>
                    </table>
                </div>
            </fieldset>
        </div>
        <!--        <div class="col-lg-12 col-md-12 col-sm-12">
                    <fieldset class="border p-1">
                        <legend class="w-auto px-2 m-0">Update Detail:</legend>           
                        <div class="row mb-2">
                            <div class="col-lg-6 col-md-12 col-sm-12">
        <?php // = $form->field($item, 'model_type')->textInput()  ?>
                            </div> 
                            <div class="col-lg-6 col-md-12 col-sm-12"> 
        <?php // = $form->field($item, 'brand')->textInput()  ?>
                            </div> 
                            <div class="col-lg-6 col-md-12 col-sm-12">
        <?php // = $form->field($item, 'descriptions')->textInput()  ?>
                            </div> 
                            <div class="col-lg-6 col-md-12 col-sm-12">
        <?php // = $form->field($item, 'qty')->textInput(['type' => 'number', 'step' => '1', 'min' => ($item->dispatched_qty + $item->unacknowledged_qty)])  ?>
                            </div> 
                        </div>
                    </fieldset>
                    <p>
        <?php // = Html::submitButton('Save', ['class' => 'btn btn-success px-3 proceed mt-3']) ?>
        <?php
//                if (!$item->isNewRecord && $item->active_sts == 1) {
//                    echo Html::a('Deactivate', ['deactivate-item', 'id' => $item->id], [
//                        'class' => 'btn btn-danger float-right mt-3',
//                        'data' => [
//                            'confirm' => 'Are you sure you want to deactivate this item?',
//                            'method' => 'post',
//                        ],
//                    ]);
//                }
        ?>
                    </p>
                </div>-->

        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Update Detail:</legend>       
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
                    <input type="hidden" name="StockOutboundDetails[model_type]" id="model-type-legacy" value="<?= Html::encode($model->model_type) ?>">
                    <input type="hidden" name="StockOutboundDetails[brand]" id="brand-legacy" value="<?= Html::encode($model->brand) ?>">

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
                                           value="<?= !empty($model->inventoryModel) ? Html::encode($model->inventoryModel->type . ' - ' . $model->inventoryBrand->name) : '' ?>" autocomplete="off">

                                    <?php if (!$model->isNewRecord && $model->active_sts == 1 && ($model->dispatched_qty === null || $model->dispatched_qty == 0) && ($model->unacknowledged_qty === null || $model->unacknowledged_qty == 0)) { ?>
                                        <button type="button" class="clear-btn" id="clear-selection"
                                                style="display: none;" title="Clear and select a different option">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php } ?>

                                    <input type="hidden" 
                                           name="StockOutboundDetails[model_type_input]" 
                                           id="model-type-hidden"
                                           value="<?= Html::encode($model->model_type_input) ?>">
                                    <input type="hidden" 
                                           name="StockOutboundDetails[brand_input]" 
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
                    $form->field($model, 'descriptions')->textInput([
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
                        'min' => ($model->dispatched_qty + $model->unacknowledged_qty),
                        'placeholder' => 'Enter quantity'
                    ])->label('Quantity')
                    ?>

            </fieldset>  
            <div class="form-group">
                <?=
                Html::submitButton('Save', [
                    'class' => 'btn btn-success mt-3',
                    'id' => 'submit-btn'
                ])
                ?>

                <?php if (!$model->isNewRecord && $model->active_sts == 1 && ($model->dispatched_qty === null || $model->dispatched_qty == 0) && ($model->unacknowledged_qty === null || $model->unacknowledged_qty == 0)): ?>
                    <?=
                    Html::a('Deactivate',
                            ['deactivate-item', 'id' => $model->id],
                            [
                                'class' => 'btn btn-danger float-right mt-3',
                                'data' => [
                                    'confirm' => 'Are you sure you want to deactivate this item?',
                                    'method' => 'post',
                                ],
                            ]
                    )
                    ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <?php ActiveForm::end(); ?>
</div> 
</div>
<script>
    $('.proceed').click(function (e) {
        const userConfirmation = confirm('Are you sure you want to proceed?');
        if (!userConfirmation) {
            e.preventDefault();
        }
    });
</script>
<script>
    $(document).ready(function () {

        var isLegacy = <?= $isLegacy ? 'true' : 'false' ?>;
        var selectedModelText = '';

        /* ================= MIGRATION ================= */
        $('#migrate-to-dropdown').on('click', function () {
            if (!confirm('Are you sure you want to migrate this record?\n\nOld values will be replaced.')) {
                return;
            }

            $('#legacy-alert').fadeOut();
            $('#legacy-fields').fadeOut(function () {
                $('#dropdown-fields').fadeIn();
                isLegacy = false;

                // Clear legacy values
                $('#model-type-legacy').val('');
                $('#brand-legacy').val('');

                // Reset fields
                $('#model-type-search').val('').removeClass('has-selection').attr('readonly', false);
                $('#model-type-hidden').val('');
                $('#brand-hidden').val('');
                $('#brand-display').val('');
                $('#description-input').val('');
                selectedModelText = '';

                $('#clear-selection').hide();

                initSearchableDropdown();
            });
        });

        /* ================= DROPDOWN ================= */
        function initSearchableDropdown() {

            var $input = $('#model-type-search');
            var $list = $('#model-type-list');
            var $modelHidden = $('#model-type-hidden');
            var $brandHidden = $('#brand-hidden');
            var $brandDisplay = $('#brand-display');
            var $description = $('#description-input');
            var $clearBtn = $('#clear-selection');

            /* ---------- SHOW DROPDOWN ---------- */
            $input.on('focus', function () {
                if (!selectedModelText) {
                    $list.show();
                    filterItems($input.val());
                }
            });

            $(document).on('click', function (e) {
                if (!$(e.target).closest('.searchable-dropdown').length) {
                    $list.hide();
                }
            });

            /* ---------- INPUT ---------- */
            $input.on('input', function () {
                if (!selectedModelText) {
                    filterItems($(this).val());
                } else {
                    if ($(this).val() !== selectedModelText) {
                        $(this).val(selectedModelText);
                        alert('Click × button to change selection.');
                    }
                }
            });

            /* ---------- KEYBOARD ---------- */
            $input.on('keydown', function (e) {
                if (!$list.is(':visible'))
                    return;

                var $items = $list.find('.dropdown-item:visible');
                var $selected = $items.filter('.selected');
                var index = $items.index($selected);

                if (e.keyCode === 40) { // down
                    e.preventDefault();
                    $selected.removeClass('selected');
                    $items.eq(index + 1).addClass('selected');
                }

                if (e.keyCode === 38) { // up
                    e.preventDefault();
                    $selected.removeClass('selected');
                    $items.eq(index - 1).addClass('selected');
                }

                if (e.keyCode === 13) { // enter
                    e.preventDefault();
                    if ($selected.length)
                        $selected.click();
                }

                if (e.keyCode === 27) { // esc
                    $list.hide();
                }
            });

            /* ---------- SELECT ITEM ---------- */
            $list.on('click', '.dropdown-item', function () {

                var modelId = $(this).data('model-id');
                var brandId = $(this).data('brand-id');
                var modelName = $(this).data('model-name');
                var brandName = $(this).data('brand-name');
                var description = $(this).data('description');

                var displayText = modelName + ' - ' + brandName;

                $input.val(displayText)
                        .addClass('has-selection')
                        .attr('readonly', true);

                $modelHidden.val(modelId);
                $brandHidden.val(brandId);
                $brandDisplay.val(brandName);
                $description.val(description);

                selectedModelText = displayText;

                $clearBtn.show(); // ✅ FIXED: always show after select
                $list.hide();
            });

            /* ---------- CLEAR BUTTON ---------- */
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
                $list.show();
                filterItems('');
            });

            /* ---------- FILTER ---------- */
            function filterItems(term) {
                var $items = $list.find('.dropdown-item');

                if (!term) {
                    $items.show().removeClass('selected');
                    $items.first().addClass('selected');
                    return;
                }

                var t = term.toLowerCase();
                var first = true;

                $items.each(function () {
                    var text = ($(this).data('model-name') + ' ' + $(this).data('brand-name')).toLowerCase();

                    if (text.includes(t)) {
                        $(this).show();
                        if (first) {
                            $(this).addClass('selected');
                            first = false;
                        } else {
                            $(this).removeClass('selected');
                        }
                    } else {
                        $(this).hide().removeClass('selected');
                    }
                });
            }

            /* ---------- INIT EXISTING VALUE ---------- */
            if ($modelHidden.val() && $input.val()) {
                selectedModelText = $input.val();
                $input.addClass('has-selection').attr('readonly', true);
                $clearBtn.show(); // ✅ IMPORTANT FIX
            }

            /* ---------- EXISTING RECORD LOCK ---------- */
            var isExistingRecord = <?= (!$model->isNewRecord) ? 'true' : 'false' ?>;

            if (isExistingRecord && !isLegacy) {
                $input.attr('readonly', true).addClass('has-selection');

                // ✅ DO NOT HIDE BUTTON ANYMORE
                if ($modelHidden.val() && $input.val()) {
                    $clearBtn.show();
                }
            }
        }

        /* ================= INIT ================= */
        if (!isLegacy) {
            initSearchableDropdown();
        }

        /* ================= VALIDATION ================= */
        $('#stockoutbound-details-form').on('beforeSubmit', function () {

            if (!isLegacy) {
                var modelValue = $('#model-type-search').val();
                var modelHidden = $('#model-type-hidden').val();
                var brandHidden = $('#brand-hidden').val();

                if (!modelValue) {
                    alert('Please select Model Type');
                    return false;
                }

                if (!modelHidden || !brandHidden) {
                    alert('Invalid selection');
                    return false;
                }

                if (modelValue !== selectedModelText) {
                    alert('Use × button to change selection');
                    $('#model-type-search').val(selectedModelText);
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