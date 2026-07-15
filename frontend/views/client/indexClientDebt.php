<?php

//debug
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;
?>

<?php
$this->registerCss("
    /* Fix datepicker overlapping issue inside GridView */
.grid-view {
    overflow: visible !important;
}
.ui-datepicker {
    z-index: 99999 !important; 
}
");
?>
<div class="client-debt-index">

    <?php
    Pjax::begin([
        'enablePushState' => false,
        'timeout' => false,
        'clientOptions' => [
            'method' => 'GET'
        ],
        'id' => 'client-debt-grid',
        'enablePushState' => true,
        'scrollTo' => false,
    ]);
    ?>

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

    <div class="table-responsive">
        <button type="button" class="btn btn-primary mb-1" onclick="resetDebtFilters()">
            Reset Filter <i class="fas fa-search-minus"></i>
        </button>

        <?php
        $this->registerJs("
    document.addEventListener('click', function (e) {

        let link = e.target.closest('#client-debt-grid table thead a');

        if (link && !link.href.includes('#debt')) {
            link.href += '#debt';
        }

    });
");
        ?>
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
                    'attribute' => 'tk_group_code',
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
                    'format' => 'raw',
                    'contentOptions' => [
                        'style' => 'text-align:right;',
                    ],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'style' => 'text-align:right;',
                    ],
                    'value' => function ($model) {
                        return number_format($model->balance, 2);
                    },
                ],
                [
                    'attribute' => 'created_at',
                    'headerOptions' => [
                        'style' => 'width:120px; text-align:left;',
                    ],
                    'contentOptions' => [
                        'style' => 'text-align:center;',
                    ],
                    'value' => function ($model) {
                        return $model->created_at ? MyFormatter::asDateTime_ReaddmYHi($model->created_at) : '-';
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'beforeShow' => new \yii\web\JsExpression("
                function(input, inst) {
                    setTimeout(function(){
                        inst.dpDiv.css({zIndex: 99999});
                    },0);
                }
            ")
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'width:120px;',
                            'autocomplete' => 'off'
                        ]
                    ]),
                ],
                [
                    'label' => 'Created By',
                    'enableSorting' => true,
                    'attribute' => 'created_by_name',
                    'headerOptions' => [
                        'style' => 'color:#007bff; font-weight:bold;'
                    ],
                    'filter' => Html::activeTextInput($searchModel, 'created_by_name', [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ]),
                    'value' => function ($model) {
                        return $model->createdBy ? $model->createdBy->fullname : '-';
                    },
                    'contentOptions' => ['style' => 'text-align:center;'],
                    'filterOptions' => ['style' => 'text-align:center;'],
                ],
                [
                    'attribute' => 'updated_at',
                    'headerOptions' => [
                        'style' => 'width:120px; text-align:left;',
                    ],
                    'contentOptions' => [
                        'style' => 'text-align:left;',
                    ],
                    'value' => function ($model) {
                        return $model->updated_at ? MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : '-';
                    },
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'clientOptions' => [
                            'dateFormat' => 'dd/mm/yy',
                            'changeMonth' => true,
                            'changeYear' => true,
                            'beforeShow' => new \yii\web\JsExpression("
                function(input, inst) {
                    setTimeout(function(){
                        inst.dpDiv.css({zIndex: 99999});
                    },0);
                }
            ")
                        ],
                        'options' => [
                            'class' => 'form-control',
                            'style' => 'width:120px;',
                            'autocomplete' => 'off'
                        ]
                    ])
                ],
                [
                    'label' => 'Updated By',
                    'enableSorting' => true,
                    'attribute' => 'updated_by_name',
                    'contentOptions' => ['style' => 'text-align:center;'],
                    'filterOptions' => ['style' => 'text-align:center;'],
                    'filterInputOptions' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                    ],
                    'value' => function ($model) {
                        return $model->updatedBy ? $model->updatedBy->fullname : '-';
                    },
                ],
            ],
        ]);
        ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<style>
    .pagination {
        display: flex;
        justify-content: flex-start;
        align-items: center;
        padding-left: 0;
        list-style: none;
        margin: 10px 0 15px;
        gap: 0;
    }

    .pagination li {
        margin: 0;
    }

    .pagination li a,
    .pagination li span {
        min-width: 34px;
        height: 36px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 6px 12px;
        border: 1px solid #ddd;
        text-decoration: none;
        background: #fff;
        color: #007bff;
    }

    .pagination li:first-child a,
    .pagination li:first-child span {
        border-radius: 4px 0 0 4px;
    }

    .pagination li:last-child a,
    .pagination li:last-child span {
        border-radius: 0 4px 4px 0;
    }

    .pagination li.active a,
    .pagination li.active span {
        background-color: #007bff;
        color: white !important;
        border-color: #007bff;
    }

    .grid-view .pagination {
        justify-content: flex-start;
    }

</style>

<script>
    function resetDebtFilters() {
        window.location.href =
                'view-client?id=<?= Yii::$app->request->get("id") ?>#debt';
    }
</script>
