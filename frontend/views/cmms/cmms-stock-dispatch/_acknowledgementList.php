<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\cmms\CmmsWoMaterialRequestMaster;
use common\models\myTools\MyFormatter;
use frontend\models\bom\StockDispatchMaster;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsStockDispatchMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$moduleIndex = (isset($moduleIndex) ? $moduleIndex : null);
?>
<div class="cmms-stock-dispatch-master-index">

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
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
                'attribute' => 'dispatch_no',
                'format' => 'raw',
                'value' => function ($model) use ($moduleIndex) {
                    return Html::a($model->dispatch_no, ['dispatch-item-list', 'dispatchId' => $model->id, 'status' => 'pending', 'moduleIndex' => $moduleIndex]);
                }
            ],
            [
                'attribute' => 'wo_type',
                'format' => 'raw',
                'filter' => [
                    'fl' => 'Corrective Maintenance',
                    'pm' => 'Preventive Maintenance'
                ],
                'value' => function ($model) {
                    if ($model->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                        $woType = 'Corrective Maintenance';
                    } else if ($model->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                        $woType = 'Preventive Maintenance';
                    }
                    return $woType ?? '-';
                },
            ],
            [
                'attribute' => 'wo_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->wo_id ?? '-';
                },
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Dispatch At',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => "yyyy-MM-dd",
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Dispatch By',
                'value' => function ($model) {
                    return ($model->createdBy->fullname);
                }
            ],
            [
                'attribute' => 'received_by',
                'value' => function ($model) {
                    return ($model->receivedBy->fullname);
                }
            ],
            [
                'attribute' => 'status',
                'contentOptions' => ['style' => 'white-space:normal!important'],
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'status',
                        StockDispatchMaster::pending_status,
                        ['class' => 'form-control', 'prompt' => 'All']
                ),
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->status == StockDispatchMaster::TO_BE_COLLECTED || $model->status == StockDispatchMaster::TO_BE_ACKNOWLEDGED) {
                        $message = '<span class="text-warning">' . ($model->status == StockDispatchMaster::TO_BE_COLLECTED ? 'To Be Collected' : 'To Be Acknowledged') . '</span>';
                    } else {
                        $message = '<span class="text-success">Has Been Acknowledged</span>';
                    }

                    return $message;
                }
            ],
            [
                'attribute' => 'status_updated_at',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->status_updated_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'status_updated_at',
                    'language' => 'en',
                    'dateFormat' => "yyyy-MM-dd",
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            $moduleIndex !== null ? [
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a('<i class="fa fa-edit"></i>', ['dispatch-item-detail-list', 'dispatchId' => $model->id]);
                }
                    ] : [],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
