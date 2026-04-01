<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventoryReserveItemSearch */
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
?>
<div class="inventory-reserve-item-index">
    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => '5']) ?>

    <p>
        <?= Html::a('Add New Reserve Item', ['add-new-reserve-item', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'user_id',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->user->fullname);
                }
            ],
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
                    'bom_detail' => 'Project - Bill of Material',
                    'reserve' => 'Reservation',
//                            'cm' => 'Corrective Maintenance',
//                            'pm' => 'Preventive Maintenance'
                ],
                'value' => function ($model) {
                    if ($model->reference_type === 'bom_detail') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($model->reference_type === 'bomstockoutbound') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($model->reference_type === 'reserve') {
                        $referenceType = 'Reservation';
                    } else if ($model->reference_type === 'cm') {
                        $referenceType = 'Corrective Maintenance';
                    } else if ($model->reference_type === 'pm') {
                        $referenceType = 'Preventive Maintenance';
                    }
                    return $referenceType ?? '-';
                },
            ],
            [
                'attribute' => 'reference_id',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->reference_type === 'bom_detail') {
                        $id = frontend\models\bom\BomDetails::findOne($model->reference_id);
                        $referenceId = $id->bomMaster->productionPanel->project_production_panel_code;
                    } else if ($model->reference_type === 'bomstockoutbound') {
                        $id = frontend\models\bom\StockOutboundDetails::findOne($model->reference_id);
                        $referenceId = $id->bomDetail->bomMaster->productionPanel->project_production_panel_code;
                    } else if ($model->reference_type === 'reserve') {
                        $id = common\models\User::findOne($model->reference_id);
                        $referenceId = $id->fullname;
                    }
                    return $referenceId ?? '-';
                },
            ],
            [
                'attribute' => 'reserved_qty',
                'headerOptions' => ['style' => 'width: 100px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->reserved_qty ?? 0;
                }
            ],
            [
                'attribute' => 'dispatched_qty',
                'headerOptions' => ['style' => 'width: 100px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->dispatched_qty ?? 0;
                }
            ],
            [
                'attribute' => 'available_qty',
                'headerOptions' => ['style' => 'width: 100px; text-align: center;'],
                'contentOptions' => ['style' => 'text-align: center;'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->available_qty ?? 0;
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
            [
                'attribute' => 'status',
                'contentOptions' => ['class' => 'col-sm-1'],
                'filter' => [
                    '1' => "No",
                    '2' => "Yes"
                ],
                'value' => function ($model) {
                    return $model->status == 2 ? "Yes" : "No";
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) use ($moduleIndex) {
                    $buttons = '';

                    if ($model->status == 2) {
                        $buttons .= Html::a(
                                        '<i class="fas fa-edit"></i>',
                                        "javascript:void(0)",
                                        [
                                            'title' => 'Edit Reservation',
                                            'value' => yii\helpers\Url::to(['edit-reservation', 'id' => $model->id, 'type' => $moduleIndex]),
                                            'class' => 'modalButton mr-1',
                                            'data-modaltitle' => 'Edit Reservation',
                                            'data-pjax' => '0',
                                        ]
                                ) . ' ';

                        if ($model->dispatched_qty == 0 || $model->dispatched_qty === null) {
                            $buttons .= Html::a(
                                    '<i class="fas fa-trash"></i>',
                                    ['cancel-reservation', 'id' => $model->id, 'type' => $moduleIndex], // Direct URL, not through data-value
                                    [
                                        'title' => 'Cancel Reservation',
                                        'class' => 'text-danger',
                                        'data-confirm' => 'Are you sure you want to cancel this reservation?',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                            );
                        }
                    }

                    return $buttons;
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
