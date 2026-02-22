<?php

use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
?>

<div>
    <?=
    DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'description',
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status0->sts_name;
                }
            ],
            [
                'attribute' => 'appraisal_start_date',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->appraisal_start_date);
                }
            ],
            [
                'attribute' => 'appraisal_end_date',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->appraisal_end_date);
                }
            ],
            [
                'attribute' => 'rating_end_date',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->rating_end_date);
                }
            ],
        ]
    ]))
    ?>
</div>