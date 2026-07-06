<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
?>

<div id="emailLog" class="table-responsive">
    <?=
    Html::a('Reset Filter <i class="fas fa-search-minus"></i>',
            ['view-client', 'id' => Yii::$app->request->get('id'), '#' => 'emailLog'],
            ['class' => 'btn btn-primary'])
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
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
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
        'tableOptions' => ['class' => 'table table-bordered table-striped'],
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
                'template' => '{view}',
                'contentOptions' => [
                    'style' => 'text-align:center; vertical-align:middle; width:90px;',
                ],
                'buttons' => [
                    'view' => function ($url, $model) {

                        return Html::a(
                                'View',
                                [
                                    '/client/view-client-reminder-letter-emails',
                                    'id' => $model->id
                                ],
                                [
                                    'class' => 'btn btn-primary',
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
    .table td,
    .table th {
        padding: 4px !important;
        font-size: 13px;
        line-height: 1.2 !important;
        vertical-align: middle;
    }

    .filters input,
    .filters select {
        height: 30px !important;
        font-size: 12px;
        padding: 4px 6px;
    }

    .pagination > li > a,
    .pagination > li > span {
        padding: 4px 10px;
        font-size: 12px;
    }
</style>
