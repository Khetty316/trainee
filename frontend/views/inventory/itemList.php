<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
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

<div class="inventory-supplier-index">

    <?= $this->render('__stockNavBar', ['module' => 'superior', 'pageKey' => '1']) ?>

    <p>
        <?= Html::a('Add Item', ['add-new-item'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

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
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'headerOptions' => ['style' => 'width: 50px;'],
                ],
                [
                    'attribute' => 'inventory_code',
                    'headerOptions' => ['style' => 'width: 150px; white-space: nowrap;'],
                    'contentOptions' => ['style' => 'white-space: nowrap;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->inventory_code, ['view-item-detail', 'id' => $model->inventory_id]);
                    }
                ], 
                [
                    'attribute' => 'department_name',
                    'label' => 'Department',
                    'headerOptions' => ['style' => 'width: 120px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => fn($model) => $model->department_name,
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'department_code', // Use department_code for filtering
                            \yii\helpers\ArrayHelper::map(
                                    frontend\models\common\RefUserDepartments::find()
                                            ->select(['code', 'department_name'])
                                            ->orderBy(['department_name' => SORT_ASC])
                                            ->all(),
                                    'code',
                                    'department_name'
                            ),
                            [
                                'class' => 'form-control',
                                'prompt' => 'All'
                            ]
                    )
                ],
                [
                    'attribute' => 'supplier_display',
                    'headerOptions' => ['style' => 'width: 300px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->supplier_display;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'supplier_display', [
                        'class' => 'form-control',
                    ])
                ],
                [
                    'attribute' => 'brand_display',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->brand_display;
                    },
                    'filter' => Html::activeTextInput($searchModel, 'brand_display', [
                        'class' => 'form-control',
                    ])
                ],
                [
                    'attribute' => 'model_type',
                    'label' => 'Model Type',
                    'headerOptions' => ['style' => 'width: 120px;'],
                    'contentOptions' => ['class' => 'col-sm-1'],
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
                    'headerOptions' => ['style' => 'width: 150px;'],
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => fn($model) => $model->model_description,
                ],
                [
                    'attribute' => 'group',
                    'headerOptions' => ['style' => 'width: 100px; white-space: nowrap;'],
                    'contentOptions' => ['style' => 'white-space: nowrap;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->group;
                    }
                ],
//                [
//                    'attribute' => 'minimum_qty',
//                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
//                    'contentOptions' => ['style' => 'text-align: center;'],
//                    'format' => 'raw',
//                    'value' => function ($model) {
//                        return $model->minimum_qty;
//                    }
//                ],
                        [
                    'attribute' => 'stock_in',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->stock_in;
                    }
                ],
                [
                    'attribute' => 'stock_on_hand',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->stock_on_hand;
                    }
                ],
                        [
                    'attribute' => 'stock_reserved',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->stock_reserved;
                    }
                ],
                        [
                    'attribute' => 'stock_out',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->stock_out;
                    }
                ],
                        [
                    'attribute' => 'stock_available',
                    'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->stock_available;
                    }
                ],
//                [
//                    'attribute' => 'required_qty',
//                    'label' => 'Required Qty',
//                    'headerOptions' => ['style' => 'width: 80px; text-align: center;'],
//                    'contentOptions' => ['style' => 'text-align: center;'],
//                    'format' => 'raw',
//                    'value' => function ($model) {
//                        return $model->required_qty;
//                    }
//                ],
                [
                    'attribute' => 'qty_pending_receipt',
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->qty_pending_receipt;
                    }
                ],
                [
                    'attribute' => 'active_sts',
                    'headerOptions' => ['style' => 'width: 90px;'],
                    'contentOptions' => ['class' => 'grid-wrap', 'style' => 'text-align: center;'],
                    'value' => function ($model) {
                        return $model->active_sts == 1 ? 'No' : 'Yes';
                    },
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
                ],
                [
                    'attribute' => 'created_by_fullname',
                    'label' => 'Created By',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => function ($model) {
                        return ($model->created_by_fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                ],
                [
                    'attribute' => 'updated_by_fullname',
                    'label' => 'Updated By',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => function ($model) {
                        return $model->updated_by_fullname ? $model->updated_by_fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
                    }
                ],
            ],
        ]);
        ?>
    </div>
    <?=
    $this->render('/_docModal')
    ?> 
</div>