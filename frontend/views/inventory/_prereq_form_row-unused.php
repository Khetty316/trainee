<?php

use yii\helpers\Html;

$isDeleted = ($model->is_deleted == 1);
?>

<tr <?= $isDeleted ? 'class="deleted-row"' : '' ?> id="tr_<?= $index ?>" data-index="<?= $index ?>">
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
        // Check if we need to add the current value if it's not in the list
        $supplierOptions = $supplierList;
        $currentSupplierName = $model->supplier_name ?? '';

        if (!empty($currentSupplierName) && !in_array($currentSupplierName, $supplierOptions)) {
            // Add the current value to options if it doesn't exist
            $supplierOptions[$currentSupplierName] = $currentSupplierName . ' (not in list)';
            $supplierValue = $currentSupplierName;
        }
        ?>

        <?=
        Html::dropDownList(
                "VPrereqFormMasterDetail[$index][supplier_name]",
                $supplierValue,
                $supplierOptions,
                [
                    'class' => 'form-control supplier-select',
                    'prompt' => 'Select',
                    'required' => true
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
        // Check if we need to add the current value if it's not in the list
        $brandOptions = $brandList;
        $currentBrandName = $model->brand_name ?? '';

        if (!empty($currentBrandName) && !in_array($currentBrandName, $brandOptions)) {
            // Add the current value to options if it doesn't exist
            $brandOptions[$currentBrandName] = $currentBrandName . ' (not in list)';
            $brandValue = $currentBrandName;
        }
        ?>

        <?=
        Html::dropDownList(
                "VPrereqFormMasterDetail[$index][brand_name]",
                $brandValue,
                $brandOptions,
                [
                    'class' => 'form-control brand-select',
                    'prompt' => 'Select',
                    'required' => true
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
            $model->model_unit_type ?? '',
            ['class' => 'form-control']
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
    <?= Html::hiddenInput("VPrereqFormMasterDetail[$index][toDelete]", '0', ['id' => "toDelete-$index"]) ?>

    <?php if ($moduleIndex === 'personal' || $moduleIndex === 'inventory'): ?>
        <button type="button" class="btn btn-danger btn-sm" onclick="PrereqForm.removeRow(<?= $index ?>)">
            <i class="fas fa-minus-circle"></i>
        </button>
    <?php endif; ?>
</td>
</tr>



