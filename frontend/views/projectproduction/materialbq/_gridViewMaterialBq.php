<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\ProjectProduction\RefProjProdBqStatus;

$isMain = $isMain ?? false;
?>
<?=
GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'bq_no',
            'format' => 'raw',
            'value' => function($model) use ($isMain) {
                return Html::a($model->bq_no, ['update-material-bq', 'bqMasterId' => $model->id, 'fromMain' => $isMain]);
            }
        ],
        [
            'visible' => $isMain,
            'attribute' => 'project_code',
        ],
        [
            'visible' => $isMain,
            'attribute' => 'panel_code'
        ],
        [
            'attribute' => 'bq_status',
            'label' => 'Status',
            'filter' => RefProjProdBqStatus::getDropDownList(),
            'format' => 'raw',
            'value' => function($model) {
                return $model->bqStatus->status_name ?? null;
            }
        ],
        [
            'attribute' => 'created_at',
            'format' => 'raw',
            'value' => function($model) {
                return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
            }
        ],
        [
            'attribute' => 'created_by',
            'format' => 'raw',
            'value' => function($model) {
                return User::findOne($model->created_by)->fullname ?? null;
            }
        ],
    ],
]));
?>

