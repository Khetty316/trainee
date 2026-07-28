<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
?>
<link rel="stylesheet" type="text/css" href="/css/responsiveTableIndex.css">
<div id="emailLog">
    <?=
    Html::a('Reset Filter <i class="fas fa-search-minus"></i>',
            ['view-client', 'id' => Yii::$app->request->get('id'), '#' => 'emailLog'],
            ['class' => 'btn btn-primary mb-1'])
    ?> 
    <?php
    $this->registerJs("
document.addEventListener('click', function (e) {
    let link = e.target.closest('.pagination a, table thead a');
    if (link) {
        link.href = link.href.replace('#contact', '');
        link.href = link.href.replace('#debt', '');
        if (!link.href.includes('#emailLog')) {
            link.href += '#emailLog';
        }
    }
});
");
    ?>
    <?=
    GridView::widget([
        'layout' => "
    {summary}
    {pager}
    <div class='table-scroll table-responsive'>
        {items}
    </div>
    {pager}
",
        'dataProvider' => $emailLogDataProvider,
        'filterModel' => $emailLogSearchModel,
        'pager' => [
            'class' => \yii\widgets\LinkPager::class,
            'options' => ['class' => 'pagination'],
            'linkOptions' => [
                'class' => 'page-link',
            ],
        ],
        'summary' => 'Showing <b>{begin}-{end}</b> of <b>{totalCount}</b> items.',
        'tableOptions' => [
            'class' => 'table table-bordered table-striped mb-0',
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'label' => 'Subject',
                'attribute' => 'subject',
            ],
            [
                'label' => 'Recipient',
                'attribute' => 'recipient',
                'contentOptions' => [
                    'style' => 'max-width:250px; word-break:break-word;'
                ],
            ],
            [
                'label' => 'Status',
                'attribute' => 'status',
                'filter' => [
                    1 => 'Draft',
                    2 => 'Sent',
                ],
                'filterInputOptions' => [
                    'class' => 'form-control',
                    'prompt' => 'All Status',
                ],
                'format' => 'raw',
                'value' => function ($model) {

                    if ($model->status == 2) {
                        return '<span class="text-success">Sent</span>';
                    }
                    return '<span class="text-warning">Draft</span>';
                }
            ],
            [
                'label' => 'Created At',
                'attribute' => 'created_at',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'filter' => DatePicker::widget([
                    'model' => $emailLogSearchModel,
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
                        'style' => 'min-width:110px;',
                        'autocomplete' => 'off'
                    ]
                ]),
                'value' => function ($model) {
                    return $model->created_at ? date('d/m/Y H:i', strtotime($model->created_at)) : '-';
                }
            ],
            [
                'label' => 'Sent At',
                'attribute' => 'sent_at',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'filter' => DatePicker::widget([
                    'model' => $emailLogSearchModel,
                    'attribute' => 'sent_at',
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
                        'style' => 'min-width:110px;',
                        'autocomplete' => 'off'
                    ]
                ]),
                'value' => function ($model) {
                    return $model->sent_at ? date('d/m/Y H:i', strtotime($model->sent_at)) : '-';
                }
            ],
            [
                'label' => 'Sent By',
                'attribute' => 'sent_by_name',
                'filter' => Html::activeTextInput(
                        $emailLogSearchModel,
                        'sent_by_name',
                        [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                        ]
                ),
                'value' => function ($model) {
                    if ($model->status == 1) {
                        return 'Not Sent Yet';
                    }
                    return $model->senderUser ? $model->senderUser->fullname : '-';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'template' => '{view}',
                'headerOptions' => [
                    'style' => 'width:90px; min-width:90px; max-width:90px; text-align:center;',
                ],
                'contentOptions' => [
                    'style' => 'width:90px; min-width:90px; max-width:90px; text-align:center; vertical-align:middle;',
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
                                    'class' => 'btn btn-info',
                                    'style' => 'padding:2px 8px; font-size:12px;',
                                    'title' => 'View'
                                ]
                        );
                    },
                ],
            ],
        ],
    ]);
    ?>
</div>

<style>
    .table-scroll table {
        width: max-content;
        min-width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    #emailLog .table-scroll th:last-child,
    #emailLog .table-scroll td:last-child {
        position: sticky;
        right: 0;
        background: #fff;
    }
</style>