<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
?>

<div>
    <?=
    DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'index',
            ],
            [
                'attribute' => 'appraisal_sts_name',
            ],
            [
                'attribute' => 'staff_id',
            ],
            [
                'attribute' => 'design_name',
            ],
            [
                'attribute' => 'date_of_join',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->date_of_join);
                }
            ],
            [
                'attribute' => 'appraise_by',
            ],
            [
                'attribute' => 'appraise_date',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->appraise_date);
                }
            ],
            [
                'attribute' => 'review_by_name',
            ],
            [
                'attribute' => 'review_date',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->review_date);
                }
            ],
        ]
    ]))
    ?>
</div>