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

    <!--<h3><?php //= Html::encode($this->title)   ?></h3>-->

    <p>
        <?=
        Html::a('Create New', "javascript:void(0)", [
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
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
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
                        return ($model->created_at === '0000-00-00 00:00:00' ? null :  MyFormatter::asDateTime_ReaddmYHi($model->created_at));
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
                        return ($model->updated_at === '0000-00-00 00:00:00' ? null :  MyFormatter::asDateTime_ReaddmYHi($model->updated_at));
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
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return Html::a('Update <i class="fa fa-edit"></i>', "javascript:void(0)", [
                        'title' => "Update Detail",
                        'value' => yii\helpers\Url::to(['update', 'id' => $model->currency_id]),
                        'class' => 'modalButtonMedium btn btn-sm btn-success text-center',
                        'data-modaltitle' => 'Update Detail',
                    ]);
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
