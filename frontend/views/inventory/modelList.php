<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
}

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $pageName;

$brandList = frontend\models\inventory\InventoryBrand::getAllDropDownBrandList();
?>
<div class="inventory-model-index">

    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => '4']) ?>

    <p>
        <?php if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'projcoorStock' || $moduleIndex === 'maintenanceHeadStock') { ?>
            <?=
            Html::a("Add New Model", "javascript:void(0)", [
                'title' => "Add Model",
                "value" => yii\helpers\Url::to(['add-new-model', 'type' => $moduleIndex]),
                "class" => "modalButton btn btn-success ml-1",
                'data-modaltitle' => 'Add Model',
            ]);
            ?>        
            <?= Html::a('Upload Template', ['add-by-template-model', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
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
                'value' => function ($model) use ($moduleIndex) {
                    $image = '';
                    if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'projcoorStock' || $moduleIndex === 'maintenanceHeadStock') {
                        $typeLink = Html::a(
                                $model->type,
                                "javascript:void(0)",
                                [
                                    'title' => "View Brand",
                                    'value' => yii\helpers\Url::to(['view-model', 'id' => $model->id, 'type' => $moduleIndex]),
                                    'class' => 'modalButton',
                                    'data-modaltitle' => 'View Model',
                                ]
                        );
                    } else {
                        if ($model->image !== null) {
                            $image = Html::a(
                                    "<i class='far fa-file-alt fa-lg'></i>",
                                    "javascript:void(0);",
                                    [
                                        'title' => "View Image",
                                        'value' => "/inventory/inventory/get-model-image?filename=" . urlencode($model->image),
                                        'class' => "docModal text-primary"
                                    ]
                            );
                        }
                        $typeLink = $model->type;
                    }

                    return '<div class="d-flex justify-content-between align-items-center">'
                    . '<span>' . $typeLink . '</span>'
                    . '<span>' . $image . '</span>'
                    . '</div>';
                }
            ],
            [
                'attribute' => 'brand_name',
                'value' => function ($model) {
                    return $model->brand_name ?? null;
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'brand_name',
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
                    return $model->active_sts == 2 ? 'Yes' : 'No';
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
                'attribute' => 'created_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->created_by_name) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->updated_by_name ? $model->updated_by_name . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
                }
            ],
//            [
//                'format' => 'raw',
//                'contentOptions' => ['class' => 'text-center'],
//                'value' => function ($model) {
//                    return Html::a('View <i class="fa fa-eye"></i>', "javascript:void(0)", [
//                        'title' => "View Model",
//                        'value' => yii\helpers\Url::to(['view-model', 'id' => $model->id]),
//                        'class' => 'modalButton btn btn-sm btn-success text-center',
//                        'data-modaltitle' => 'View Model',
//                    ]);
//                }
//            ],
        //'updated_at',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

    <?=
    $this->render('/_docModal')
    ?> 
</div>
