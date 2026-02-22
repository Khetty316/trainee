<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use yii\jui\DatePicker;
?>


<div class="">


    <?php
    $i = 0;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'options' => ['class' => 'table-sm'],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'columns' => [
            [
                'attribute' => 'claims_detail_id',
                'label' => 'Item ID'
            ],
            [
                'attribute' => 'claims_id',
            ],
            [
                'attribute' => 'claimant',
            ],
            [
                'attribute' => 'detail',
                'value' => function($data) {
                    return ($data->claim_type == 'med' ? '(Medical) - ' : '') . $data->detail;
                }
            ],
            [
                'attribute' => 'claims_status_name',
                'label' => 'Status',
            ],
            [
                'attribute' => 'amount',
                'label' => 'Amount',
                'format' => 'raw',
                'value' => function($data) {
                    return '<p class="p-0 m-0 text-right">' . MyFormatter::asCurrency($data->amount) . '</p>';
                }
            ],
            [
                'attribute' => 'invoice_date',
                'label' => 'Invoice Date',
                'format' => 'raw',
                'value' => function($data) {
                    return MyFormatter::asDate_Read($data->invoice_date);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'invoice_date',
                    'language' => 'en',
                    'dateFormat' => 'yyyy-MM-dd',
//                    'dateFormat'=>'php:d/m/Y',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
        ],
    ]);
    ?>

</div>
