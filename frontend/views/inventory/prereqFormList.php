<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\RefGeneralStatus;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\preReqForm\PrereqFormMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'execPendingPurchasing') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
    $key = 1;
} else if ($moduleIndex === 'execAllPurchasing') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
    $key = 2;
} else if ($moduleIndex === 'assistPendingPurchasing') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
    $key = 1;
} else if ($moduleIndex === 'assistAllPurchasing') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
    $key = 2;
} else if ($moduleIndex === 'projcoorPendingApproval') {
    $pageName = 'Purchasing - Project Coordinator';
    $module = 'projcoor';
    $key = 1;
} else if ($moduleIndex === 'projcoorReadyForProcurement') {
    $pageName = 'Purchasing - Project Coordinator';
    $module = 'projcoor';
    $key = 2;
} else if ($moduleIndex === 'projcoorAllApproval') {
    $pageName = 'Purchasing - Project Coordinator';
    $module = 'projcoor';
    $key = 3;
} else if ($moduleIndex === 'maintenanceHeadPendingApproval') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
    $key = 1;
} else if ($moduleIndex === 'maintenanceHeadReadyForProcurement') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
    $key = 2;
} else if ($moduleIndex === 'maintenanceHeadAllApproval') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
    $key = 3;
}

//$this->title = 'Pre-Requisition';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
//$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="prereq-form-master-index">

    <?= $this->render('__inventoryNavBar', ['module' => $module, 'pageKey' => $key]) ?>
    <p>
        <?php //        if ($page === 'newItem'):  ?>
        <?php //= Html::a('Pre-requisition', ['prerequisition', 'moduleIndex' => $moduleIndex], ['class' => 'btn btn-success']) ?>
        <?=
        Html::a("Pre-requisition <i class='fas fa-plus'></i>", ['create-prerequisition', 'sourceModule' => 'inventory', 'moduleIndex' => $moduleIndex, 'referenceType' => null, 'referenceId' => null], [
            'class' => 'btn btn-success',
            'target' => '_blank',
            'rel' => 'noopener noreferrer'
        ])
        ?>    
        <?php // endif; ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?type=' . $moduleIndex . '&context=' . $context, ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-inventory'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

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
                'attribute' => 'prf_no',
                'label' => 'PRF Code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->prf_no . ($model->status == RefGeneralStatus::STATUS_Approved ? ' ' . Html::a(
                            "<i class='far fa-file-alt fa-lg float-right m-1'></i>",
                            "#",
                            [
                                'title' => "Generate PDF",
                                'value' => "/office/prereq-form-master/get-file?filename=" . urlencode($model->filename) . '&id=' . $model->id,
                                'class' => "docModal"
                            ]
                    ) : "");
                }
            ],
            [
                'attribute' => 'reference_type',
                'format' => 'raw',
                'filter' => [
                    'bom' => 'Project - Bill of Material',
                    'reserve' => 'Reservation'
                ],
                'value' => function ($model) {
                    if ($model->reference_type === 'bom') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($model->reference_type === 'bomstockoutbound') {
                        $referenceType = 'Project - Bill of Material';
                    } else if ($model->reference_type === 'reserve') {
                        $referenceType = 'Reservation';
                    }
                    return $referenceType ?? '-';
                },
            ],
            [
                'attribute' => 'reference_id',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->reference_type === 'bom') {
                        $bomMaster = frontend\models\bom\BomMaster::findOne($model->reference_id);
                        $referenceId = $bomMaster->productionPanel->project_production_panel_code;
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
                'attribute' => 'date_of_material_required',
                'format' => 'raw',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->date_of_material_required);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_of_material_required',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
//                    'clientOptions' => [
//                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
//                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'date_of_material_required'), // Hidden input for sending formatted date
//                    ],
                ]),
            ],
//            [
//                'attribute' => 'total_amount',
//                'label' => 'Total Amount (RM)',
//                'format' => 'raw',
//                'contentOptions' => ['class' => 'text-right'],
//                'value' => function ($model) {
//                    return \common\models\myTools\MyFormatter::asDecimal2($model->total_amount);
//                }
//            ],
            [
                'attribute' => 'superior_id',
                'label' => 'Superior Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->superior->fullname;
                }
            ],
            //'filename',
//            'status',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    $status_desc = RefGeneralStatus::findOne($model->status);
                    return $status_desc->status_name;
                }
            ],
            //'is_deleted',
            //'created_by',
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
            //'updated_by',
            //'updated_at',
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($moduleIndex, $module) {
//                    if ($module === 'personal' || $module === 'inventory' || $module === 'superuser') {
                    $html = Html::a(
                            'View <i class="far fa-eye"></i>',
                            ['view-pre-requisition', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                            ['class' => 'btn btn-sm btn-primary mx-1']
                    );
//                    print_r($model);
                    if ($model->source_module == 2 && $model->status == RefGeneralStatus::STATUS_Approved && $model->is_deleted == 0 && ($model->inventory_flag == 0 || $model->inventory_flag === null)) {
                        $html .= Html::a(
                                'Send to Procurement <i class="fas fa-arrow-right"></i>',
                                ['send-to-procurement', 'id' => $model->id, 'type' => $module],
                                [
                                    'class' => 'btn btn-sm btn-success mx-1',
                                    'data-confirm' => 'Are you sure you want to send this item to Procurement?',
                                    'data-method' => 'post', // Recommended for state-changing actions
                                ]
                        );
                    }
                    return $html;
//                    } else {
//                        return Html::a(
//                                        'View <i class="far fa-eye"></i>',
//                                        ['save-superior-update', 'id' => $model->id, 'moduleIndex' => $module],
//                                        ['class' => 'btn btn-sm btn-success mx-1']
//                                );
//                    }
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
    <?=
    $this->render('/_docModal')
    ?>  
</div>
