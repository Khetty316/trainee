<?php

use yii\bootstrap4\Html;
use common\models\myTools\MyFormatter;
use yii\widgets\DetailView;
?>

<div>
    <?=
    DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'equipment_code',
            ],
            [
                'attribute' => 'equipment_description',
            ],
            [
                'attribute' => 'remark',
            ],
            [
                'attribute' => 'next_service_date',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->next_service_date);
                }
            ],
        ]
    ]))
    ?>

    <div>
        <?= Html::a('Update', 'javascript:void(0);', ['class' => 'close-update float-right btn btn-success', 'onclick' => 'closeAndUpdate(' . $model->id . ');']) ?>
        <?= Html::a('Duplicate', 'javascript:void(0);', ['class' => 'close-duplicate float-right btn btn-warning mr-2', 'onclick' => 'closeAndDuplicate(' . $model->id . ');']) ?>
    </div>
</div>