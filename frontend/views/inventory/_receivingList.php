<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
?>
<div class="inventory-purchase-order-receive-batch-index">
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

    <?= GridView::widget([
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
                'attribute' => 'inventory_po_id',
                'label' => 'PO No.',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->inventoryPo->po_no;
                }
            ],
            [
                'attribute' => 'received_by',
                'headerOptions' => ['style' => 'width: 180px;'],
                'contentOptions' => ['class' => 'grid-wrap'],
                'value' => function ($model) {
                    return ($model->receivedBy->fullname ?? null);
                }
            ],
            [
                'attribute' => 'received_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->received_at === '0000-00-00 00:00:00' ? null : MyFormatter::asDateTime_ReaddmYHi($model->received_at));
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'received_at',
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
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($moduleIndex) {
                    $html = Html::a(
                            'View <i class="far fa-eye"></i>',
                            ['view-batch-details', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                            ['class' => 'btn btn-sm btn-primary mx-1']
                    );

                    return $html;
                }
            ],

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
