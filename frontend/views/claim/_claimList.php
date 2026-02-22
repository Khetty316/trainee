<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

$user = common\models\User::findOne(Yii::$app->user->id);
?>
<div class="claim-master-index">
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
            [
                'attribute' => 'claim_code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->claim_code;
                }
            ],
            [
                'attribute' => 'claim_type',
                'format' => 'raw',
                'filter' => frontend\models\office\claim\RefClaimType::getDropDownList($user->grade),
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->claimType->claim_name;
                }
            ],
            [
                'attribute' => 'claimant_id',
                'label' => 'Claimant Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->claimant->fullname;
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return "By " . ($model->claimant->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
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
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'claim_status',
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->claimStatus->status_name;
                }
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($module) {
                    if ($module === 'personal') {
                        return Html::a('View <i class="far fa-eye"></i>', ['personal-view-claim', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                    } else if ($module === 'superior') {
                        return Html::a('View <i class="far fa-eye"></i>', ['superior-view-claim', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                    } else if ($module === 'finance') {
                        return Html::a('View <i class="far fa-eye"></i>', ['finance-view-claim', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                    }
                }
            ],
        //'created_by',
        //'created_at',
        //'updated_by',
        //'updated_at',
        //            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
</div>
