<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;
?>

<legend class="w-auto px-2 m-0">Client Debt:</legend>

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
        'id' => 'client-debt-grid',
        'enablePushState' => false
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

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterOnFocusOut' => true,
        'headerRowOptions' => ['style' => 'text-align:center;'],
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
                    return $months[$model->month] ?? $model->month;
                },
            ],
            [
                'attribute' => 'year',
                'contentOptions' => ['style' => 'text-align:center;'],
                'filterOptions' => ['style' => 'text-align:center;'],
            ],
            [
                'attribute' => 'balance',
                'headerOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    return number_format($model->balance, 2);
                },
                'contentOptions' => ['style' => 'text-align:right;'],
            ],
            [
                'attribute' => 'created_at',
//                'format' => ['datetime', 'php:d/m/Y H:i'],
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
                'attribute' => 'created_by_name',
                'headerOptions' => [
                    'style' => 'color:#007bff; font-weight:bold;'
                ],
                'filter' => Html::activeTextInput($searchModel, 'created_by_name', [
                    'class' => 'form-control',
                ]),
                'value' => function ($model) {
                    return $model->createdBy ? $model->createdBy->fullname : '-';
                },
                'contentOptions' => ['style' => 'text-align:center;'],
                'filterOptions' => ['style' => 'text-align:center;'],
            ],
            [
                'attribute' => 'updated_at',
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
                'attribute' => 'updated_by',
                'contentOptions' => ['style' => 'text-align:center;'],
                'filterOptions' => ['style' => 'text-align:center;'],
                'value' => function ($model) {
                    return $model->updatedBy ? $model->updatedBy->fullname : '-';
                },
            ],
        ],
    ]);
    ?>

    <?php Pjax::end(); ?>
</div>
