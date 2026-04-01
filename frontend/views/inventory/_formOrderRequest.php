<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventoryOrderRequest */
/* @var $form yii\widgets\ActiveForm */

if ($moduleIndex === 'execPending') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
} else if ($moduleIndex === 'execAll') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
} else if ($moduleIndex === 'assistPending') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
} else if ($moduleIndex === 'assistAll') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
} else if ($moduleIndex === 'projcoor') {
    $pageName = 'Purchasing - Project Coordinator';
    $module = 'projcoor';
} else if ($moduleIndex === 'maintenanceHeadPending') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
}else if ($moduleIndex === 'maintenanceHeadAll') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
}

$this->title = 'Add New Order Request';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = ['label' => 'Order Request List', 'url' => ['order-request-list', 'type' => $moduleIndex]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="add-new-order-request-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row mt-3 mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex justify-content-left align-items-center">
                <h6 class="mb-0 mr-2 text-nowrap">Reserved For: </h6>
                <span>
                    <?=
                    $form->field($reserve, 'user_id')->dropDownList(
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
                        <th style="min-width: 130px;">Supplier</th>
                        <th style="min-width: 130px;">Model Type</th>
                        <th style="min-width: 120px;">Brand</th>
                        <th style="min-width: 180px;">Item Description</th>
                        <th style="min-width: 80px;">Quantity</th>
                        <th style="min-width: 80px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="listTBody">
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <select class="form-control supplier-select" name="InventoryOrderRequest[<?= $index ?>][supplier_id]" data-row="<?= $index ?>">
                                    <option value="">Select Supplier (Optional)</option>
                                    <?php
                                    // Get selected supplier ID safely
                                    $selectedSupplierId = null;
                                    if ($item->inventoryDetail && $item->inventoryDetail->supplier) {
                                        $selectedSupplierId = $item->inventoryDetail->supplier->id;
                                    }

                                    foreach ($supplierList as $value => $label):
                                        ?>
                                        <option value="<?= $value ?>" <?= ($value == $selectedSupplierId) ? 'selected' : '' ?>>
                                            <?= Html::encode($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <select class="form-control model-select" name="InventoryOrderRequest[<?= $index ?>][model_id]" data-row="<?= $index ?>">
                                    <option value="">Select Model</option>
                                    <?php
                                    // Remove duplicates for display
                                    $uniqueModels = [];
                                    $modelIds = [];
                                    foreach ($allModels ?? $modelBrandList as $combo) {
                                        if (!in_array($combo['model_id'], $modelIds)) {
                                            $modelIds[] = $combo['model_id'];
                                            $uniqueModels[] = $combo;
                                        }
                                    }
                                    // Sort by model name
                                    usort($uniqueModels, function ($a, $b) {
                                        return strcmp($a['model_name'] . ' ' . $a['brand_name'], $b['model_name'] . ' ' . $b['brand_name']);
                                    });
                                    foreach ($uniqueModels as $combo):
                                        ?>
                                        <option 
                                            value="<?= $combo['model_id'] ?>"
                                            data-brand="<?= Html::encode($combo['brand_name']) ?>"
                                            data-brand-id="<?= $combo['brand_id'] ?>"
                                            data-description="<?= Html::encode($combo['description'] ?? '') ?>"
                                            <?= ($combo['model_id'] == $item->inventory_model_id) ? 'selected' : '' ?>
                                            >
                                                <?= Html::encode($combo['model_name'] . ' - ' . $combo['brand_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control brand-input" readonly value="<?= $item->brand_name ?? '' ?>">
                                <input type="hidden" name="InventoryOrderRequest[<?= $index ?>][inventory_brand_id]" class="brand-id" value="<?= $item->brand_id ?? '' ?>">
                            </td>
                            <td>
                                <input type="text" class="form-control description-input" readonly value="<?= $item->item_description ?? '' ?>">
                            </td>
                            <td>
                                <?=
                                Html::input(
                                        'number',
                                        "InventoryOrderRequest[$index][required_qty]",
                                        $item->required_qty ?? '',
                                        [
                                            'class' => 'form-control text-center quantity-input',
                                            'min' => '1',
                                            'required' => true
                                        ]
                                )
                                ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm remove-row">
                                    <i class="fas fa-minus-circle"></i>
                                </button>
                            </td>
                    <input type="hidden" name="InventoryOrderRequest[<?= $index ?>][inventory_model_id]" class="model-id" value="<?= $item->inventory_model_id ?? '' ?>">
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7">
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
// Pass PHP data to JavaScript
    var modelsBySupplier = <?= json_encode($modelsBySupplier) ?>;
    var allModels = <?= json_encode($allModels ?? $modelBrandList) ?>;
    var allSuppliers = <?= json_encode($supplierDetails) ?>;

// Create reverse mapping: model_id + brand_id -> suppliers
    var suppliersByModelAndBrand = {};

    allModels.forEach(function (model) {
        // Create a composite key using model_id and brand_id
        var compositeKey = model.model_id + '_' + model.brand_id;

        if (!suppliersByModelAndBrand[compositeKey]) {
            suppliersByModelAndBrand[compositeKey] = [];
        }
        if (model.supplier_id && !suppliersByModelAndBrand[compositeKey].includes(parseInt(model.supplier_id))) {
            suppliersByModelAndBrand[compositeKey].push(parseInt(model.supplier_id));
        }
    });

    $(document).ready(function () {
        // Initialize all selects
        $('.model-select, .supplier-select').prop('disabled', false);

        // Check for pre-filled values
        $('tr').each(function () {
            let row = $(this);
            let modelSelect = row.find('.model-select');
            let supplierSelect = row.find('.supplier-select');
            let modelId = modelSelect.val();
            let supplierId = supplierSelect.val();
            let brandId = row.find('.brand-id').val();

            if (modelId && supplierId && brandId) {
                // Both selected - validate they match with brand
                let compositeKey = modelId + '_' + brandId;
                let allowedSuppliers = suppliersByModelAndBrand[compositeKey] || [];
                if (!allowedSuppliers.includes(parseInt(supplierId))) {
                    // Mismatch - clear supplier
                    supplierSelect.val('');
                }
            } else if (modelId && brandId) {
                // Only model selected - filter suppliers by model AND brand
                filterSuppliersByModelAndBrand(row, modelId, brandId);
            } else if (supplierId) {
                // Only supplier selected - filter models
                filterModelsBySupplier(row, supplierId);
            }
        });
    });

// Handle model selection change
    $(document).on('change', '.model-select', function () {
        let row = $(this).closest('tr');
        let modelId = $(this).val();
        let selected = $(this).find(':selected');
        let supplierSelect = row.find('.supplier-select');

        // Update brand and description
        if (modelId) {
            let brand = selected.data('brand');
            let description = selected.data('description');
            let brandId = selected.data('brand-id');

            row.find('.brand-input').val(brand);
            row.find('.brand-id').val(brandId);
            row.find('.description-input').val(description);
            row.find('.model-id').val(modelId);

            // Filter suppliers based on selected model AND brand
            filterSuppliersByModelAndBrand(row, modelId, brandId);

            // Save current supplier selection if valid
            let currentSupplier = supplierSelect.val();
            let compositeKey = modelId + '_' + brandId;
            let allowedSuppliers = suppliersByModelAndBrand[compositeKey] || [];
            if (currentSupplier && !allowedSuppliers.includes(parseInt(currentSupplier))) {
                supplierSelect.val(''); // Clear invalid supplier
            }
        } else {
            // Clear model-specific fields
            row.find('.brand-input').val('');
            row.find('.brand-id').val('');
            row.find('.description-input').val('');
            row.find('.model-id').val('');

            // Reset supplier to show all
            resetSupplierOptions(row, 'all');
        }
    });

// Handle supplier selection change
    $(document).on('change', '.supplier-select', function () {
        let row = $(this).closest('tr');
        let supplierId = $(this).val();
        let modelSelect = row.find('.model-select');
        let currentModel = modelSelect.val();
        let currentBrandId = row.find('.brand-id').val();

        if (supplierId) {
            // Filter models based on selected supplier
            filterModelsBySupplier(row, supplierId);

            // Check if current model+brand is valid for this supplier
            if (currentModel && currentBrandId) {
                let compositeKey = currentModel + '_' + currentBrandId;
                let allowedSuppliers = suppliersByModelAndBrand[compositeKey] || [];
                if (!allowedSuppliers.includes(parseInt(supplierId))) {
                    // Current model+brand not valid for this supplier - clear it
                    modelSelect.val('');
                    row.find('.brand-input').val('');
                    row.find('.brand-id').val('');
                    row.find('.description-input').val('');
                    row.find('.model-id').val('');
                }
            }
        } else {
            // Reset model to show all
            resetModelOptions(row, 'all');
        }
    });

// NEW FUNCTION: Filter suppliers by both model ID and brand ID
    function filterSuppliersByModelAndBrand(row, modelId, brandId) {
        let supplierSelect = row.find('.supplier-select');
        let currentSupplierId = supplierSelect.val();

        // Create composite key
        let compositeKey = modelId + '_' + brandId;

        // Get suppliers for this model and brand combination
        let allowedSuppliers = suppliersByModelAndBrand[compositeKey] || [];

        // Generate supplier options
        let options = '<option value="">Select Supplier (Optional)</option>';

        if (allowedSuppliers.length > 0) {
            allowedSuppliers.forEach(function (supplierId) {
                let supplier = allSuppliers.find(s => s.id == supplierId);
                if (supplier) {
                    let selected = (supplierId == currentSupplierId) ? 'selected' : '';
                    options += `<option value="${supplier.id}" ${selected}>${supplier.name}</option>`;
                }
            });

            // Add "Other Suppliers" option if user wants to see all
            options += `<option value="all">-- Show All Suppliers --</option>`;
        } else {
            options = '<option value="">No suppliers for this model and brand</option>';
            options += `<option value="all">-- Show All Suppliers --</option>`;
        }

        supplierSelect.empty().append(options);

        // Handle "Show All Suppliers" selection
        supplierSelect.off('change.showall').on('change.showall', function () {
            if ($(this).val() === 'all') {
                resetSupplierOptions(row, 'all');
            }
        });
    }

    function filterModelsBySupplier(row, supplierId) {
        let modelSelect = row.find('.model-select');
        let currentModelId = modelSelect.val();

        // Get models for this supplier
        let allowedModels = modelsBySupplier[supplierId] || [];

        // Generate model options
        let options = '<option value="">Select Model</option>';

        if (allowedModels.length > 0) {
            // Remove duplicates based on model_id
            let uniqueModels = [];
            let modelIds = new Set();

            allowedModels.forEach(function (model) {
                if (!modelIds.has(model.model_id)) {
                    modelIds.add(model.model_id);
                    uniqueModels.push(model);
                }
            });

            // Sort models
            uniqueModels.sort(function (a, b) {
                return (a.model_name + ' ' + a.brand_name).localeCompare(b.model_name + ' ' + b.brand_name);
            });

            uniqueModels.forEach(function (model) {
                let selected = (model.model_id == currentModelId) ? 'selected' : '';
                options += `<option 
                value="${model.model_id}"
                data-brand="${model.brand_name}"
                data-brand-id="${model.brand_id}"
                data-description="${model.description || ''}"
                ${selected}
            >
                ${model.model_name} - ${model.brand_name}
            </option>`;
            });

            // Add "All Models" option
            options += `<option value="all">-- Show All Models --</option>`;
        } else {
            options = '<option value="">No models for this supplier</option>';
            options += `<option value="all">-- Show All Models --</option>`;
        }

        modelSelect.empty().append(options);

        // Handle "Show All Models" selection
        modelSelect.off('change.showall').on('change.showall', function () {
            if ($(this).val() === 'all') {
                resetModelOptions(row, 'all');
            }
        });
    }

    function resetSupplierOptions(row, mode) {
        let supplierSelect = row.find('.supplier-select');
        let options = '<option value="">Select Supplier (Optional)</option>';

        if (mode === 'all') {
            allSuppliers.forEach(function (supplier) {
                options += `<option value="${supplier.id}">${supplier.name}</option>`;
            });
        }

        supplierSelect.empty().append(options);
        supplierSelect.off('change.showall');
    }

    function resetModelOptions(row, mode) {
        let modelSelect = row.find('.model-select');
        let options = '<option value="">Select Model</option>';

        if (mode === 'all') {
            // Remove duplicates
            let uniqueModels = [];
            let modelIds = new Set();

            allModels.forEach(function (model) {
                if (!modelIds.has(model.model_id)) {
                    modelIds.add(model.model_id);
                    uniqueModels.push(model);
                }
            });

            // Sort models
            uniqueModels.sort(function (a, b) {
                return (a.model_name + ' ' + a.brand_name).localeCompare(b.model_name + ' ' + b.brand_name);
            });

            uniqueModels.forEach(function (model) {
                options += `<option 
                value="${model.model_id}"
                data-brand="${model.brand_name}"
                data-brand-id="${model.brand_id}"
                data-description="${model.description || ''}"
            >
                ${model.model_name} - ${model.brand_name}
            </option>`;
            });
        }

        modelSelect.empty().append(options);
        modelSelect.off('change.showall');
    }

    function addRow() {
        let index = $('#listTBody tr').length;

        let newRow = `
        <tr>
            <td class="text-center">${index + 1}</td>
            <td>
                <select class="form-control supplier-select" name="InventoryOrderRequest[${index}][supplier_id]" data-row="${index}">
                    <option value="">Select Supplier (Optional)</option>
<?php foreach ($supplierList as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
<?php endforeach; ?>
                </select>
            </td>
            <td>
                <select class="form-control model-select" name="InventoryOrderRequest[${index}][model_id]" data-row="${index}">
                    <option value="">Select Model</option>
<?php
// Remove duplicates for display
$uniqueModels = [];
$modelIds = [];
foreach ($allModels ?? $modelBrandList as $combo) {
    if (!in_array($combo['model_id'], $modelIds)) {
        $modelIds[] = $combo['model_id'];
        $uniqueModels[] = $combo;
    }
}
// Sort by model name
usort($uniqueModels, function ($a, $b) {
    return strcmp($a['model_name'] . ' ' . $a['brand_name'], $b['model_name'] . ' ' . $b['brand_name']);
});
foreach ($uniqueModels as $combo):
    ?>
                                <option 
                                    value="<?= $combo['model_id'] ?>"
                                    data-brand="<?= Html::encode($combo['brand_name']) ?>"
                                    data-brand-id="<?= $combo['brand_id'] ?>"
                                    data-description="<?= Html::encode($combo['description'] ?? '') ?>"
                                >
    <?= Html::encode($combo['model_name'] . ' - ' . $combo['brand_name']) ?>
                                </option>
<?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="text" class="form-control brand-input" readonly>
                <input type="hidden" name="InventoryOrderRequest[${index}][inventory_brand_id]" class="brand-id">
            </td>
            <td>
                <input type="text" class="form-control description-input" readonly>
            </td>
            <td>
                <input type="number"
                    name="InventoryOrderRequest[${index}][required_qty]"
                    class="form-control text-center quantity-input"
                    min="1"
                    required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fas fa-minus-circle"></i>
                </button>
            </td>
            <input type="hidden" name="InventoryOrderRequest[${index}][inventory_model_id]" class="model-id">
        </tr>
    `;

        $('#listTBody').append(newRow);
    }

    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();

        // update numbering
        $('#listTBody tr').each(function (index) {
            $(this).find('td:first').text(index + 1);
        });
    });
</script>