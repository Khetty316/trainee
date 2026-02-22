<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use common\models\User;

if ($page === "newItem") {
    $label = "New Item";
    $url = 'executive-new-item-ready-for-po-list';
} else {
    $label = "Reorder Item";
    $url = 'executive-reorder-item-ready-for-po-list';
}

$this->title = $pr->inventorySupplier->name;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - ' . $label];
$this->params['breadcrumbs'][] = ['label' => 'Ready for Purchase Order', 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->params['breadcrumbs'][] = ['label' => "View"];
\yii\web\YiiAsset::register($this);
?>
<style>
    .deleted-row {
        text-decoration: line-through;
        opacity: 0.6;
        color: #6c757d;
    }
    .total-amount-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .table-sm.font-weight-bold {
        font-size: 14px;
    }
</style>
<div class="prereq-form-master-view mb-5">
    <div class="alert alert-info border-left-primary mb-4">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <strong class="text-muted">Supplier:</strong>
                        <span class="ml-2 text-dark font-weight-bold"><?= Html::encode($pr->inventorySupplier->name) ?></span>
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Created By:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?=Html::encode($pr->createdBy->fullname) ?>
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Created At:</strong>
                        <span class="ml-2 text-dark font-weight-bold">
                            <?= MyFormatter::asDateTime_ReaddmYHi($pr->created_at) ?>
                        </span>                      
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered mb-0" id="item_table">
            <thead class="table-dark">
                <tr>
                    <th rowspan="2" class="text-center" width="4%">No.</th>
                    <th rowspan="2" width="8%">Department</th>
                    <th rowspan="2" width="8%">Brand</th>
                    <th rowspan="2" width="8%">Model</th>
                    <th rowspan="2" width="8%">Model Group</th>
                    <th rowspan="2" width="18%">Item Description</th>
                    <th rowspan="2" width="6%">Quantity</th>
                    <th rowspan="2" width="6%">Unit Type</th>
                    <th rowspan="2" class="text-center" width="3%">Currency</th>
                    <th rowspan="2" class="text-right" width="9%">Unit Price</th>
                    <th rowspan="2" class="text-right" width="9%">Total Price</th>
                </tr>
            </thead>

            <tbody id="listTBody">
                <?php foreach ($prItems as $index => $item): 
                    $department = \frontend\models\common\RefUserDepartments::findOne($item->department_code);
                    ?>
                    <tr>
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td><?= $department->department_name ?? '-' ?></td>
                        <td><?= Html::encode($item->brand->name ?? '-') ?></td>
                        <td><?= Html::encode($item->model_type ?? '-') ?></td>
                        <td><?= Html::encode($item->model_group ?? '-') ?></td>
                        <td><?= Html::encode($item->model_description ?? '-') ?></td>
                        <td class="text-center"><?= Html::encode($item->quantity ?? '-') ?></td>
                        <td class="text-center"><?= Html::encode($item->unit_type ?? '-') ?></td>
                        <td class="text-center"><?= Html::encode($item->currency->currency_code ?? '-') ?></td>
                        <td class="text-right"><?= number_format($item->unit_price ?? 0, 2) ?></td>
                        <td class="text-right"><?= number_format($item->total_price ?? 0, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
            </tfoot>
        </table>
    </div>
</div>
