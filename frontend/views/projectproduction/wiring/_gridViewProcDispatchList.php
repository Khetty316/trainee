<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;

$viewAllColumns = $viewAllColumns ?? false;
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
                return Html::a($model->dispatch_no, ['view-to-receive-item', 'dispatchId' => $model->id]);
            }
        ],
        [
            'attribute' => 'proj_prod_panel_id',
            'format' => 'raw',
            'value' => function($model) {
                return $model->projProdPanel->project_production_panel_code;
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
            'visible' => $viewAllColumns,
            'attribute' => 'status',
            'format' => 'raw',
            'filter' => \frontend\models\ProjectProduction\RefProdDispatchStatus::getDropDownList(),
            'value' => function($model) {
                return $model->status0->status_name;
            }
        ],
        [
            'visible' => $viewAllColumns,
            'attribute' => 'responded_at',
            'format' => 'raw',
            'value' => function($model) {

                return MyFormatter::asDateTime_ReaddmYHi($model->responded_at);
            }
        ],
        [
            'visible' => $viewAllColumns,
            'attribute' => 'responded_by',
            'format' => 'raw',
            'value' => function($model) {
                return (User::findOne($model->responded_by)->fullname ?? null);
            }
        ],
    ],
]));
?>

