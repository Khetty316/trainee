<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;

$this->params['breadcrumbs'][] = [
    'label' => 'Clients',
    'url' => ['index']
];
?>

<div class="client-debt-reminder-letter-template-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_navbarClient', ['pageKey' => '4']) ?>

    <p>
        <?= Html::a('Create New Template <i class="fas fa-plus"></i>', ['create-reminder-letter-template'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

</div>

<div class="table-responsive">
    <?=
    GridView::widget([
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
//            'letter_name',
            [
                'attribute' => 'letter_name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->letter_name, ['view-client-reminder-letter-template', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'content',
                'format' => 'raw',
                'value' => function ($model) {

                    return \yii\helpers\StringHelper::truncateWords(
                            strip_tags($model->content),
                            20
                    );
                },
                'contentOptions' => [
                    'style' => '
        max-width:400px;
        font-size:12px;
        line-height:1.6;
        padding-top:20px;
        padding-bottom:20px;
        min-height:100px;
        white-space:normal;
    '
                ],
            ],
            [
                'attribute' => 'active_sts',
                'filter' => \yii\helpers\Html::activeDropDownList(
                        $searchModel,
                        'active_sts',
                        [
                            1 => 'No',
                            0 => 'Yes',
                        ],
                        [
                            'class' => 'form-control',
                            'prompt' => 'All',
                        ]
                ),
                'value' => function ($model) {
                    return $model->active_sts == 0 ? 'Yes' : 'No';
                },
            ],
            [
                'attribute' => 'creator_name',
                'label' => 'Created By',
                'value' => 'creator.fullname',
            ],
            [
                'attribute' => 'created_at',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'value' => function ($model) {
                    return $model->created_at ? MyFormatter::asDateTime_ReaddmYHi($model->created_at) : '-';
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'clientOptions' => [
                        'dateFormat' => 'yy-mm-dd',
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
                'attribute' => 'updater_name',
                'label' => 'Updated By',
                'value' => 'updater.fullname',
            ],
            [
                'attribute' => 'updated_at',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'value' => function ($model) {
                    return $model->updated_at ? MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : '-';
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'clientOptions' => [
                        'dateFormat' => 'yy-mm-dd',
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
        ],
    ]);
    ?>
</div>