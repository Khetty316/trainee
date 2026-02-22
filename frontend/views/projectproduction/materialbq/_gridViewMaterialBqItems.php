<?php

use yii\grid\GridView;
use common\models\myTools\MyFormatter;

?>
<?=

GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'headerOptions' => ['class' => 'text-right tdnowrap'],
            'contentOptions' => ['class' => 'text-right'],
            'class' => 'yii\grid\SerialColumn'
        ],
        [
            'attribute' => 'item_description',
        ],
        [
            'attribute' => 'quantity',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right col-2'],
            'format' => 'raw',
            'value' => function($model) {
                return MyFormatter::asDecimal2($model->quantity);
            }
        ],
        [
            'headerOptions' => ['class' => 'col-2'],
            'attribute' => 'unit_code',
            'format' => 'raw',
            'value' => function($model) {
                return $model->unitCode->unit_name_single;
            }
        ],
    ],
]));
?>
