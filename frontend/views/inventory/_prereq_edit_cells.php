<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $form ActiveForm */
/* @var $model mixed */
/* @var $index integer */
/* @var $moduleIndex string */
/* @var $hasSuperiorUpdate boolean */
/* @var $worklist mixed */
/* @var $departmentList array */
/* @var $supplierList array */
/* @var $brandList array */
/* @var $currencyList array */

// Get values from model
$departmentValue = $model->department_code ?? '';
$supplierValue = $model->supplier_name ?? '';
$brandValue = $model->brand_name ?? '';
$currencyValue = $model->currency ?? '';

// Find the KEY for supplier value (convert name to ID)
if (!empty($supplierValue)) {
    $supplierKey = array_search($supplierValue, $supplierList);
    $supplierValue = ($supplierKey !== false) ? $supplierKey : $supplierValue;
}

// Find the KEY for brand value (convert name to ID)
if (!empty($brandValue)) {
    $brandKey = array_search($brandValue, $brandList);
    $brandValue = ($brandKey !== false) ? $brandKey : $brandValue;
}

// Check if values exist in dropdown lists
$departmentExists = isset($departmentList[$departmentValue]);
$supplierExists = array_search($model->supplier_name ?? '', $supplierList) !== false;
$brandExists = array_search($model->brand_name ?? '', $brandList) !== false;
$currencyExists = array_key_exists($currencyValue, $currencyList);
$isDeleted = ($model->is_deleted == 1);
?>
<tr <?= $isDeleted ? 'class="deleted-row"' : '' ?> 
    id="tr_<?= $index ?>" 
    data-index="<?= $index ?>">
    <td class="text-center"><?= $index + 1 ?></td>

    <td>
        <?=
        Html::dropDownList(
                "VPrereqFormMasterDetail[$index][department_code]",
                $departmentValue,
                $departmentList,
                [
                    'class' => 'form-control department-select',
                    'prompt' => 'Select',
                    'required' => true
                ]
        )
        ?>
    </td>

    <td class="supplier-cell">
        <?php if ($moduleIndex === 'inventory'): ?>
            <?php
            // Only use suppliers from the list - no custom values
            $supplierOptions = $supplierList;
            $currentSupplierName = $model->supplier_name ?? '';

            // Check if current supplier exists in list (check both keys AND values)
            $supplierExistsAsKey = array_key_exists($currentSupplierName, $supplierOptions);
            $supplierExistsAsValue = in_array($currentSupplierName, $supplierOptions, true);
            $supplierExists = $supplierExistsAsKey || $supplierExistsAsValue;

            // If supplier exists as value but not as key, find the key
            $selectedSupplierKey = '';
            if (!$supplierExistsAsKey && $supplierExistsAsValue) {
                $selectedSupplierKey = array_search($currentSupplierName, $supplierOptions, true);
            } elseif ($supplierExistsAsKey) {
                $selectedSupplierKey = $currentSupplierName;
            }

            $supplierNotInList = !empty($currentSupplierName) && !$supplierExists;
            ?>

            <?php if ($supplierNotInList): ?>
                <!-- Show warning if supplier is not in list -->
                <div class="alert alert-warning alert-sm mb-1 p-1">
                    <small><strong>Warning:</strong> Supplier "<?= Html::encode($currentSupplierName) ?>" not found in inventory.</small>
                </div>
            <?php endif; ?>

            <?=
            Html::dropDownList(
                    "VPrereqFormMasterDetail[$index][supplier_name]",
                    $selectedSupplierKey,
                    $supplierOptions,
                    [
                        'class' => 'form-control supplier-select',
                        'prompt' => 'Select Supplier',
                        'required' => true,
                        'data-original-value' => $currentSupplierName,
                        'data-supplier-exists' => $supplierExists ? 'true' : 'false'
                    ]
            )
            ?>
        <?php else: ?>
            <?=
            Html::textInput(
                    "VPrereqFormMasterDetail[$index][supplier_name]",
                    $model->supplier_name ?? '',
                    ['class' => 'form-control supplier-field', 'placeholder' => 'Supplier name', 'required' => true]
            )
            ?>
        <?php endif; ?>
    </td>

    <td class="brand-cell">
        <?php if ($moduleIndex === 'inventory'): ?>
            <?php
            // Only use brands from the list - no custom values
            $brandOptions = $brandList;
            $currentBrandName = $model->brand_name ?? '';

            // Check if current brand exists in list (check both keys AND values)
            $brandExistsAsKey = array_key_exists($currentBrandName, $brandOptions);
            $brandExistsAsValue = in_array($currentBrandName, $brandOptions, true);
            $brandExists = $brandExistsAsKey || $brandExistsAsValue;

            // If brand exists as value but not as key, find the key
            $selectedBrandKey = '';
            if (!$brandExistsAsKey && $brandExistsAsValue) {
                $selectedBrandKey = array_search($currentBrandName, $brandOptions, true);
            } elseif ($brandExistsAsKey) {
                $selectedBrandKey = $currentBrandName;
            }

            $brandNotInList = !empty($currentBrandName) && !$brandExists;
            ?>

            <?php if ($brandNotInList): ?>
                <!-- Show warning if brand is not in list -->
                <div class="alert alert-warning alert-sm mb-1 p-1">
                    <small><strong>Warning:</strong> Brand "<?= Html::encode($currentBrandName) ?>" not found in inventory.</small>
                </div>
            <?php endif; ?>

            <?=
            Html::dropDownList(
                    "VPrereqFormMasterDetail[$index][brand_name]",
                    $selectedBrandKey,
                    $brandOptions,
                    [
                        'class' => 'form-control brand-select',
                        'prompt' => 'Select Brand',
                        'required' => true,
                        'data-original-value' => $currentBrandName,
                        'data-brand-exists' => $brandExists ? 'true' : 'false'
                    ]
            )
            ?>
        <?php else: ?>
            <?=
            Html::textInput(
                    "VPrereqFormMasterDetail[$index][brand_name]",
                    $model->brand_name ?? '',
                    ['class' => 'form-control brand-field', 'placeholder' => 'Brand name', 'required' => true]
            )
            ?>
        <?php endif; ?>
    </td>

    <td class="model-cell">
        <?=
        Html::textInput(
                "VPrereqFormMasterDetail[$index][model_name]",
                $model->model_name ?? '',
                ['class' => 'form-control model-field', 'placeholder' => 'Model type', 'required' => true]
        )
        ?>
        <div class="duplicate-warning text-danger" style="display:none; font-size: 11px; margin-top: 2px;">
            ⚠ This item already exists in inventory
        </div>
    </td>

    <td>
        <?=
        Html::textInput(
                "VPrereqFormMasterDetail[$index][model_group]",
                $model->model_group ?? '',
                ['class' => 'form-control', 'placeholder' => 'Model group']
        )
        ?>
    </td>

    <td>
        <?=
        Html::textarea(
                "VPrereqFormMasterDetail[$index][item_description]",
                $model->item_description ?? '',
                ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Description']
        )
        ?>
    </td>

    <td>
        <?=
        Html::input(
                'number',
                "VPrereqFormMasterDetail[$index][quantity]",
                $model->quantity ?? '',
                [
                    'class' => 'form-control text-center quantity-input',
                    'oninput' => 'PrereqForm.updateTotal(this)',
                    'min' => '1',
                    'required' => true
                ]
        )
        ?>
    </td>
    <td>
        <?=
        Html::textInput(
                "VPrereqFormMasterDetail[$index][unit_type]",
                $model->unit_type ?? '',
                ['class' => 'form-control', 'placeholder' => "UNIT"]
        )
        ?>
    </td>
    <td>
        <?=
        Html::dropDownList(
                "VPrereqFormMasterDetail[$index][currency]",
                $currencyValue,
                $currencyList,
                [
                    'class' => 'form-control currency-select',
                    'prompt' => 'Select',
                    'id' => 'currency-' . $index,
                    'required' => true,
                    'onchange' => 'PrereqForm.buildCurrencyTotals()'
                ]
        )
        ?>
    </td>

    <td>
        <?=
        Html::input(
                'number',
                "VPrereqFormMasterDetail[$index][unit_price]",
                $model->unit_price ?? '',
                [
                    'class' => 'form-control text-right unit-price-input',
                    'step' => '0.01',
                    'min' => '0',
                    'oninput' => 'PrereqForm.updateTotal(this)',
                    'required' => true
                ]
        )
        ?>
    </td>

    <td>
        <?=
        Html::input(
                'number',
                "VPrereqFormMasterDetail[$index][total_price]",
                $model->total_price ?? '',
                [
                    'class' => 'form-control text-right total-price-input',
                    'readonly' => true,
                    'tabindex' => '-1'
                ]
        )
        ?>
    </td>

    <td>
        <?=
        Html::textarea(
                "VPrereqFormMasterDetail[$index][purpose_or_function]",
                $model->purpose_or_function ?? '',
                ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Purpose', 'required' => true]
        )
        ?>
    </td>

    <td>
        <?=
        Html::textInput(
                "VPrereqFormMasterDetail[$index][remark]",
                $model->remark ?? '',
                ['class' => 'form-control', 'readonly' => true]
        )
        ?>
    </td>

    <td class="text-center" style="vertical-align: middle;">
        <?= Html::hiddenInput("VPrereqFormMasterDetail[$index][id]", $model->item_id ?? '', ['id' => "item-id-$index"]) ?>
        <?= Html::hiddenInput("VPrereqFormMasterDetail[$index][item_reference_type]", $model->item_reference_type ?? '') ?>
        <?= Html::hiddenInput("VPrereqFormMasterDetail[$index][item_reference_id]", $model->item_reference_id ?? '') ?>
        <?= Html::hiddenInput("VPrereqFormMasterDetail[$index][toDelete]", '0', ['id' => "toDelete-$index"]) ?>

        <?php if ($moduleIndex === 'personal' || $moduleIndex === 'inventory'): ?>
            <button type="button" class="btn btn-danger btn-sm" onclick="PrereqForm.removeRow(<?= $index ?>)">
                <i class="fas fa-minus-circle"></i>
            </button>
        <?php endif; ?>
    </td>
</tr>
