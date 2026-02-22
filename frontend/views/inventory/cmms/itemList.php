<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\cmms\InventorySupplierCmmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventory Control - CMMS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-supplier-cmms-index">

    <?= $this->render('__inventoryCmmsNavBar', ['module' => 'superior', 'pageKey' => '1']) ?>

    <p>
        <?= Html::a('Add New Item', ['add-new-item'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
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
                'attribute' => 'supplier_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->supplier_code;
                }
            ],
            [
                'attribute' => 'supplier_name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->supplier_name;
                }
            ],
            [
                'attribute' => 'brand_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->brand_code;
                }
            ],
            [
                'attribute' => 'brand_name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->brand_name;
                }
            ],
            [
                'attribute' => 'model_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->model_code;
                }
            ],
            [
                'attribute' => 'model_description',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->model_description;
                }
            ],
            [
                'attribute' => 'stock_level_min',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->stock_level_min;
                }
            ],
//            [
//                'attribute' => 'stock_level_sts',
//                'format' => 'raw',
//                'value' => function ($model) {
//                    return $model->stock_level_sts;
//                }
//            ],
            [
                'attribute' => 'stock_on_hand',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->stock_on_hand;
                }
            ],
            [
                'attribute' => 'required_quantity',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->required_quantity;
                }
            ],
            [
                'attribute' => 'pending_quantity',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->pending_quantity;
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
                'attribute' => 'created_by_fullname',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->created_by_fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by_fullname',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->updated_by_fullname ? $model->updated_by_fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
                }
            ],
        ],
    ]);
    ?>

</div>
