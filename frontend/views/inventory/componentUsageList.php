<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventoryStockoutboundSearch */
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
<div class="inventory-stockoutbound-index">
    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => '7']) ?>

    <p>
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
                    'attribute' => 'inventory_detail_id',
                    'label' => 'Supplier',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return ($model->inventoryDetail->supplier->name ?? '-');
                    }
                ],
                [
                    'attribute' => 'inventory_model_id',
                    'label' => 'Model Type',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->inventoryDetail->model->type ?? '-';
                    }
                ],
                [
                    'attribute' => 'inventory_brand_id',
                    'label' => 'Brand',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->inventoryDetail->brand->name ?? '-';
                    }
                ],
            [
                'attribute' => 'reference_type',
                'format' => 'raw',
                'filter' => [
                    'cm' => 'Corrective Maintenance',
                    'pm' => 'Preventive Maintenance',
                    'materialrequest' => 'Material Requisition',
                    'reserve' => 'Material Reservation',
                    'bomstockoutbound' => 'Project - Bill of Material',                    
                ],
                'value' => function ($model) {
                    if ($model->reference_type === 'bomstockoutbound') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($model->reference_type === 'reserve') {
                        $referenceType = 'Material Reservation';
                    } else if ($model->reference_type === 'cm') {
                        $referenceType = 'Corrective Maintenance';
                    } else if ($model->reference_type === 'pm') {
                        $referenceType = 'Preventive Maintenance';
                    } else if ($model->reference_type === 'materialrequest') {
                        $referenceType = 'Material Requisition';
                    }
                    return $referenceType ?? '-';
                },
            ],
            [
                'attribute' => 'reference_id',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->reference_type === 'bomstockoutbound') {
                        $id = frontend\models\bom\StockOutboundDetails::findOne($model->reference_id);
                        $referenceId = $id->bomDetail->bomMaster->productionPanel->project_production_panel_code;
                    } else if ($model->reference_type === 'reserve') {
                        $id = common\models\User::findOne($model->reference_id);
                        $referenceId = $id->fullname;
                    } else if ($model->reference_type === 'cm') {
                        $referenceId = 'Work Order - ' . $model->reference_id;
                    } else if ($model->reference_type === 'pm') {
                        $referenceId = 'Work Order - ' . $model->reference_id;
                    }else if ($model->reference_type === 'materialrequest') {
                        $materialRequest = frontend\models\inventory\InventoryMaterialRequest::findOne($model->reference_id);
                        if ($materialRequest->reference_type === 1) {
                            $id = \frontend\models\ProjectProduction\ProjectProductionPanels::findOne($materialRequest->reference_id);
                            $referenceId = $id->project_production_panel_code;
                        } else if ($materialRequest->reference_type === 2) {
                            $referenceId = 'Work Order - ' . $materialRequest->reference_id;
                        } else if ($materialRequest->reference_type === 3) {
                            $referenceId = 'Work Order - ' . $materialRequest->reference_id;
                        }else{
                            $referenceId = $materialRequest->reference_id;
                        }
                    }
                    return $referenceId ?? $model->reference_id;
                },
            ],
            [
                'attribute' => 'qty',
                'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->qty;
                }
            ],
            [
                'attribute' => 'dispatched_qty',
                'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->dispatched_qty;
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    $responder = common\models\User::findOne($model->created_by);
                    if ($responder) {
                        return "By " . ($responder->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                ]),
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    $responder = common\models\User::findOne($model->updated_by);
                    if ($responder) {
                        return "By " . ($responder->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    }
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                ]),
            ],
        //'dispatched_qty',
        //'created_by',
        //'created_at',
        //'updated_by',
        //'updated_at',
        //'reserve_item_id',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
