<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\pettyCash\PettyCashLedgerDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$action = ($module === 'finance' ? ['label' => 'Petty Cash Ledger - Finance', 'url' => ['finance-ledger']] : ['label' => 'Petty Cash Ledger - Director', 'url' => ['director-ledger']]);
$this->params['breadcrumbs'][] = $action;
$this->title = 'Ledger by: ' . $master->createdBy->fullname;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="petty-cash-ledger-detail-index">
    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded mt-3 mb-3">
        <div class="">
            <span class="text-muted small">Current Balance</span>
            <h4 class="mb-0 text-success font-weight-bold">
                RM <?= \common\models\myTools\MyFormatter::asDecimal2($master->amount ?? 0) ?>
            </h4>
        </div>
        <?=
        Html::a("Export to CSV <i class='fas fa-file-csv fa-lg'></i>",
                "javascript:",
                [
                    "onclick" => "event.preventDefault();",
                    "value" => \yii\helpers\Url::to(['ajax-export-ledger-csv', 'id' => $master->id]),
                    "class" => "modalButtonMedium btn btn-primary mx-1",
                    'data-modaltitle' => "Export to CSV"
                ]
        );
        ?>
    </div>
    <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?id=' . $master->id, ['class' => 'btn btn-primary mb-2']) ?>
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
                'attribute' => 'date',
                'format' => 'raw',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->date);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'date'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'voucher_no',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->debit === null && $model->debit == 0.00) {
                        return Html::a(
                                        $model->voucher_no,
                                        ['finance-view-form', 'voucher_no' => $model->voucher_no],
                                        ['target' => '_blank', 'rel' => 'noopener noreferrer']
                                );
                    } else {
                        return Html::a(
                                        $model->voucher_no,
                                        ['finance-view-replenishment-request', 'voucher_no' => $model->voucher_no],
                                        ['target' => '_blank', 'rel' => 'noopener noreferrer']
                                );
                    }
                }
            ],
            'ref_1',
            'ref_2',
            'description',
            [
                'attribute' => 'debit',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-right'],
                'value' => function ($model) {
                    return \common\models\myTools\MyFormatter::asDecimal2($model->debit ?? 0);
                }
            ],
            [
                'attribute' => 'credit',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-right'],
                'value' => function ($model) {
                    return \common\models\myTools\MyFormatter::asDecimal2($model->credit ?? 0);
                }
            ],
            [
                'attribute' => 'balance',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-right'],
                'value' => function ($model) {
                    return \common\models\myTools\MyFormatter::asDecimal2($model->balance ?? 0);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    return "By " . ($model->createdBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
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
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
        ],
    ]);
    ?>


</div>