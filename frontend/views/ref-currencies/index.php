<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\common\RefCurrenciesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Currency Exchange';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ref-currencies-index">

    <!--<h3><?php //= Html::encode($this->title)      ?></h3>-->

    <p>
        <?=
        Html::a('Create New <i class="fas fa-plus"></i>', "javascript:void(0)", [
            'title' => "Create New",
            'value' => yii\helpers\Url::to(['create']),
            'class' => 'modalButtonMedium btn btn-success text-center',
            'data-modaltitle' => 'Create New',
        ]);
        ?>

        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => yii\bootstrap4\LinkPager::class,
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i> First Page',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i> Prev',
            'nextPageLabel' => 'Next <i class="fa fa-angle-right"></i>',
            'lastPageLabel' => 'Last Page <i class="fa fa-angle-double-right"></i>',
            'maxButtonCount' => 5,
        ],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "
{summary}
{pager}
<div class='table-scroll' style='margin-bottom:20px;'>
    {items}
</div>
{pager}
",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'currency_id',
            'currency_name',
            'currency_code',
            'currency_sign',
            [
                'attribute' => 'exchange_rate',
                'contentOptions' => ['class' => 'col-sm-1 text-right'],
                'format' => 'raw',
            ],
            [
                'attribute' => 'active',
                'contentOptions' => ['class' => 'grid-wrap', 'style' => 'text-align: center;'],
                'value' => function ($model) {
                    return $model->active == 1 ? 'Yes' : 'No';
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'active',
                        [
                            '' => 'All',
                            '1' => 'Yes',
                            '0' => 'No'
                        ],
                        ['class' => 'form-control text-center']
                )
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Created By',
                'headerOptions' => ['style' => 'width: 180px;'],
                'contentOptions' => ['class' => 'grid-wrap'],
                'value' => function ($model) {
                    return ($model->createdBy->fullname ?? null);
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->created_at === '0000-00-00 00:00:00' ? null : MyFormatter::asDateTime_ReaddmYHi($model->created_at));
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
            [
                'attribute' => 'updated_by',
                'label' => 'Updated By',
                'headerOptions' => ['style' => 'width: 180px;'],
                'contentOptions' => ['class' => 'grid-wrap'],
                'value' => function ($model) {
                    return ($model->updatedBy->fullname ?? null);
                }
            ],
            [
                'attribute' => 'updated_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return ($model->updated_at === '0000-00-00 00:00:00' ? null : MyFormatter::asDateTime_ReaddmYHi($model->updated_at));
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
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
                'headerOptions' => [
                    'class' => 'sticky-action',
                ],
                'filterOptions' => [
                    'class' => 'sticky-action',
                ],
                'contentOptions' => [
                    'class' => 'text-center sticky-action',
                ],
                'value' => function ($model) {
                    return Html::a(
                            'Update <i class="fa fa-edit"></i>',
                            "javascript:void(0)",
                            [
                                'title' => "Update Detail",
                                'value' => yii\helpers\Url::to(['update', 'id' => $model->currency_id]),
                                'class' => 'modalButtonMedium btn btn-sm btn-primary text-center',
                                'data-modaltitle' => 'Update Detail',
                            ]
                    );
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>
<style>
    .table-scroll {
        width: 100%;
        max-height: 600px;
        overflow: auto;
    }

    .table-scroll table {
        min-width: 1400px;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 3;
    }

    .grid-view {
        overflow: visible;
    }

    .grid-view table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-scroll th.sticky-action,
    .table-scroll td.sticky-action {
        position: sticky;
        right: 0;
        background: #fff;
        white-space: nowrap;
    }

    .table-scroll thead th.sticky-action,
    .table-scroll thead td.sticky-action {
        z-index: 5;
    }

    .table-scroll tbody td.sticky-action {
        z-index: 2;
    }
</style>