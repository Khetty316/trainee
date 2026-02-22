<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;

/* @var $model mixed */
/* @var $index integer */
/* @var $hasSuperiorUpdate boolean */
/* @var $worklist mixed */
/* @var $departmentList array */
/* @var $currencyList array */
$isDeleted = ($model->is_deleted == 1);
?>
<tr <?= $isDeleted ? 'class="deleted-row"' : '' ?> 
    id="tr_<?= $index ?>" 
    data-index="<?= $index ?>">
    <td class="text-center"><?= $index + 1 ?></td>
    <td><?= $departmentList[$model->department_code] ?? '-' ?></td>
    <td><?= Html::encode($model->supplier_name ?? '-') ?></td>
    <td><?= Html::encode($model->brand_name ?? '-') ?></td>
    <td><?= Html::encode($model->model_name ?? '-') ?></td>
    <td><?= Html::encode($model->model_group ?? '-') ?></td>
    <td><?= Html::encode($model->item_description ?? '-') ?></td>
    <td class="text-center"><?= Html::encode($model->quantity ?? '-') ?></td>
    <td class="text-center"><?= Html::encode($model->model_unit_type ?? '-') ?></td>
    <td class="text-center"><?= Html::encode($model->currency ?? '-') ?></td>
    <td class="text-right"><?= number_format($model->unit_price ?? 0, 2) ?></td>
    <td class="text-right"><?= number_format($model->total_price ?? 0, 2) ?></td>
    <td><?= Html::encode($model->purpose_or_function ?? '-') ?></td>

<?php if ($hasSuperiorUpdate && $worklist): ?>
        <!-- Superior update columns -->
        <td class="text-center"><?= $worklist->quantity_approved ?? '-' ?></td>
        <td class="text-center"><?= $worklist->currency_approved ?? '-' ?></td>
        <td class="text-right"><?= number_format($worklist->unit_price_approved ?? 0, 2) ?></td>
        <td class="text-right"><?= number_format($worklist->total_price_approved ?? 0, 2) ?></td>
        <td><?= Html::encode($worklist->remark ?? '-') ?></td>
<?php else: ?>
        <!-- Regular view columns -->
        <td><?= Html::encode($model->remark ?? '-') ?></td>
        <td></td>
<?php endif; ?>
</tr>