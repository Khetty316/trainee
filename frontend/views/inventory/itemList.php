<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'execStock') {
    $pageName = 'Inventory Master - Executive';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Inventory Master - Assistant';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Inventory Master - Project Coordinator';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Inventory Master - Head of Maintenance';
} else if ($moduleIndex === 'personalStock') {
    $pageName = 'Inventory Master - Personal';
}

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $pageName;
?>
<style>
    .grid-wrap {
        white-space: normal !important;
        word-break: break-word;
        text-align: left;
    }

    .w-500 {
        max-width: 500px;
    }

    .my-thead th {
        vertical-align: middle;
        padding: 8px 5px;
    }

    .table-responsive {
        overflow-x: auto;
    }
</style>

<div class="inventory-item-index">
    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => '1']) ?>

    <p>
        <?php if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'maintenanceHeadStock') { ?>
            <?= Html::a('Add Item <i class="fas fa-plus"></i>', ['add-new-item', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?type=' . $moduleIndex, ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-inventory'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>
    <?php
    $isPersonal = ($moduleIndex === 'personalStock');

    $columns = [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['style' => 'width: 50px;'],
        ],
        [
            'attribute' => 'inventory_code',
            'headerOptions' => ['style' => 'width: 150px; white-space: nowrap;'],
            'contentOptions' => ['style' => 'white-space: nowrap;'],
            'format' => 'raw',
            'value' => function ($model) use ($moduleIndex) {
                if (in_array($moduleIndex, ['execStock', 'assistStock', 'maintenanceHeadStock'])) {
                    return Html::a($model->inventory_code, [
                                'view-item-detail',
                                'id' => $model->inventory_id,
                                'type' => $moduleIndex
                    ]);
                }
                return $model->inventory_code;
            }
        ],
        [
            'attribute' => 'department_name',
            'label' => 'Department',
            'contentOptions' => ['class' => 'grid-wrap'],
            'value' => fn($model) => $model->department_name,
        ],
        [
            'attribute' => 'supplier_display',
            'contentOptions' => ['class' => 'grid-wrap'],
            'format' => 'raw',
            'value' => fn($model) => $model->supplier_display,
        ],
        [
            'attribute' => 'brand_display',
            'contentOptions' => ['class' => 'grid-wrap'],
            'format' => 'raw',
            'value' => fn($model) => $model->brand_display,
        ],
        [
            'attribute' => 'model_type',
            'label' => 'Model Type',
            'format' => 'raw',
            'value' => function ($model) {
                $image = '';

                if ($model->image !== null) {
                    $image = Html::a(
                            "<i class='far fa-file-alt fa-lg m-1'></i>",
                            "#",
                            [
                                'title' => "View Image",
                                'value' => "/inventory/inventory/get-model-image?filename=" . urlencode($model->image),
                                'class' => "docModal"
                            ]
                    );
                }

                return $model->model_type . $image;
            }
        ],
        [
            'attribute' => 'model_description',
            'label' => 'Description',
            'contentOptions' => ['class' => 'grid-wrap'],
        ],
        [
            'attribute' => 'group',
            'headerOptions' => ['style' => 'white-space: nowrap;'],
        ],
    ];

    if (!$isPersonal) {

        $columns[] = [
            'attribute' => 'stock_in',
            'contentOptions' => ['style' => 'text-align:center'],
        ];

        $columns[] = [
            'attribute' => 'stock_on_hand',
            'contentOptions' => ['style' => 'text-align:center'],
        ];

        $columns[] = [
            'attribute' => 'stock_reserved',
            'contentOptions' => ['style' => 'text-align:center'],
        ];

        $columns[] = [
            'attribute' => 'stock_out',
            'contentOptions' => ['style' => 'text-align:center'],
        ];

        $columns[] = [
            'attribute' => 'stock_available',
            'contentOptions' => ['style' => 'text-align:center'],
        ];

        $columns[] = [
            'attribute' => 'qty_pending_receipt',
            'contentOptions' => ['style' => 'text-align:center'],
        ];
    }


    $columns[] = [
        'attribute' => 'active_sts',
        'contentOptions' => ['style' => 'text-align:center'],
        'value' => fn($model) => $model->active_sts == 2 ? 'Yes' : 'No',
        'filter' => Html::activeDropDownList(
                $searchModel,
                'active_sts',
                [
                    '' => 'All',
                    '1' => 'No',
                    '2' => 'Yes'
                ],
                ['class' => 'form-control text-center']
        )
    ];

    if (!$isPersonal) {

        $columns[] = [
            'attribute' => 'created_by_fullname',
            'label' => 'Created By',
            'contentOptions' => ['class' => 'grid-wrap'],
            'value' => function ($model) {
                return $model->created_by_fullname . " @ " .
                MyFormatter::asDateTime_ReaddmYHi($model->created_at);
            }
        ];

        $columns[] = [
            'attribute' => 'updated_by_fullname',
            'label' => 'Updated By',
            'contentOptions' => ['class' => 'grid-wrap'],
            'value' => function ($model) {
                return $model->updated_by_fullname ? $model->updated_by_fullname . " @ " .
                MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
            }
        ];
    }
    ?>

    <div class="table-responsive">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => $columns,
    ]);
    ?>
    </div>

        <?=
        $this->render('/_docModal')
        ?> 
</div>