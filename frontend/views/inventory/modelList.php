<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;

$brandList = frontend\models\inventory\InventoryBrand::getAllDropDownBrandList();
?>
<div class="inventory-supplier-index">

    <?= $this->render('__stockNavBar', ['module' => 'superior', 'pageKey' => '4']) ?>

    <p>
        <?php
//        =
//        Html::a("Add New Model", "javascript:void(0)", [
//            'title' => "Add Model",
//            "value" => yii\helpers\Url::to(['add-new-model']),
//            "class" => "modalButton btn btn-success ml-1",
//            'data-modaltitle' => 'Add Model',
//        ]);
        ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        <?= Html::a('Add By Template', ['add-by-template-model'], ['class' => 'btn btn-success']) ?>
    </p>

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
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->type;
                }
            ],
            [
                'attribute' => 'inventory_brand_id',
                'value' => function ($model) {
                    return $model->inventoryBrand->name ?? null;
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'inventory_brand_id',
                        $brandList,
                        ['class' => 'form-control', 'prompt' => 'All']
                )
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->description;
                }
            ],
            [
                'attribute' => 'group',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->group;
                }
            ],
            [
                'attribute' => 'unit_type',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->unit_type;
                }
            ],
            [
                'attribute' => 'total_stock_on_hand',
                'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->total_stock_on_hand;
                }
            ],
            [
                'attribute' => 'total_stock_reserved',
                'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->total_stock_reserved;
                }
            ],
            [
                'attribute' => 'total_stock_available',
                'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->total_stock_available;
                }
            ],
            [
                'attribute' => 'active_sts',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return $model->active_sts == 0 ? 'No' : 'Yes';
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'active_sts',
                        [
                            '' => 'All',
                            '0' => 'No',
                            '1' => 'Yes'
                        ],
                        ['class' => 'form-control text-center']
                )
            ],
            [
                'attribute' => 'created_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->createdBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->updatedBy ? $model->updatedBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
                }
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return Html::a('View <i class="fa fa-eye"></i>', "javascript:void(0)", [
                        'title' => "View Model",
                        'value' => yii\helpers\Url::to(['view-model', 'id' => $model->id]),
                        'class' => 'modalButton btn btn-sm btn-success text-center',
                        'data-modaltitle' => 'View Model',
                    ]);
                }
            ],
        //'updated_at',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
