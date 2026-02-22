<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use common\models\User;
?>

<div class="project-production-master-view">
    <?=
    DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
        'model' => $model, // Model = ProjectProductionMaster
        'attributes' => [
            'name',
            [
                'attribute' => 'project_type',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->projectType;
                }
            ],
            [
                'attribute' => 'quotation_id',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->quotationNo;
                }
            ],
                    [
                'attribute' => 'amount',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->amount;
                }
            ],
            [
                'attribute' => 'client_id',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->client->company_name ?? null;
                }
            ],
            'remark:ntext',
        ],
    ]))
    ?>
</div>