<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use frontend\models\bom\BomMaster;
use frontend\models\inventory\InventoryModel;
use frontend\models\inventory\InventoryBrand;

/* @var $this yii\web\View */
/* @var $buffer array */
/* @var $bomMasterId int */
/* @var $errors array */

$bomMaster = BomMaster::findOne($bomMasterId);
$this->title = 'Confirm and Submit BOM Details';

$panel = $bomMaster->productionPanel;
$production = $panel->projProdMaster;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/production/production/view-production-main', 'id' => $production->id]];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['/bom/index', 'productionPanelId' => $panel->id]];
$this->params['breadcrumbs'][] = $this->title;

// Get all active models and brands for dropdowns
$allModels = InventoryModel::find()
        ->where(['active_sts' => 1])
        ->orderBy(['type' => SORT_ASC])
        ->all();

$allBrands = InventoryBrand::find()
        ->where(['active_sts' => 1])
        ->orderBy(['name' => SORT_ASC])
        ->all();
?>

<h4><?= Html::encode($this->title) ?></h4>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <h5>
            <i class="fas fa-exclamation-triangle text-danger"></i>
            Duplicate Items Detected
        </h5><small>The following items appear more than once in your submission. Please remove duplicate entries before proceeding.</small>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        
    </div>
<?php endif; ?>


<?php if (!empty($warnings)): ?>
    <div class="alert alert-warning">
        <h5>
            <i class="fas fa-exclamation-circle"></i>
            Items Not Found in Inventory
        </h5>
        <small>
            The following items are not currently in inventory. You can proceed with saving, but these items will require a Pre-Requisition submission to be added to the system.
        </small>
        <ul class="mb-0">
            <?php foreach ($warnings as $index => $warning): ?>
                <li>
                    <strong>Row <?= $index + 1 ?>:</strong>
                    <?= Html::encode($warning) ?>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
<?php endif; ?>

<?php $form = ActiveForm::begin(['action' => ['save-bom-details', 'bomMasterId' => $bomMasterId]]); ?>

<div class="table-responsive">
    <table class="table table-bordered" id="bom-table">
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 20%;">Model/Type <span class="text-danger">*</span></th>
                <th style="width: 15%;">Brand <span class="text-danger">*</span></th>
                <th style="width: 25%;">Description</th>
                <th style="width: 10%;">Quantity <span class="text-danger">*</span></th>
                <th style="width: 20%;">Remark</th>
                <th style="width: 7%;">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buffer as $index => $row): ?>
                <tr data-index="<?= $index ?>">
                    <td class="text-right px-2 pt-1">
                        <?= $index + 1 ?>
                    </td>
                    <td class="p-1">
                        <!-- Searchable Model Type Dropdown -->
                        <div class="searchable-dropdown-wrapper">
                            <input type="text" 
                                   class="form-control model-search" 
                                   data-index="<?= $index ?>"
                                   value="<?= Html::encode($row['model_type']) ?>"
                                   placeholder="Type to search..."
                                   readonly>
                            <input type="hidden" 
                                   name="BomDetails[model_type][<?= $index ?>]" 
                                   class="model-hidden"
                                   value="<?= Html::encode($row['model_type']) ?>">
                            <div class="dropdown-list model-list" data-index="<?= $index ?>">
                                <?php foreach ($allModels as $model): ?>
                                    <div class="dropdown-item" 
                                         data-value="<?= Html::encode($model->type) ?>"
                                         data-id="<?= $model->id ?>"
                                         data-brand-id="<?= $model->inventory_brand_id ?>"
                                         data-description="<?= Html::encode($model->description) ?>">
                                             <?= Html::encode($model->type) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php if (isset($errors[$index])): ?>
                            <small class="text-danger"><?= Html::encode($errors[$index]) ?></small>
                        <?php endif; ?>
                    </td>
                    <td class="p-1">
                        <!-- Brand Display (Auto-filled) -->
                        <input type="text" 
                               class="form-control brand-display" 
                               value="<?= Html::encode($row['brand']) ?>"
                               readonly>
                        <input type="hidden" 
                               name="BomDetails[brand][<?= $index ?>]" 
                               class="brand-hidden"
                               value="<?= Html::encode($row['brand']) ?>">
                    </td>
                    <td class="p-1">
                        <input type="text" 
                               name="BomDetails[description][<?= $index ?>]" 
                               class="form-control description-input"
                               value="<?= Html::encode($row['description']) ?>">
                    </td>
                    <td class="p-1">
                        <input type="number" 
                               name="BomDetails[quantity][<?= $index ?>]" 
                               class="form-control text-right"
                               value="<?= Html::encode($row['quantity']) ?>"
                               min="1"
                               step="1"
                               required>
                    </td>
                    <td class="p-1">
                        <input type="text" 
                               name="BomDetails[remark][<?= $index ?>]" 
                               class="form-control"
                               value="<?= Html::encode($row['remark']) ?>">
                    </td>
                    <td class="text-center p-1">
                        <button type="button" class="btn btn-danger btn-sm delete-row">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="form-group mt-3">
    <?=
    Html::submitButton(
            'Check Inventory Status',
            [
                'class' => 'btn btn-primary',
                'name' => 'action_mode',
                'value' => 'check',
                'id' => 'btn-check'
            ]
    )
    ?>

    <?=
    Html::submitButton(
            'Save and Proceed',
            [
                'class' => 'btn btn-success',
                'name' => 'action_mode',
                'value' => 'save',
                'id' => 'btn-save',
                'style' => 'display:none'
            ]
    )
    ?>

    <?=
    Html::a(
            'Cancel',
            ['index', 'productionPanelId' => $panel->id],
            ['class' => 'btn btn-danger']
    )
    ?>
</div>


<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {

// AFTER CHECK, SHOW SAVE BUTTON IF NO DUPLICATE ERROR
<?php if (!empty($warnings)): ?>
            $('#btn-save').show();
<?php endif; ?>

        // Store brand data for quick lookup
        var brandData = <?=
json_encode(array_reduce($allBrands, function ($carry, $brand) {
            $carry[$brand->id] = $brand->name;
            return $carry;
        }, []))
?>;

        // Initialize searchable dropdowns
        function initSearchableDropdown(index) {
            var $row = $('tr[data-index="' + index + '"]');
            var $modelSearch = $row.find('.model-search');
            var $modelHidden = $row.find('.model-hidden');
            var $modelList = $row.find('.model-list');
            var $brandDisplay = $row.find('.brand-display');
            var $brandHidden = $row.find('.brand-hidden');
            var $descriptionInput = $row.find('.description-input');

            // Show dropdown on click
            $modelSearch.on('click', function () {
                $modelList.show();
                filterModelItems('', index);
            });

            // Filter on keyup
            var searchBuffer = '';
            var searchTimeout;

            $modelSearch.on('keydown', function (e) {
                var allowedKeys = [8, 9, 13, 27, 38, 40];
                if (allowedKeys.indexOf(e.keyCode) === -1 && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                    var char = String.fromCharCode(e.keyCode);
                    searchBuffer += char.toLowerCase();
                    filterModelItems(searchBuffer, index);
                    $modelList.show();

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function () {
                        searchBuffer = '';
                    }, 1000);
                }
            });

            // Handle item selection
            $modelList.on('click', '.dropdown-item', function () {
                var modelType = $(this).data('value');
                var modelId = $(this).data('id');
                var brandId = $(this).data('brand-id');
                var description = $(this).data('description');

                // Set model
                $modelSearch.val(modelType);
                $modelHidden.val(modelType);

                // Auto-fill brand
                if (brandId && brandData[brandId]) {
                    $brandDisplay.val(brandData[brandId]);
                    $brandHidden.val(brandData[brandId]);
                } else {
                    $brandDisplay.val('');
                    $brandHidden.val('');
                }

                // Auto-fill description
                if (description) {
                    $descriptionInput.val(description);
                }

                $modelList.hide();

                // Remove error highlighting
                $row.removeClass('table-danger');
            });
        }

        // Filter model items
        function filterModelItems(searchTerm, index) {
            var $list = $('.model-list[data-index="' + index + '"]');
            var $items = $list.find('.dropdown-item');

            if (!searchTerm) {
                $items.show();
                return;
            }

            var term = searchTerm.toLowerCase();
            $items.each(function () {
                var text = $(this).data('value').toLowerCase();
                if (text.indexOf(term) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Hide dropdown when clicking outside
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.searchable-dropdown-wrapper').length) {
                $('.dropdown-list').hide();
            }
        });

        // Initialize all dropdowns
<?php foreach ($buffer as $index => $row): ?>
            initSearchableDropdown(<?= $index ?>);
<?php endforeach; ?>

        // Delete row functionality - FIXED VERSION
        $('.delete-row').on('click', function () {
            if (confirm('Are you sure you want to delete this row?')) {
                $(this).closest('tr').remove();

                // Re-index all rows and update input names
                $('#bom-table tbody tr').each(function (newIndex) {
                    var $row = $(this);

                    // Update visual row number
                    $row.find('td:first').text(newIndex + 1);

                    // Update data-index
                    $row.attr('data-index', newIndex);

                    // Update all input names with new index
                    $row.find('input[name*="model_type"]').attr('name', 'BomDetails[model_type][' + newIndex + ']');
                    $row.find('input[name*="brand"]').attr('name', 'BomDetails[brand][' + newIndex + ']');
                    $row.find('input[name*="description"]').attr('name', 'BomDetails[description][' + newIndex + ']');
                    $row.find('input[name*="quantity"]').attr('name', 'BomDetails[quantity][' + newIndex + ']');
                    $row.find('input[name*="remark"]').attr('name', 'BomDetails[remark][' + newIndex + ']');

                    // Update data-index attributes for dropdowns
                    $row.find('.model-search').attr('data-index', newIndex);
                    $row.find('.model-list').attr('data-index', newIndex);
                });
            }
        });

        // Form validation
        $('form').on('submit', function (e) {
            var hasError = false;

            $('#bom-table tbody tr').each(function (index) {
                var $modelHidden = $(this).find('.model-hidden');
                var $brandHidden = $(this).find('.brand-hidden');
                var $quantity = $(this).find('input[name*="quantity"]');

                if (!$quantity.val() || $quantity.val() <= 0) {
                    alert('Please enter a valid quantity for all rows.');
                    hasError = true;
                    return false;
                }
            });

            if (hasError) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>

<style>
    #btn-save {
        animation: pulse 1.2s infinite;
    }

    /* Searchable Dropdown Styles */
    .searchable-dropdown-wrapper {
        position: relative;
    }

    .searchable-dropdown-wrapper .model-search {
        cursor: pointer;
    }

    .searchable-dropdown-wrapper .dropdown-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        max-height: 200px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        background-color: white;
        z-index: 1000;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .searchable-dropdown-wrapper .dropdown-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
    }

    .searchable-dropdown-wrapper .dropdown-item:hover {
        background-color: #007bff;
        color: white;
    }

    .brand-display {
        background-color: #e9ecef;
        cursor: not-allowed;
    }

    .table-danger {
        background-color: #f8d7da !important;
    }
</style>