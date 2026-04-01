<?php

use yii\helpers\Html;

$isDeleted = ($model->is_deleted == 1);
$department = \frontend\models\common\RefUserDepartments::findOne($model->department_code);
?>
<tr <?= $isDeleted ? 'class="deleted-row"' : '' ?> id="tr_<?= $key ?>" data-index="<?= $key ?>" name="currencyValue">
    <td class="text-center">
        <?= $key + 1 ?>
    </td>
    <?php if (!$isView): ?>
        <td>
            <?php //= Html::activeHiddenInput($model, "[$key]inventory_id", ['class' => 'inventory-id-field']) ?>
            <?=
            $form->field($model, "[$key]department_code")->dropDownList(
                    $departmentList,
                    [
                        'class' => 'form-control department-select',
                        'prompt' => 'Select Department',
                        'value' => $department ? $department->department_name : '-'
                    ]
            )->label(false)
            ?>
        </td>
        <td class="supplier-cell">
            <?=
            $form->field($model, "[$key]supplier_name")->textInput([
                'class' => 'form-control supplier-field'
            ])->label(false)
            ?>
        </td>
        <td class="brand-cell">
            <?=
            $form->field($model, "[$key]brand_name")->textInput([
                'class' => 'form-control brand-field'
            ])->label(false)
            ?>
        </td>
        <td class="model-cell">
            <?=
            $form->field($model, "[$key]model_name")->textInput([
                'class' => 'form-control model-field'
            ])->label(false)
            ?>
        </td>
        <td>
            <?=
            $form->field($model, "[$key]item_description")->textarea([
                'class' => 'form-control',
                'required' => true,
                'readonly' => false
            ])->label(false)
            ?>
        </td>
        <td>
            <?=
            $form->field($model, "[$key]quantity")->input('number', [
                'class' => 'form-control text-center',
                'required' => true,
                'oninput' => 'updateTotal(this)',
                'readonly' => false
            ])->label(false)
            ?>
        </td>
        <td>
            <?=
            $form->field($model, "[$key]currency")->dropDownList(
                    $currencyList
            )->label(false)
            ?>
        </td>
        <td>
            <?=
            $form->field($model, "[$key]unit_price")->input('number', [
                'class' => 'form-control text-right',
                'required' => true,
                'oninput' => 'updateTotal(this)',
                'step' => 'any',
                'min' => '0.01',
                'readonly' => false,
                'value' => \common\models\myTools\MyFormatter::asDecimal2($model->unit_price)
            ])->label(false)
            ?>
        </td>
        <td>
            <?=
            $form->field($model, "[$key]total_price")->input('number', [
                'readonly' => true,
                'class' => 'form-control text-right',
                'required' => true,
                'value' => \common\models\myTools\MyFormatter::asDecimal2($model->total_price)
            ])->label(false)
            ?>
        </td>
        <td>
            <?=
            $form->field($model, "[$key]purpose_or_function")->textarea([
                'class' => 'form-control',
                'readonly' => false,
                'required' => true
            ])->label(false)
            ?> 
        </td>
        <td>
            <?=
            $form->field($model, "[$key]remark")->textInput([
                'class' => 'form-control text-left',
                'readonly' => true
            ])->label(false)
            ?>
        </td>
        <td>
            <input type="hidden" name="[<?= $key ?>]toDelete" id="toDelete-<?= $key ?>" value="0">

            <?php $dataId = isset($model->item_id) ? $model->item_id : 'null'; ?>
            <?php if (!$isView): ?>
                <?php if ($moduleIndex === 'personal'): ?>
                    <?php if ($isUpdate): ?>
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                           onclick="markDelete(<?= $dataId ?>, <?= $key ?>)">
                            <i class="fas fa-minus-circle"></i>
                        </a>
                    <?php else: ?>
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm" 
                           onclick="removeRow(<?= $key ?>)">
                            <i class="fas fa-minus-circle"></i> 
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <?php
    else:
        ?>
        <td><?= $department ? $department->department_name : '-' ?></td>
        <td><?= $model->supplier_name === null ? "-" : $model->supplier_name ?></td>
        <td><?= $model->brand_name === null ? "-" : $model->brand_name ?></td>
        <td><?= $model->model_name === null ? "-" : $model->model_name ?></td>
        <td><?= $model->item_description ?></td>
        <td class="text-center"><?= $model->quantity ?></td>
        <td class="text-center"><?= $model->currency ?></td>
        <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->unit_price) ?></td>
        <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->total_price) ?></td>
        <td class="text-left"><?= $model->purpose_or_function ?></td>
        <?=
        $this->render('_viewSuperiorRemark', [
            'worklist' => $worklists[$key],
            'master' => $master,
            'model' => $model,
        ]);
        ?>
    <?php endif; ?>
</tr>