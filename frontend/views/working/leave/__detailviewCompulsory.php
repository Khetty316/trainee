<?php

use yii\widgets\DetailView;
use yii\bootstrap4\Html;
?>

<?=

DetailView::widget(array_merge(Yii::$app->params['detailViewOption28'], [
    'model' => $model,
    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
    'attributes' => [
        [
            'attribute' => 'start_date',
            'value' => function ($model) {
                return common\models\myTools\MyFormatter::asDate_Read($model->start_date);
            }
        ],
        [
            'attribute' => 'end_date',
            'value' => function ($model) {
                return common\models\myTools\MyFormatter::asDate_Read($model->end_date);
            }
        ],
        'days',
        [
            'attribute' => 'requestor',
            'value' => function ($model) {
                return $model->requestor0->fullname;
            }
        ],
        'requestor_remark:ntext',
        [
            'attribute' => 'status',
            'format' => 'raw',
            'value' => function ($model) {
                if ($model->status == frontend\models\office\leave\LeaveMaster::STATUS_Approved) {
                    return '<div class="text-success">' . $model->status0->remark . '</div>';
                } else if ($model->status == frontend\models\office\leave\LeaveMaster::STATUS_Rejected) {
                    return '<div class="text-danger">' . $model->status0->remark . '</div>';
                } else {
                    return $model->status0->remark;
                }
            }
        ],
        [
            'attribute' => 'approval_by',
            'value' => function ($model) {
                return $model->approvalBy->fullname ?? null;
            },
            'visible' => $all ?? false
        ],
        [
            'attribute' => 'approval_remark',
            'value' => function ($model) {
                return Html::encode($model->approval_remark);
            },
            'visible' => $all ?? false
        ]
    ],
]))
?>