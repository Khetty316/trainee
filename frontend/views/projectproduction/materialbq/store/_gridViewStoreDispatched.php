<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;

$isMain = $isMain ?? false;
?>
<?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
<?=
GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'dispatch_no',
            'format' => 'raw',
            'value' => function($model) {
                return Html::a($model->dispatch_no, ['view-store-dispatch', 'dispatchId' => $model->id]);
            }
        ],
        [
            'attribute' => 'fab_bq_master_id',
            'label' => 'B.Q. No.',
            'format' => 'raw',
            'value' => function($model) {
//                return Html::a($model->fabBqMaster->bq_no, ['view-store-dispatch', 'dispatchId' => $model->id]);
                return $model->fabBqMaster->bq_no ?? null;
            }
        ],
        [
            'attribute' => 'dispatched_at',
            'format' => 'raw',
            'value' => function($model) {
                return MyFormatter::asDateTime_ReaddmYHi($model->dispatched_at);
            }
        ],
        [
            'attribute' => 'dispatched_by',
            'format' => 'raw',
            'value' => function($model) {
                return (User::findOne($model->dispatched_by)->fullname ?? null);
            }
        ],
        [
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \frontend\models\ProjectProduction\RefProdDispatchStatus::getDropDownList(),
            'value' => function($model) {
                return $model->status0->status_name;
            }
        ],
        [
            'attribute' => 'responded_at',
            'format' => 'raw',
            'value' => function($model) {

                return MyFormatter::asDateTime_ReaddmYHi($model->responded_at);
            }
        ],
        [
            'attribute' => 'responded_by',
            'format' => 'raw',
            'value' => function($model) {
                return (User::findOne($model->responded_by)->fullname ?? null);
            }
        ],
    ],
]));
?>

