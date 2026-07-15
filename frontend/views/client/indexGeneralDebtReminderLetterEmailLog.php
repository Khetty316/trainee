<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;

$this->params['breadcrumbs'][] = [
    'label' => 'Clients',
    'url' => ['index']
];
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

<div class="general-debt-reminder-letter-email-log-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_navbarClient', ['pageKey' => '3']) ?>

    <p>
        <?=
        Html::a(
                'Reset Filter <i class="fas fa-search-minus"></i>',
                '?',
                ['class' => 'btn btn-primary']
        )
        ?>
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

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
                    'enableSorting' => true,
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
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
                    'attribute' => 'subject',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                ],
                [
                    'attribute' => 'recipient',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                ],
                [
                    'attribute' => 'status',
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'status',
                            [
                                1 => 'Draft',
                                2 => 'Sent',
                            ],
                            [
                                'class' => 'form-control',
                                'prompt' => 'All',
                            ]
                    ),
                    'value' => function ($model) {
                        if ($model->status == 1) {
                            return Html::tag('span', 'Draft', [
                                        'style' => 'color:#f0ad4e; font-weight:600;',
                            ]);
                        }
                        if ($model->status == 2) {
                            return Html::tag('span', 'Sent', [
                                        'style' => 'color:#28a745; font-weight:600;',
                            ]);
                        }
                        return '-';
                    },
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => 'width:100px;',
                    ],
                ],
                [
                    'label' => 'Created At',
                    'attribute' => 'created_at',
                    'headerOptions' => [
                        'style' => 'width:140px;',
                    ],
                    'filter' => \yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'beforeShow' => new \yii\web\JsExpression("
                function(input, inst) {
                    setTimeout(function(){
                        inst.dpDiv.css({zIndex:99999});
                    },0);
                }
            "),
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'min-width:110px;',
                            'autocomplete' => 'off',
                        ],
                    ]),
                    'value' => function ($model) {
                        return $model->created_at ? Yii::$app->formatter->asDatetime($model->created_at, 'php:d/m/Y H:i') : '-';
                    },
                ],
                [
                    'attribute' => 'sent_at',
                    'headerOptions' => [
                        'style' => 'width:140px;',
                    ],
                    'value' => function ($model) {
                        return $model->sent_at ? MyFormatter::asDateTime_ReaddmYHi($model->sent_at) : '-';
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'sent_at',
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'changeMonth' => true,
                            'changeYear' => true,
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'width:120px;',
                            'autocomplete' => 'off'
                        ]
                    ]),
                ],
                [
                    'label' => 'Sent By',
                    'attribute' => 'sent_by_name',
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                    'value' => function ($model) {
                        return $model->sentBy ? $model->sentBy->fullname : '-';
                    },
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'contentOptions' => [
                        'style' => 'text-align:center; vertical-align:middle; width:90px;',
                    ],
                    'buttons' => [
                        'view' => function ($url, $model) {

                            return Html::a(
                                    'View <i class="fas fa-eye"></i>',
                                    [
                                        '/client/view-client-reminder-letter-emails',
                                        'id' => $model->id
                                    ],
                                    [
                                        'class' => 'btn btn-sm btn-info',
                                        'title' => 'View'
                                    ]
                            );
                        },
                    ],
                ],
            ],
        ]);
        ?>

<!--    </div>-->
    </div>
