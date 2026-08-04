<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;

$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
?>
<link rel="stylesheet" type="text/css" href="/css/responsiveTableIndex.css">
<?php
$this->registerCss("
.grid-view > .pagination:last-child {
    margin-bottom: 20px;
}
");
?>
<div class="client-general-debt-index">
    <?= $this->render('_navbarClient', ['pageKey' => '2']) ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="grid-table-wrapper">
            <table>
                <?=
                Html::a(
                        'New Entry <i class="fas fa-plus"></i>',
                        "javascript:",
                        [
                            "onclick" => "event.preventDefault();",
                            "value" => \yii\helpers\Url::to(['create-new-entry']),
                            "class" => "modalButton btn btn-success",
                            'data-modaltitle' => "New Debt Entry"
                        ]
                )
                ?>
                <?=
                Html::a(
                        'Import Outstanding Balance <i class="fas fa-download"></i>',
                        ['add-by-template-clients'],
                        ['class' => 'btn btn-success ml-1']
                )
                ?>
                <?=
                Html::a(
                        'Reset Filter <i class="fas fa-search-minus"></i>',
                        '?',
                        ['class' => 'btn btn-primary ml-1']
                )
                ?>
            </table>
        </div>
        <div>
            <?=
            Html::a(
                    'User Manual <i class="fas fa-book"></i>',
                    ['user-manual'],
                    [
                        'class' => 'btn btn-warning',
                        'target' => '_blank',
                        'title' => 'View User Manual'
                    ]
            )
            ?>
        </div>
    </div>

    <?php
    $months = [
        1 => 'January',
        2 => 'February',
        3 => 'March',
        4 => 'April',
        5 => 'May',
        6 => 'June',
        7 => 'July',
        8 => 'August',
        9 => 'September',
        10 => 'October',
        11 => 'November',
        12 => 'December',
    ];
    ?>
    <div class="grid-table-wrapper">
        <?=
        GridView::widget([
            'layout' => "
    {summary}
    {pager}
    <div class='table-scroll' style='margin-bottom:20px;'>
        {items}
    </div>
    {pager}
",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class,
                'firstPageLabel' => '<i class="fa fa-angle-double-left"></i> First Page',
                'prevPageLabel' => '<i class="fa fa-angle-left"></i> Prev',
                'nextPageLabel' => 'Next <i class="fa fa-angle-right"></i>',
                'lastPageLabel' => 'Last Page <i class="fa fa-angle-double-right"></i>',
                'maxButtonCount' => 5,],
            'formatter' => [
                'class' => 'yii\i18n\Formatter',
                'nullDisplay' => ' - '
            ],
            'tableOptions' => [
                'class' => 'table-striped table-bordered table-sm',
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'client_code',
                    'label' => 'Client Code',
                    'value' => function ($model) {
                        return $model->client->client_code ?? '-';
                    },
                ],
                [
                    'attribute' => 'company_name',
                    'label' => 'Company Name',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                    'value' => function ($model) {
                        return $model->client->company_name ?? '-';
                    },
                ],
                [
                    'attribute' => 'tk_group_code',
                    'label' => 'Company Group',
                    'enableSorting' => true,
                    'filter' => \frontend\models\common\RefCompanyGroupList::COMPANYGROUP3,
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'prompt' => 'All Group',
                    ],
                    'value' => function ($model) {
                        return \frontend\models\common\RefCompanyGroupList::COMPANYGROUP3[$model->tk_group_code] ?? $model->tk_group_code;
                    },
                ],
                [
                    'attribute' => 'month',
                    'contentOptions' => ['style' => 'text-align:center;'],
                    'filter' => $months,
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'prompt' => 'All'
                    ],
                    'value' => function ($model) use ($months) {
                        return $months[$model->month] ?? '-';
                    },
                ],
                [
                    'attribute' => 'year',
                    'contentOptions' => ['style' => 'text-align:center;'],
                    'filterOptions' => ['style' => 'text-align:center;'],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                ],
                [
                    'attribute' => 'balance',
                    'label' => 'Balance (MYR)',
                    'headerOptions' => ['class' => 'text-left'],
                    'value' => function ($model) {
                        return number_format($model->balance, 2);
                    },
                    'contentOptions' => ['style' => 'text-align:right;'],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                ],
                [
                    'label' => 'Created At',
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if (!$model->created_at && !$model->createdBy) {
                            return '-';
                        }
                        $createdBy = $model->createdBy ? $model->createdBy->fullname : '-';
                        $createdAt = $model->created_at ? MyFormatter::asDateTime_ReaddmYHi($model->created_at) : '-';
                        return $createdBy . ' @ ' . $createdAt;
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'changeMonth' => true,
                            'changeYear' => true,
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'width:180px;',
                            'autocomplete' => 'off'
                        ]
                    ]),
                ],
                [
                    'label' => 'Updated At',
                    'attribute' => 'updated_at',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if (!$model->updated_at && !$model->updatedBy) {
                            return '-';
                        }
                        $updatedBy = $model->updatedBy ? $model->updatedBy->fullname : '-';
                        $updatedAt = $model->updated_at ? MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : '-';
                        return $updatedBy . ' @ ' . $updatedAt;
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'changeMonth' => true,
                            'changeYear' => true,
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'width:180px;',
                            'autocomplete' => 'off'
                        ]
                    ]),
                ],
                [
                    'header' => 'Action',
                    'format' => 'raw',
                    'headerOptions' => [
                        'class' => 'sticky-action',
                        'style' => 'width:180px; text-align:center;',
                    ],
                    'filterOptions' => [
                        'class' => 'sticky-action',
                    ],
                    'contentOptions' => [
                        'class' => 'sticky-action text-center',
                        'style' => 'min-width:180px; white-space:nowrap;',
                    ],
                    'value' => function ($model) {
                        $updateBtn = Html::a(
                                'Update <i class="far fa-edit"></i>',
                                "javascript:",
                                [
                                    "onclick" => "event.preventDefault();",
                                    "value" => \yii\helpers\Url::to(array_merge(
                                                    ['update-debt', 'id' => $model->id],
                                                    Yii::$app->request->queryParams
                                            )),
                                    "class" => "modalButton btn btn-sm btn-primary",
                                    'data-modaltitle' => "Update Debt Summary"
                                ]
                        );
                        $deleteBtn = Html::a(
                                'Delete <i class="fas fa-trash"></i>',
                                array_merge(
                                        ['delete-debt', 'id' => $model->id],
                                        Yii::$app->request->queryParams
                                ),
                                [
                                    'class' => 'btn btn-sm btn-danger',
                                    'data' => [
                                        'confirm' => 'Are you sure you want to delete this record?',
                                        'method' => 'post',
                                    ],
                                ]
                        );
                        return $updateBtn . ' ' . $deleteBtn;
                    },
                ],
            ],
        ]);
        ?>
    </div>