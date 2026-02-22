<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;
use common\modules\auth\models\AuthItem;

echo GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
    'filterModel' => $searchModel,
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'panelCode',
            'format' => 'raw',
            'value' => function ($model) {
                $project = $model->projProdPanel->projProdMaster;
                $projCode = $project->project_production_code;
                $panelCode = $model->panelCode;
                $panelCode = str_replace($projCode, "", $panelCode);
                return Html::a($projCode, ['index-elec-project-panels', 'id' => $project->id]) . $panelCode;
            }
        ],
        [
            'attribute' => 'taskCode',
            'filter' => frontend\models\ProjectProduction\electrical\RefProjProdTaskElec::getDropDownList(),
            'format' => 'raw',
            'value' => function ($model) {
                return $model->prodElecTask->elecTaskCode->name ?? null;
            }
        ],
        [
            'attribute' => 'quantity',
            'headerOptions' => ['class' => 'tdnowrap'],
            'label' => 'QTY'
        ],
//        [
//            'attribute' => 'start_date',
//            'headerOptions' => ['class' => 'tdnowrap'],
//            'format' => 'raw',
//            'value' => function ($model) {
//                return MyFormatter::asDate_Read($model->start_date);
//            }
//        ],
        [
            'attribute' => 'start_date',
            'headerOptions' => ['class' => 'tdnowrap'],
            'format' => 'raw',
            'value' => function ($model) {
                return MyFormatter::asDate_Read($model->start_date);
            },
            'filter' => yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'start_date',
                'language' => 'en',
                'dateFormat' => 'php:d/m/Y',
                'options' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'onchange' => '$("#w0").yiiGridView("applyFilter")',
                ],
                'clientOptions' => [
                    'altFormat' => 'yy-mm-dd', // Format for sending to the server
                    'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'start_date'), // Hidden input for sending formatted date
                ]
            ]),
        ],
        [
            'attribute' => 'current_target_date',
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) {
                if (!$model->current_target_date) {
                    return '-';
                }

                $today = new \DateTime();
                $target = new \DateTime($model->current_target_date);

                // calculate days difference
                $diff = (int) $today->diff($target)->format('%r%a');

                // default styles
                $bg = 'transparent';
                $clr = '#000';

                if ($model->complete_date === null) {
                    if ($diff < 0) {
                        $bg = '#dc3545'; // Bootstrap danger red
                        $clr = '#fff';
                    } elseif ($diff <= 4) {
                        $bg = '#ffc107'; // Bootstrap warning yellow/orange
                        $clr = '#000';    // black text for better contrast
                    }
                } else if ($model->complete_date !== null) {
                    $bg = '#28a745';
                    $clr = '#fff';
                }

                return Html::tag(
                        'span',
                        MyFormatter::asDate_Read($model->current_target_date),
                        [
                            'class' => "align-text: center;",
                            'style' => "background-color: {$bg}; color: {$clr}; padding: 3px 8px; border-radius: 4px;"
                        ]
                );
            },
            'filter' => yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'current_target_date',
                'language' => 'en',
                'dateFormat' => 'php:d/m/Y',
                'options' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'onchange' => '$("#w0").yiiGridView("applyFilter")',
                ],
                'clientOptions' => [
                    'altFormat' => 'yy-mm-dd', // Format for sending to the server
                    'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'current_target_date'), // Hidden input for sending formatted date
                ]
            ]),
        ],
        [
            'attribute' => 'complete_date',
            'headerOptions' => ['class' => 'tdnowrap'],
            'format' => 'raw',
            'contentOptions' => ['class' => 'text-center'],
            'value' => function ($model) use ($toIndex) {
                if (empty($model->complete_date)) {
                    if ((Yii::$app->user->can(AuthItem::ROLE_PrdnElec_Executive) || $model->created_by == Yii::$app->user->id) && $model->active_sts) {
                        return Html::a('Add <i class="fas fa-plus"></i>', 'javascript:void(0)', ['value' => 'ajax-action-set-complete?id=' . $model->id . "&toIndex=" . ($toIndex ?? ""), 'class' => 'btn btn-sm btn-success modalButton']);
                    } else {
                        return Html::a('Add <i class="fas fa-plus"></i>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-secondary', 'disabled' => true]);
                    }
                } else {
                    return MyFormatter::asDate_Read($model->complete_date);
                }
            }
        ],
        'comments:ntext',
        [
            'attribute' => 'assignee',
            'label' => 'Assigned Staffs',
            'format' => 'raw',
            'value' => function ($model) {
                $staffList = explode(";", $model->assignee);
                $returnStr = "";
                foreach ((array) $staffList as $key => $staff) {
                    $returnStr .= ($key + 1) . " - " . Html::encode($staff) . "<br/>";
                }
                return $returnStr;
            }
        ],
        [
            'attribute' => 'active_sts',
            'headerOptions' => ['class' => 'tdnowrap'],
            'filter' => [1 => 'Yes', 0 => 'No'],
            'format' => 'raw',
            'value' => function ($model) {
                return $model->active_sts ? "Yes" : "<span class='text-danger'>No</span>";
            }
        ],
        [
            'attribute' => 'created_by',
            'label' => 'Assigned By',
            'headerOptions' => ['class' => 'tdnowrap'],
            'format' => 'raw',
            'value' => function ($model) {
                $createdBy = User::findOne($model->created_by);
                return $createdBy->fullname;
            }
        ],
        [
            'attribute' => 'created_at',
            'label' => 'Assigned At',
            'headerOptions' => ['class' => 'tdnowrap'],
            'format' => 'raw',
            'value' => function ($model) {
                return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
            },
            'filter' => yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'created_at',
                'language' => 'en',
                'dateFormat' => 'php:d/m/Y',
                'options' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'onchange' => '$("#w0").yiiGridView("applyFilter")',
                ],
                'clientOptions' => [
                    'altFormat' => 'yy-mm-dd', // Format for sending to the server
                    'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at'), // Hidden input for sending formatted date
                ]
            ]),
        ],
        [
            'format' => 'raw',
            'headerOptions' => ['class' => 'tdnowrap'],
            'value' => function ($model) use ($toIndex) {
                if ((Yii::$app->user->can(AuthItem::ROLE_PrdnElec_Executive) || $model->created_by == Yii::$app->user->id) && $model->active_sts) {
                    return Html::a("<i class='far fa-edit text-success'></i>", ['update-assign-task', 'taskAssignId' => $model->id, 'toIndex' => ($toIndex ?? "")], ['class' => 'mx-1'])
                            . Html::a('<i class="fas fa-times-circle text-danger"></i>', ['deactivate-assign-task', 'taskAssignId' => $model->id, 'toIndex' => ($toIndex ?? "")], ['data' => ['method' => 'post', 'confirm' => 'Are you sure to deactivate?'], 'class' => 'mx-1']);
                } else {
                    return "<i class='far fa-edit text-secondary'></i>" . '<i class="fas fa-times-circle text-secondary mx-2"></i>';
                }
            }
        ],
//                    ['class' => 'yii\grid\ActionColumn'],
    ],
]))
?>


