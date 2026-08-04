<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

$user = common\models\User::findOne(Yii::$app->user->id);
?>
<div class="claim-master-index">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class,
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i> First Page',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i> Prev',
            'nextPageLabel' => 'Next <i class="fa fa-angle-right"></i>',
            'lastPageLabel' => 'Last Page <i class="fa fa-angle-double-right"></i>',
            'maxButtonCount' => 5,],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "
{summary}
{pager}
<div class='table-scroll'>
    {items}
</div>
{pager}
",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'claim_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->claim_code;
                }
            ],
            [
                'attribute' => 'claim_type',
                'format' => 'raw',
                'filter' => frontend\models\office\claim\RefClaimType::getDropDownList($user->grade),
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->claimType->claim_name;
                }
            ],
            [
                'attribute' => 'claimant_id',
                'label' => 'Claimant Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->claimant->fullname;
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return "By " . ($model->claimant->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
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
            [
                'attribute' => 'claim_status',
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->claimStatus->status_name;
                }
            ],
            [
                'format' => 'raw',
                'headerOptions' => [
                    'class' => 'sticky-action',
                    'style' => 'width:90px; min-width:90px; max-width:90px;',
                ],
                'contentOptions' => [
                    'class' => 'text-center sticky-action',
                    'style' => 'width:90px; min-width:90px; max-width:90px;',
                ],
                'filterOptions' => [
                    'class' => 'sticky-action',
                    'style' => 'width:90px; min-width:90px; max-width:90px;',
                ],
                'filter' => false,
                'value' => function ($model) use ($module) {
                    if ($module === 'personal') {
                        return Html::a('View <i class="far fa-eye"></i>', ['personal-view-claim', 'id' => $model->id], ['class' => 'btn btn-sm btn-info mx-1']);
                    } elseif ($module === 'superior') {
                        return Html::a('View <i class="far fa-eye"></i>', ['superior-view-claim', 'id' => $model->id], ['class' => 'btn btn-sm btn-info mx-1']);
                    } elseif ($module === 'finance') {
                        return Html::a('View <i class="far fa-eye"></i>', ['finance-view-claim', 'id' => $model->id], ['class' => 'btn btn-sm btn-info mx-1']);
                    }
                }
            ],
        //'created_by',
        //'created_at',
        //'updated_by',
        //'updated_at',
        //            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>

<style>
    .table-scroll {
        max-height: calc(100vh - 320px);
        overflow: auto;
        margin: 20px 0;
    }

    .table-scroll table {
        width: max(100%, 1200px);
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 20;
        border-bottom: 1px solid #dee2e6;
    }

    .table-scroll thead tr.filters th {
        position: sticky;
        top: 40px;
        background: #fff;
        z-index: 19;
    }

    .table td,
    .table th {
        padding: 4px !important;
    }

    .filters input,
    .filters select {
        height: 30px !important;
        padding: 4px 6px;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:nth-child(2) .page-link,
    .pagination .page-item:nth-last-child(2) .page-link,
    .pagination .page-item:last-child .page-link {
        min-width: unset;
    }

    /* Sticky Action Column */
    .table-scroll th.sticky-action,
    .table-scroll td.sticky-action {
        position: sticky;
        right: 0;
        background: #fff;
        border-left: 1px solid #dee2e6;
    }

    .table-scroll thead th.sticky-action {
        z-index: 30;
        box-shadow: -2px 0 4px rgba(0,0,0,.08);
    }

    .table-scroll thead tr.filters th.sticky-action {
        top: 40px;
        z-index: 29;
        box-shadow: -2px 0 4px rgba(0,0,0,.08);
    }

    .table-scroll tbody td.sticky-action {
        z-index: 10;
        box-shadow: -2px 0 4px rgba(0,0,0,.08);
    }
</style>
