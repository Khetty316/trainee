<?php

use frontend\models\test\TestMain;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use \common\models\myTools\MyFormatter;
?>

<div class="test-lists-index">
    <?= $this->render('_PanelTestNavBar', ['pageKey' => '2']) ?>
    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary mt-3']) ?> 

    <div class="col-lg-12 col-md-12 col-sm-12">
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
            'tableOptions' => [
                'class' => 'table table-sm table-bordered table-striped table-hover m-0 mt-2',
                'style' => 'min-width:1800px;',
            ],
            'formatter' => [
                'class' => 'yii\i18n\Formatter',
                'nullDisplay' => ' - '
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'tc_ref',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->tc_ref, ['index-master-detail', 'id' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'project_name',
                    'value' => function ($model) {
                        return $model->project_name;
                    }
                ],
                [
                    'attribute' => 'prod_panel_code',
                    'value' => function ($model) {
                        return $model->prod_panel_code;
                    }
                ], [
                    'attribute' => 'panel_desc',
                    'value' => function ($model) {
                        return $model->panel_desc;
                    }
                ],
                [
                    'attribute' => 'panel_type',
                    'value' => function ($model) {
                        return $model->panel_type;
                    }
                ],
                [
                    'attribute' => 'test_type',
                    'value' => function ($model) {
                        return $model->test_type;
                    }
                ],
                [
                    'attribute' => 'test_num',
                    'value' => function ($model) {
                        return $model->test_num;
                    }
                ],
                [
                    'attribute' => 'panel_qty',
                    'value' => function ($model) {
                        return $model->panel_qty;
                    }
                ],
                [
                    'attribute' => 'date',
                    'value' => function ($model) {
                        return MyFormatter::asDate_Read($model->date);
                    }
                ],
                [
                    'attribute' => 'venue',
                    'value' => function ($model) {
                        return $model->venue;
                    }
                ],
                [
                    'attribute' => 'client',
                    'value' => function ($model) {
                        return $model->client;
                    }
                ],
                [
                    'attribute' => 'elec_consultant',
                    'value' => function ($model) {
                        return $model->elec_consultant;
                    }
                ],
                [
                    'attribute' => 'elec_contractor',
                    'value' => function ($model) {
                        return $model->elec_contractor;
                    }
                ],
                [
                    'attribute' => 'tested_by',
                    'value' => function ($model) {
                        return $model->tested_by;
                    }
                ],
                [
                    'attribute' => 'certified_by',
                    'value' => function ($model) {
                        return $model->certified_by;
                    }
                ],
                [
                    'attribute' => 'status',
                    'headerOptions' => [
                        'class' => 'sticky-status text-center',
                    ],
                    'filterOptions' => [
                        'class' => 'sticky-status',
                    ],
                    'contentOptions' => [
                        'class' => 'sticky-status text-center',
                    ],
                    'format' => 'raw',
                    'filter' => \frontend\models\test\RefTestStatus::getDropDownListFiltered(),
                    'value' => function ($model) {
                        return $model->status;
                    }
                ],
            ],
        ]);
        ?>
    </div>
</div>

<style>
    .table-scroll {
        width: 100%;
        max-height: calc(100vh - 320px);
        overflow-x: auto;
        overflow-y: auto;
        position: relative;
        margin-bottom: 20px;
    }

    .table-scroll table {
        width: max-content;
        min-width: 1800px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-scroll th,
    .table-scroll td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 4px !important;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 5;
        border-bottom: 1px solid #dee2e6;
    }

    .table-scroll .filters input,
    .table-scroll .filters select {
        height: 30px !important;
        padding: 4px 6px;
    }
    .grid-view > .pagination:last-child {
        margin-top: 20px;
    }

    .table-scroll th.sticky-status,
    .table-scroll td.sticky-status {
        position: sticky;
        right: 0;
        background: #fff;
        white-space: nowrap;
    }

    .table-scroll thead th.sticky-status {
        z-index: 10;
    }

    .table-scroll .filters .sticky-status {
        position: sticky;
        right: 0;
        background: #fff;
        z-index: 9;
    }

    .table-scroll tbody td.sticky-status {
        z-index: 2;
    }
</style>