<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;

$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
?>

<?php
$this->registerCss("
.table-responsive {
    overflow-x: auto;
}

.grid-view table {
    min-width: 1400px;
    border-collapse: collapse;
}

/* Sticky Action Column */
.grid-view th:last-child,
.grid-view td:last-child {
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 1;
    min-width: 160px;
    white-space: nowrap;
}

.grid-view .btn {
    white-space: nowrap;
}
");
?>

<div class="client-general-debt-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_navbarClient', ['pageKey' => '2']) ?>
    <p>
        <?php
        $newEntryBtn = Html::a(
                'New Entry <i class="fas fa-plus"></i>',
                "javascript:",
                [
                    "onclick" => "event.preventDefault();",
                    "value" => \yii\helpers\Url::to(['create-new-entry']),
                    "class" => "modalButton btn btn-success",
                    'data-modaltitle' => "New Debt Entry"
                ]
        );
        ?>

        <?= $newEntryBtn ?>
        <?= Html::a('Import Outstanding Balance <i class="fas fa-download"></i>', ['add-by-template-clients'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

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

    <!--<div class="table-responsive">-->
        <?=
        GridView::widget([
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => [
                'class' => yii\bootstrap4\LinkPager::class
            ],
            'formatter' => [
                'class' => 'yii\i18n\Formatter',
                'nullDisplay' => ' - '
            ],
            'tableOptions' => [
                'class' => 'table table-striped table-bordered table-sm'
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
                    'contentOptions' => [
                        'style' => 'width:140px; text-align:center;',
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
                                    "class" => "modalButton btn btn-sm btn-primary mx-1",
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
                                    'class' => 'btn btn-sm btn-danger mx-1',
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

    <!--</div>-->
