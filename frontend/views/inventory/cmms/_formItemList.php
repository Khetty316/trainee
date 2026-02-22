<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\cmms\InventorySupplierCmms */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="inventory-supplier-cmms-form">

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-detail-form',
        'enableClientValidation' => false,
        'enableAjaxValidation' => false,
    ]); ?>

    <div class="card-body p-2 table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Supplier</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th class="text-right">Stock Level Minimum</th>
                    <th class="text-right">Quantity Stock</th>
                    <th>Active Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="item-table-body">
                <?php foreach ($itemList as $index => $item): ?>
                    <tr>
                        <td>
                            <?=
                            $form->field($item, "[$index]supplier_cmms_code", ['options' => ['class' => 'mb-0']])
                                ->dropDownList($supplierList, [
                                    'class' => 'form-control mb-0',
                                    'prompt' => 'Select Supplier'
                                ])
                                ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                            $form->field($item, "[$index]brand_cmms_code", ['options' => ['class' => 'mb-0']])
                                ->dropDownList($brandList, [
                                    'class' => 'form-control mb-0',
                                    'prompt' => 'Select Brand'
                                ])
                                ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                            $form->field($item, "[$index]model_cmms_code", ['options' => ['class' => 'mb-0']])
                                ->dropDownList($modelList, [
                                    'class' => 'form-control mb-0',
                                    'prompt' => 'Select Model'
                                ])
                                ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                            $form->field($item, "[$index]stock_level_min", ['options' => ['class' => 'mb-0']])
                                ->input('number', [
                                    'class' => 'form-control text-right',
                                    'step' => '1',
                                    'min' => '1',
                                    'required' => true,
                                ])
                                ->label(false)
                            ?>
                        </td>

                        <td>
                            <?=
                            $form->field($item, "[$index]quantity_stock", ['options' => ['class' => 'mb-0']])
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
                                ->dropDownList([1 => 'Active', 0 => 'Inactive'], ['class' => 'form-control'])
                                ->label(false)
                            ?>
                        </td>

                        <td>
                            <?= Html::activeHiddenInput($item, "[$index]id") ?>
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
        <?= Html::submitButton('Save', ['class' => 'btn btn-success float-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
$(document).ready(function() {
    // Remove row
    $(document).on('click', '.remove-row-btn', function(e) {
        e.preventDefault();
        let tableBody = $('#item-table-body');
        let rows = tableBody.find('tr');
        
        if (rows.length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
        } else {
            alert('At least one item is required');
        }
    });

    // Add row
    $('#add-row-btn').on('click', function() {
        addRow();
    });

    function addRow() {
        let tableBody = $('#item-table-body');
        let rows = tableBody.find('tr');
        let lastRow = rows.last();
        let newRow = lastRow.clone();
        let newIndex = rows.length;

        // Update all name/id attributes
        newRow.find('input, select').each(function() {
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
        
        // Update for attributes in labels
        newRow.find('label').each(function() {
            let forAttr = $(this).attr('for');
            if (forAttr) {
                forAttr = forAttr.replace(/-\d+-/, '-' + newIndex + '-');
                $(this).attr('for', forAttr);
            }
        });

        tableBody.append(newRow);
    }

    function reindexRows() {
        let tableBody = $('#item-table-body');
        let rows = tableBody.find('tr');

        rows.each(function(newIndex) {
            $(this).find('input, select, textarea, label').each(function() {
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
            });
        });
    }
});
</script>