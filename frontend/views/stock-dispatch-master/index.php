<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\bom\StockDispatchMaster;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\bom\StockDispatchMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Dispatch Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-dispatch-master-index">
    <div class="table-responsive">
        <!--<h4><?php //= $this->title ?></h4>-->
        <?= $this->render('/stockoutbound/__stockoutboundNavBar', ['module' => 'inventory', 'pageKey' => 2]) ?>

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
                [
                    'attribute' => 'dispatch_no',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->dispatch_no, ['master-item-list', 'productionPanelId' => $model->production_panel_id, 'dispatchId' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Dispatch At',
                    'format' => 'html',
                    'value' => function ($model) {
                        return \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($model->created_at);
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
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => function ($model) {
                        return ($model->createdBy->fullname);
                    }
                ],
                [
                    'attribute' => 'received_by',
                    'contentOptions' => ['class' => 'col-sm-1'],
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
                            StockDispatchMaster::all_status,
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
            ],
        ]);
        ?>
    </div>
</div>
