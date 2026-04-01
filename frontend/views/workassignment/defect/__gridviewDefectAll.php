<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;
use frontend\models\projectproduction\task\TaskAssignment;
use frontend\models\projectproduction\VProductionTasksError;

echo GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
    'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'task_type',
            'filter' => ['elec' => 'Electrical', 'fab' => 'Fabrication'],
            'format' => 'raw',
            'value' => function ($model) {
                return $model->task_type == TaskAssignment::taskTypeElectrical ? 'Electrical' : 'Fabrication';
            }
        ],
        [
            'attribute' => 'panel_code',
        ],
        [
            'attribute' => 'task_name',
            'filter' => VProductionTasksError::getDropDownListTaskType(),
            'format' => 'raw',
        ],
        'description:ntext',
        [
            'attribute' => 'remark',
            'format' => 'ntext',
            'contentOptions' => [
                'style' => 'white-space: normal; word-break: break-word; max-width: 300px; text-align: left;'
            ],
        ],
        [
            'attribute' => 'created_by',
            'label' => 'Complaint By',
            'headerOptions' => ['class' => 'tdnowrap'],
            'filter' => User::getActiveDropDownList(),
            'format' => 'raw',
            'value' => function ($model) {
                $createdBy = User::findOne($model->created_by);
                return $createdBy->fullname;
            }
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Complaint At',
            'headerOptions' => ['class' => 'tdnowrap'],
            'format' => 'raw',
            'value' => function ($model) {
                return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
            }
        ],
//        [
//            'attribute' => 'created_at',
//            'label' => 'Complaint At',
//            'headerOptions' => ['class' => 'tdnowrap'],
//            'format' => 'raw',
//            'value' => function ($model) {
//                return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
//            }
//        ],
        [
            'attribute' => 'is_read',
            'label' => 'Read',
            'format' => 'raw',
            'filter' => ['2' => 'Yes', '1' => 'No'],
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                return ($model->is_read == 2) ? '<i class="far fa-check-circle text-success"></i>' : '<i class="far fa-times-circle text-danger"></i>';
            }
        ],
        [
            'format' => 'raw',
            'value' => function ($model) {
                $taskLink = Html::a('View Task <i class="fas fa-tasks"></i>',
                        $model->task_type == TaskAssignment::taskTypeFabrication ? ['fab-task/view-assigned-task', 'taskId' => $model->production_task_id] : ['elec-task/view-assigned-task', 'taskId' => $model->production_task_id],
                        [
                            'class' => 'btn btn-success btn-sm',
                            'title' => 'View Task Assignment',
                            'target' => '_blank'
                        ]
                );

                $staffLink = Html::a('View Staff <i class="fas fa-users "></i>',
                        'javascript:void(0)',
                        [
                            'class' => 'btn btn-primary btn-sm modalButton',
                            'title' => 'View Staff',
                            'value' => $model->task_type == TaskAssignment::taskTypeFabrication ? \yii\helpers\Url::to(['view-task-fab-defect-staff', 'complaintId' => $model->id]) : \yii\helpers\Url::to(['view-task-elec-defect-staff', 'complaintId' => $model->id]),
                        ]
                );

                return $taskLink . ' ' . $staffLink;
            }
        ],
    ],
]));

