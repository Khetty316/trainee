<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\bom\StockDispatchTrial;
use frontend\models\bom\StockDispatchMaster;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\bom\VStockDispatchMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="vstock-dispatch-master-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'rowOptions' => function ($model) {
            if ($model->active_sts == 0) {
                return ['style' => 'text-decoration: line-through; color: red;'];
            }
            return [];
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'model_type',
                'contentOptions' => ['style' => 'white-space:normal!important']
            ],
            [
                'attribute' => 'brand',
                'contentOptions' => ['style' => 'white-space:normal!important']
            ],
            [
                'attribute' => 'descriptions',
                'contentOptions' => ['style' => 'white-space:normal!important']
            ],
            [
                'attribute' => 'total_trial_dispatch_qty',
                'label' => 'Dispatch Quantity',
                'contentOptions' => ['style' => 'white-space:normal!important'],
                'format' => 'raw',
                'value' => function ($model) use ($action) {
                    $hasPendingAcknowledgements = StockDispatchTrial::find()
                            ->where([
                                'stock_dispatch_master_id' => $model->dispatch_id,
                                'stock_outbound_details_id' => $model->stock_outbound_details_id,
                            ])
                            ->andWhere(['in', 'current_sts', [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED]])
                            ->exists();

                    $message = '<span class="text-success">Has Been Acknowledged</span>';
                    
                    if (!$hasPendingAcknowledgements && $model->active_sts == 1) {
                        if ($action === 'adjust') {
                            return $model->total_trial_dispatch_qty . Html::a(
                                            "<div><i class='far fa-edit float-right'></i></div><div class='float-right'>" . $message . "</div>",
                                            "javascript:",
                                            [
                                                'title' => "Adjust Dispatched Quantity",
                                                "value" => yii\helpers\Url::to(['adjust-dispatch-quantity', 'dispatchId' => $model->dispatch_id, 'detailId' => $model->stock_outbound_details_id]),
                                                "class" => "modalButton float-right",
                                                'data-modaltitle' => "Adjust Quantity"
                                            ]
                            );
                        } else {
                            return $model->total_trial_dispatch_qty . Html::a(
                                            "<div><i class='far fa-edit float-right'></i></div><div class='float-right'>" . $message . "</div>",
                                            "javascript:",
                                            [
                                                'title' => "Return Dispatched Quantity",
                                                "value" => yii\helpers\Url::to(['return-dispatched-quantity', 'dispatchId' => $model->dispatch_id, 'detailId' => $model->stock_outbound_details_id]),
                                                "class" => "modalButton float-right",
                                                'data-modaltitle' => "Return Quantity"
                                            ]
                            );
                        }
                    } else {
                        $trial = frontend\models\bom\StockDispatchTrial::find()
                                ->where([
                                    'stock_dispatch_master_id' => $model->dispatch_id,
                                    'stock_outbound_details_id' => $model->stock_outbound_details_id,
                                ])
                                ->andWhere(['in', 'current_sts', [StockDispatchMaster::TO_BE_COLLECTED, StockDispatchMaster::TO_BE_ACKNOWLEDGED]])
                                ->one();
                        if ($trial !== null) {
                            $message = '<span class="text-warning">' . ($trial->current_sts == StockDispatchMaster::TO_BE_COLLECTED ? 'To be Collected' : 'To be Acknowledged') . '</span>';
                        }
                        return "<div>" . $model->total_trial_dispatch_qty . Html::a("<i class='far fa-edit float-right'></i></div>") . "<div class='float-right'>" . $message . "</div>";
                    }
                },
            ],
            [
                'attribute' => 'master_status_updated_at',
                'label' => 'Status Updated At',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->master_status_updated_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'master_status_updated_at',
                    'language' => 'en',
                    'dateFormat' => "yyyy-MM-dd",
                    'options' => ['class' => 'form-control'],
                ]),
            ],
        ],
    ]);
    ?>
</div>
