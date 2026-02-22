<?php
$isDeleted = ($model->is_deleted == 1);
?>
<tr <?= $isDeleted ? 'class="deleted-row"' : '' ?> id="tr_<?= $key ?>" data-index="<?= $key ?>" name="currencyValue">
    <td class="text-center"><?= $key + 1 ?></td>
    <td><?= $model->department_name === null ? "-" :  $model->department_name ?></td>
    <td><?= $model->supplier_name === null ? "-" :  $model->supplier_name ?></td>
    <td><?= $model->brand_name === null ? "-" :  $model->brand_name ?></td>
    <td><?= $model->model_name === null ? "-" :  $model->model_name ?></td>
    <td><?= $model->model_group === null ? "-" :  $model->model_group ?></td>
    <td><?= $model->item_description ?></td>
    <td class="text-center"><?= $model->quantity ?></td>
    <td class="text-center"><?= $model->currency ?></td>
    <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->unit_price) ?></td>
    <td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->total_price) ?></td>
    <td class="text-left"><?= $model->purpose_or_function ?></td>
    <?php 
    if (!$hasSuperiorUpdate): ?>
        <?=
        $this->render('_superiorRemark', [
            'worklist' => $worklists[$model->item_id],
            'key' => $model->item_id,
            'form' => $form,
            'model' => $model,
            'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist()
        ])
        ?>
    <?php else: ?>
        <?=
        $this->render('_viewSuperiorRemark', [
            'worklist' => $worklists[$model->item_id],
            'master' => $master,
            'model' => $model,
            'currencyList' => \frontend\models\common\RefCurrencies::getCurrencyActiveDropdownlist()
        ]);
        ?>
    <?php endif; ?>
</tr>

