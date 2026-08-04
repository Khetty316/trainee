<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\pettyCash\PettyCashRequestMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Petty Cash Request - ' . ($module === 'personal' ? 'Personal' : 'Finance');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="petty-cash-request-master-index">

    <?= $this->render('__pettycashNavBar', ['module' => $module, 'pageKey' => $key]) ?>

    <p class="mt-2">
        <?php if ($module === 'personal') { ?>
            <?=
            Html::a("Request Petty Cash",
                    "javascript:",
                    [
                        "onclick" => "event.preventDefault();",
                        "value" => \yii\helpers\Url::to(['create']),
                        "class" => "modalButtonMedium btn btn-success",
                        'data-modaltitle' => "Petty Cash Request Form"
                    ]
            )
            ?>
        <?php } ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

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
//            'id',
            [
                'attribute' => 'ref_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->ref_code;
                }
            ],
            [
                'attribute' => 'voucher_no',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->voucher_no;
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
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
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => false,
                'value' => function ($model) {
                    return $model->status0->status_name;
                }
            ],
            [
                'filterOptions' => [
                    'class' => 'sticky-action',
                    'style' => 'width:90px; min-width:90px; max-width:90px;',
                ],
                'headerOptions' => [
                    'class' => 'sticky-action',
                ],
                'format' => 'raw',
                'contentOptions' => [
                    'class' => 'text-center sticky-action',
                ],
                'value' => function ($model) use ($module) {
                    if ($module === 'personal') {
                        $action = 'view';
                    } else {
                        $action = 'finance-view-form';
                    }
                    return Html::a('View <i class="far fa-eye"></i>', [$action, 'id' => $model->id], ['class' => 'btn btn-sm btn-info mx-1']);
                }
            ],
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
        width: 100%;
        min-width: 1100px;
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
        top: 40px;   /* adjust if necessary */
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

    .table-scroll th.sticky-action,
    .table-scroll td.sticky-action {
        position: sticky;
        right: 0;
        background: #fff;
    }

    .table-scroll thead th.sticky-action {
        z-index: 30;
    }

    .table-scroll tbody td.sticky-action {
        z-index: 10;
        border-left: 1px solid #dee2e6;
        box-shadow: -2px 0 4px rgba(0,0,0,.08);
    }

    .table-scroll thead tr.filters th.sticky-action {
        position: sticky;
        right: 0;
        top: 40px; /* Same as your filter row */
        background: #fff;
        z-index: 29;
        border-left: 1px solid #dee2e6;
        box-shadow: -2px 0 4px rgba(0,0,0,.08);
    }
</style>