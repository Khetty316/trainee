<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;
?>
<div class="vfab-staff-production-index">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('__navbarTask', ['pageKey' => '1']) ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]);     ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Task - Fabrication</legend>
        <div class="table-responsive">
            <?=
            GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
                'dataProvider' => $dataProviderFab,
                'filterModel' => $searchModelFab,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'task_name',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a($model->task_name, 'javascript:void(0)', ['value' => 'ajax-fab-set-complete?id=' . $model->task_assign_fab_id, 'class' => 'modalButton']);
                        }
                    ],
                    [
                        'attribute' => 'panel_code',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $returnStr = $model->panel_code;
                            if (!empty($model->filename)) {
                                $returnStr .= Html::a('<i class="fas fa-file-alt fa-lg"></i>',
                                        ['/production/production/get-panel-file-by-panel-id', 'panelId' => $model->panel_id],
                                        ['class' => 'text-warning m-2', 'target' => '_blank']);
                            }
                            return $returnStr;
                        }
                    ],
                    [
                        'attribute' => 'panel_description',
                        'contentOptions' => ['style' => 'white-space: inherit!important'],
                    ],
                    'assigned_qty',
                    [
                        'attribute' => 'assigned_complete_qty_individual',
                        'label' => 'Complete Qty',
                    ],
                    [
                        'attribute' => 'assigned_start_date',
                        'label' => 'Assigned Start Date',
                        'filter' => DatePicker::widget([
                            'model' => $searchModelFab,
                            'attribute' => 'assigned_start_date',
                            'dateFormat' => 'php:d-m-Y',
                            'options' => ['class' => 'form-control'],
                        ]),
                        'value' => function ($model) {
                            return MyFormatter::asDate_Read($model->assigned_start_date);
                        }
                    ],
                    [
                        'attribute' => 'assigned_current_target_date',
                        'label' => 'Task Target Completion Date',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-center'],
                        'value' => function ($model) {
                            if (!$model->assigned_current_target_date) {
                                return '-';
                            }

                            $today = new \DateTime();
                            $target = new \DateTime($model->assigned_current_target_date);

                            // calculate days difference
                            $diff = (int) $today->diff($target)->format('%r%a');

                            // default styles
                            $bg = 'transparent';
                            $clr = '#000';

                            if ($model->assigned_complete_date === null) {
                                if ($diff < 0) {
                                    $bg = '#dc3545'; // Bootstrap danger red
                                    $clr = '#fff';
                                } elseif ($diff <= 4) {
                                    $bg = '#ffc107'; // Bootstrap warning yellow/orange
                                    $clr = '#000';    // black text for better contrast
                                }
                            } else if ($model->assigned_complete_date !== null) {
                                $bg = '#28a745';
                                $clr = '#fff';
                            }

                            return Html::tag(
                                    'span',
                                    MyFormatter::asDate_Read($model->assigned_current_target_date),
                                    [
                                        'class' => "align-text: center;",
                                        'style' => "background-color: {$bg}; color: {$clr}; padding: 3px 8px; border-radius: 4px;"
                                    ]
                            );
                        },
                        'filter' => yii\jui\DatePicker::widget([
                            'model' => $searchModelFab,
                            'attribute' => 'assigned_current_target_date',
                            'language' => 'en',
                            'dateFormat' => 'php:d/m/Y',
                            'options' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'onchange' => '$("#w0").yiiGridView("applyFilter")',
                            ],
                        ]),
                    ],
//                [
//                    'attribute' => 'assigned_comments',
//                    'format' => 'ntext',
//                    'value' => function ($model) {
//                        $text = trim($model->assigned_comments);
//                        $completeTasks = $model->taskAssignFabCompletes;
//                        foreach ($completeTasks as $completeTask) {
//                            $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
//                                    . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
//                        }
//                        return $text;
//                    }
//                ],
                    [
                        'attribute' => 'assigned_comments',
                        'format' => 'ntext',
                        'value' => function ($model) {
                            $text = trim($model->assigned_comments);
                            $taskAssignFab = \frontend\models\projectproduction\fabrication\TaskAssignFab::findOne($model->task_assign_fab_id);
                            $taskAssignFabStaffs = $taskAssignFab->taskAssignFabStaff;
                            foreach ($taskAssignFabStaffs as $taskAssignFabStaff) {
                                $completeTasks = $taskAssignFabStaff->taskAssignFabStaffCompletes;
                                foreach ($completeTasks as $completeTask) {
                                    $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
                                            . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
                                }
                            }

                            return $text;
                        }
                    ],
                    'assigner_fullname',
                    [
                        'attribute' => 'created_at',
                        'filter' => DatePicker::widget([
                            'model' => $searchModelFab,
                            'attribute' => 'created_at',
                            'dateFormat' => 'php:d-m-Y',
                            'options' => ['class' => 'form-control'],
                        ]),
                        'value' => function ($model) {
                            return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                        }
                    ],
                ],
            ]));
            ?>
        </div>
    </fieldset>

    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0">Task - Electrical</legend>
        <div class="table-responsive">
            <?=
            GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
                'dataProvider' => $dataProviderElec,
                'filterModel' => $searchModelElec,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'task_name',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return Html::a($model->task_name, 'javascript:void(0)', ['value' => 'ajax-elec-set-complete?id=' . $model->task_assign_elec_id, 'class' => 'modalButton']);
                        }
                    ],
                    [
                        'attribute' => 'panel_code',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $returnStr = $model->panel_code;
                            if (!empty($model->filename)) {
                                $returnStr .= Html::a('<i class="fas fa-file-alt fa-lg"></i>',
                                        ['/production/production/get-panel-file-by-panel-id', 'panelId' => $model->panel_id],
                                        ['class' => 'text-warning m-2', 'target' => '_blank']);
                            }
                            return $returnStr;
                        }
                    ],
                    [
                        'attribute' => 'panel_description',
                        'contentOptions' => ['style' => 'white-space: inherit!important'],
                    ],
                    'assigned_qty',
                    [
                        'attribute' => 'assigned_complete_qty_individual',
                        'label' => 'Complete Qty',
                    ],
                    [
                        'attribute' => 'assigned_start_date',
                        'label' => 'Assigned Start Date',
                        'filter' => DatePicker::widget([
                            'model' => $searchModelElec,
                            'attribute' => 'assigned_start_date',
                            'dateFormat' => 'php:d-m-Y',
                            'options' => ['class' => 'form-control'],
                        ]),
                        'value' => function ($model) {
                            return MyFormatter::asDate_Read($model->assigned_start_date);
                        }
                    ],
                    [
                        'attribute' => 'assigned_current_target_date',
                        'label' => 'Task Target Completion Date',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-center'],
                        'value' => function ($model) {
                            if (!$model->assigned_current_target_date) {
                                return '-';
                            }

                            $today = new \DateTime();
                            $target = new \DateTime($model->assigned_current_target_date);

                            // calculate days difference
                            $diff = (int) $today->diff($target)->format('%r%a');

                            // default styles
                            $bg = 'transparent';
                            $clr = '#000';

                            if ($model->assigned_complete_date === null) {
                                if ($diff < 0) {
                                    $bg = '#dc3545'; // Bootstrap danger red
                                    $clr = '#fff';
                                } elseif ($diff <= 4) {
                                    $bg = '#ffc107'; // Bootstrap warning yellow/orange
                                    $clr = '#000';    // black text for better contrast
                                }
                            } else if ($model->assigned_complete_date !== null) {
                                $bg = '#28a745';
                                $clr = '#fff';
                            }

                            return Html::tag(
                                    'span',
                                    MyFormatter::asDate_Read($model->assigned_current_target_date),
                                    [
                                        'class' => "align-text: center;",
                                        'style' => "background-color: {$bg}; color: {$clr}; padding: 3px 8px; border-radius: 4px;"
                                    ]
                            );
                        },
                        'filter' => yii\jui\DatePicker::widget([
                            'model' => $searchModelElec,
                            'attribute' => 'assigned_current_target_date',
                            'language' => 'en',
                            'dateFormat' => 'php:d/m/Y',
                            'options' => [
                                'class' => 'form-control',
                                'autocomplete' => 'off',
                                'onchange' => '$("#w0").yiiGridView("applyFilter")',
                            ],
                        ]),
                    ],
//                [
//                    'attribute' => 'assigned_comments',
//                    'format' => 'ntext',
//                    'value' => function ($model) {
//                        $text = trim($model->assigned_comments);
//                        $completeTasks = $model->taskAssignElecCompletes;
//                        foreach ($completeTasks as $completeTask) {
//                            $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
//                                    . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
//                        }
//                        return $text;
//                    }
//                ],
                    [
                        'attribute' => 'assigned_comments',
                        'format' => 'ntext',
                        'value' => function ($model) {
                            $text = trim($model->assigned_comments);
                            $taskAssignElec = \frontend\models\projectproduction\electrical\TaskAssignElec::findOne($model->task_assign_elec_id);
                            $taskAssignElecStaffs = $taskAssignElec->taskAssignElecStaff;
                            foreach ($taskAssignElecStaffs as $taskAssignElecStaff) {
                                $completeTasks = $taskAssignElecStaff->taskAssignElecStaffCompletes;
                                foreach ($completeTasks as $completeTask) {
                                    $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
                                            . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
                                }
                            }

                            return $text;
                        }
                    ],
                    'assigner_fullname',
                    [
                        'attribute' => 'created_at',
                        'filter' => DatePicker::widget([
                            'model' => $searchModelElec,
                            'attribute' => 'created_at',
                            'dateFormat' => 'php:d-m-Y',
                            'options' => ['class' => 'form-control'],
                        ]),
                        'value' => function ($model) {
                            return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                        }
                    ],
                ],
            ]));
            ?>
        </div>
    </fieldset>
</div>
