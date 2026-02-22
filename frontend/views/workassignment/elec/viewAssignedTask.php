<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyCommonFunction;

$project = $task->projProdPanel->projProdMaster;

$this->title = $task->projProdPanel->project_production_panel_code . " - " . ($task->elecTaskCode->name);
$this->params['breadcrumbs'][] = ['label' => 'Electrical Task Assignment'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-elec-project-list']];
$this->params['breadcrumbs'][] = ['label' => $project->project_production_code, 'url' => ['index-elec-project-panels', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <h4 class="col-12">
        <?php
        echo Html::a(Html::encode($project->project_production_code) . ' <i class="fas fa-external-link-square-alt fa-sm"></i>', "javascript:void(0)", ['class' => 'modalButtonMedium', 'value' => '/production/production/ajax-view-project-detail?id=' . $project->id]);
        ?>
        - <span class="h6"><?= Html::encode($project->name) ?></span>
    </h4>
    <h5 class="col-12">
        <?= ($this->title) ?>
    </h5>
</div>
<div class="row">
    <div class="col-12">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2  m-0 ">Task Assignment(s):</legend>
            <div class="table-responsive"> 
                <?php
                $dataProvider->sort = false;
                echo GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'quantity',
                            'label' => 'QTY',
                        ],
                        [
                            'attribute' => 'start_date',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return MyFormatter::asDate_Read($model->start_date);
                            }
                        ],
                        [
                            'attribute' => 'current_target_date',
                            'format' => 'raw',
                            'value' => function ($model) use ($task) {
                                $details = \frontend\models\projectproduction\electrical\TaskAssignElecTargetDateTrial::find()
                                        ->where(['task_assign_elec_id' => $model->id])
                                        ->orderBy(['created_at' => SORT_DESC])
                                        ->all();

                                if (!$details) {
                                    $html = '<span class="text-muted">-</span>';
                                } else {

                                    $html = '<div class="table-responsive">';
                                    $html .= '<table class="table table-bordered">';
                                    $html .= '<thead>';
                                    $html .= '<tr>';
                                    $html .= '<th width="20%">Target Date</th>';
                                    $html .= '<th width="40%">Set By</th>';
                                    $html .= '<th width="40%">Remark</th>';
                                    $html .= '</tr>';
                                    $html .= '</thead>';
                                    $html .= '<tbody>';

                                    foreach ($details as $index => $detail) {
                                        $isLatest = $index === 0 ? 'table-success fw-bold' : '';
                                        $html .= '<tr class="' . $isLatest . '">';

                                        // Target Date
                                        $html .= '<td>';
                                        $html .= MyFormatter::asDate_Read($detail->target_date);
                                        $html .= '</td>';

                                        // Set By
                                        $html .= '<td>';
                                        $html .= $detail->createdBy ? (Html::encode($detail->createdBy->fullname) . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($detail->created_at)) : '<em class="text-muted">-</em>';
                                        $html .= '</td>';

                                        // Remark
                                        $html .= '<td>';
                                        $html .= !empty($detail->remark) ? Html::encode($detail->remark) : '<em class="text-muted">No remark</em>';
                                        $html .= '</td>';

                                        $html .= '</tr>';
                                    }

                                    $html .= '</tbody>';
                                    $html .= '</table>';
                                    $html .= '</div>';
                                }
                                // Update button
                                $html .= '<div class="mt-2">';
                                $html .= Html::a("Update Target Date <i class='fas fa-edit'></i>",
                                        "javascript:",
                                        [
                                            "onclick" => "event.preventDefault();",
                                            "value" => \yii\helpers\Url::to(['update-target-date', 'id' => $model->id, 'taskId' => $task->id]),
                                            "class" => "modalButtonMedium btn btn-sm btn-primary",
                                            'data-modaltitle' => "Update Task Target Completion Date"
                                        ]
                                );
                                $html .= '</div>';

                                return $html;
                            }
                        ],
                        [
                            'attribute' => 'complete_date',
                            'format' => 'raw',
                            'value' => function ($model) {
                                if (empty($model->complete_date)) {
//                                    if ((Yii::$app->user->can(AuthItem::ROLE_Director) || $model->created_by == Yii::$app->user->id) && $model->active_sts) {
                                    if ((MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_Director]) || $model->created_by == Yii::$app->user->id) && $model->active_sts) {
                                        return Html::a('Add <i class="fas fa-plus"></i>', 'javascript:void(0)', ['value' => 'ajax-action-set-complete?id=' . $model->id, 'class' => 'btn btn-sm btn-success modalButton']);
                                    } else {
                                        return Html::a('Add <i class="fas fa-plus"></i>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-secondary', 'disabled' => true]);
                                    }
                                } else {
                                    return MyFormatter::asDate_Read($model->complete_date);
                                }
                            }
                        ],
                        [
                            'attribute' => 'comments',
                            'format' => 'ntext',
                            'value' => function ($model) {
                                $text = trim($model->comments);
//                            $completeTasks = $model->taskAssignElecCompletes;
//                            foreach ($completeTasks as $completeTask) {
//                                $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
//                                        . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
//                            }
                                return $text;
                            }
                        ],
                        [
                            'label' => 'Assigned Staffs',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $staffList = $model->taskAssignElecStaff;
                                $returnStr = "";
                                foreach ((array) $staffList as $key => $staff) {
                                    $returnStr .= ($key + 1) . " - " . Html::encode($staff->user->fullname) . "<br/>";
                                }

                                return $returnStr;
                            }
                        ],
                        [
                            'attribute' => 'active_sts',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->active_sts ? "Yes" : "<span class='text-danger'>No</span>";
                            }
                        ],
                        [
                            'attribute' => 'created_by',
                            'label' => 'Assigned By',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $createdBy = User::findOne($model->created_by);
                                return $createdBy->fullname . "<br/>" . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                            }
                        ],
                        [
                            'attribute' => 'created_by',
                            'label' => 'Set Completed By',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $text = '';
                                $allCompleteTasks = [];
                                $allCompleteTaskDeleted = [];

                                // Get all staff assigned to this task
                                $staffMembers = $model->taskAssignElecStaff;

                                if (empty($staffMembers)) {
                                    $text .= '<div class="alert alert-info">No staff assigned to this task.</div>';
                                    return $text;
                                }

                                // Collect all completed tasks from all staff members
                                foreach ($staffMembers as $staff) {
                                    if ($staff->taskAssignElecStaffCompletes) {
                                        $allCompleteTasks = array_merge($allCompleteTasks, $staff->taskAssignElecStaffCompletes);
                                    }
                                }

                                // Get deleted tasks
                                foreach ($staffMembers as $staff) {
                                    $deleted = frontend\models\projectproduction\electrical\TaskAssignElecStaffCompleteDelete::find()
                                            ->where(['task_assign_elec_staff_id' => $staff->id])
                                            ->all();
                                    $allCompleteTaskDeleted = array_merge($allCompleteTaskDeleted, $deleted);
                                }

                                $mergedArray = array_merge($allCompleteTasks, $allCompleteTaskDeleted);
                                if (!empty($mergedArray)) {
                                    usort($mergedArray, function ($a, $b) {
                                        $timeA = isset($a->deleted_at) ? strtotime($a->deleted_at) : strtotime($a->created_at);
                                        $timeB = isset($b->deleted_at) ? strtotime($b->complete_created_at) : strtotime($b->created_at);
                                        return $timeA - $timeB;
                                    });
                                }

                                $text .= '<table class="table table-bordered">';
                                $text .= '<thead>';
                                $text .= '<tr>';
                                $text .= '<th>Name</th>';
                                $text .= '<th>Panel Amounts</th>';
                                $text .= '<th>Assigned Staffs</th>';
                                $text .= '<th>Comment</th>';
                                $text .= '<th>Reverted By</th>';
                                $text .= '<th></th>';
                                $text .= '</tr>';
                                $text .= '</thead>';
                                $text .= '<tbody>';
                                if (!empty($mergedArray)) {
                                    foreach ($mergedArray as $item) {
                                        $taskAssignElecStaff = frontend\models\ProjectProduction\electrical\TaskAssignElecStaff::findOne($item->task_assign_elec_staff_id);
                                        $assignedStaff = User::findOne($taskAssignElecStaff->user_id);
                                        if (isset($item->deleted_at)) {
                                            $text .= '<tr>';
                                            $text .= '<td style="text-decoration: line-through; color: red;">' . $item->completeCreatedBy->fullname ?? null . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($item->complete_created_at) . '</td>';
                                            $text .= '<td class="text-right" style="text-decoration: line-through; color: red;">' . $item->quantity . '</td>';
                                            $text .= '<td class="text-right" style="text-decoration: line-through; color: red;">' . $assignedStaff->fullname . '</td>';
                                            $text .= '<td class="text-right" style="text-decoration: line-through; color: red;">' . $item->revert_comment . '</td>';
                                            $text .= '<td>' . $item->deletedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($item->deleted_at) . '<br>Comment: ' . $item->revert_comment . '</td>';
                                            $text .= '<td></td>';
                                            $text .= '</tr>';
                                        } else {
                                            $text .= '<tr>';
                                            $text .= '<td>' . $item->createdBy->fullname ?? null . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($item->created_at) . '</td>';
                                            $text .= '<td class="text-right">' . $item->quantity . '</td>';
                                            $text .= '<td>' . $assignedStaff->fullname . '</td>';
                                            $text .= '<td>' . $item->comment . '</td>';
                                            $text .= '<td></td>';
                                            if ($model->active_sts && MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director])) {
                                                $text .= '<td>' . Html::a('Revert <i class="fas fa-undo"></i>', 'javascript:void(0)', [
                                                            'value' => 'ajax-action-revert-set-complete?id=' . $item->id . '&taskId=' . $item->task_assign_elec_staff_id,
                                                            'class' => 'btn btn-sm btn-danger modalButton',
                                                            'title' => "Revert Completed Panel Amount",
                                                            'data-modaltitle' => "Revert Completed Panel Amount"
                                                        ])
                                                        . '</td>';
                                            } else {
                                                $text .= '<td>' . Html::a('Revert <i class="fas fa-undo"></i>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-secondary', 'disabled' => true]) . '</td>';
                                            }
                                            $text .= '</tr>';
                                        }
                                    }
                                } else {
                                    $text .= '<td colspan="5" class="text-center"> -- No completed tasks found -- </td>';
                                }
                                $text .= '</tbody>';
                                $text .= '</table>';
                                return $text;
                            }
                        ],
//                    [
//                        'attribute' => 'created_by',
//                        'label' => 'Set Completed By',
//                        'format' => 'raw',
//                        'value' => function ($model) {
//                            $text = '';
//                            $completeTasks = $model->taskAssignElecCompletes;
//                            $completeTaskDeleted = $model->taskAssignElecCompleteDeletes;
//                            $mergedArray = array_merge($completeTasks, $completeTaskDeleted);
//
//                            usort($mergedArray, function ($a, $b) {
//                                $timeA = isset($a->deleted_at) ? strtotime($a->deleted_at) : strtotime($a->created_at);
//                                $timeB = isset($b->deleted_at) ? strtotime($b->complete_created_at) : strtotime($b->created_at);
//                                return $timeA - $timeB;
//                            });
//
//                            $text .= '<table class="table table-bordered">';
//                            $text .= '<thead>';
//                            $text .= '<tr>';
//                            $text .= '<th>Name</th>';
//                            $text .= '<th>Panel Amounts</th>';
//                            $text .= '<th>Comment</th>';
//                            $text .= '<th>Reverted By</th>';
//                            $text .= '<th></th>';
//                            $text .= '</tr>';
//                            $text .= '</thead>';
//                            $text .= '<tbody>';
//                            if (!empty($mergedArray)) {
//                                foreach ($mergedArray as $item) {
//
//                                    if (isset($item->deleted_at)) {
//                                        $text .= '<tr>';
//                                        $text .= '<td style="text-decoration: line-through; color: red;">' . $item->completeCreatedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($item->complete_created_at) . '</td>';
//                                        $text .= '<td class="text-right" style="text-decoration: line-through; color: red;">' . $item->quantity . '</td>';
//                                        $text .= '<td class="text-right" style="text-decoration: line-through; color: red;">' . $item->complete_comment . '</td>';
//                                        $text .= '<td>' . $item->deletedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($item->deleted_at) . '<br>Comment: '. $item->revert_comment .'</td>';
//                                        $text .= '<td></td>';
//                                        $text .= '</tr>';
//                                    } else {
//                                        $text .= '<tr>';
//                                        $text .= '<td>' . $item->createdBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($item->created_at) . '</td>';
//                                        $text .= '<td class="text-right">' . $item->quantity . '</td>';
//                                        $text .= '<td>' . $item->comment . '</td>';
//                                        $text .= '<td></td>';
//                                        if ($model->active_sts && MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director])) {
//                                            $text .= '<td>' . Html::a('Revert <i class="fas fa-undo"></i>', 'javascript:void(0)', [
//                                                        'value' => 'ajax-action-revert-set-complete?id=' . $item->id . '&taskId=' . $item->task_assign_elec_id,
//                                                        'class' => 'btn btn-sm btn-danger modalButtonMedium',
//                                                        'title' => "Revert Completed Panel Amount",
//                                                        'data-modaltitle' => "Revert Completed Panel Amount"])
//                                                    . '</td>';
//                                        } else {
//                                            $text .= '<td>' . Html::a('Revert <i class="fas fa-undo"></i>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-secondary', 'disabled' => true]) . '</td>';
//                                        }
//                                        $text .= '</tr>';
//                                    }
//                                }
//                            } else {
//                                $text .= '<td colspan="5" class="text-center"> -- No completed tasks found -- </td>';
//                            }
//                            $text .= '</tbody>';
//                            $text .= '</table>';
//                            return $text;
//                        }
//                    ],
//                    [
//                        'attribute' => 'created_by',
//                        'label' => 'Set Completed By',
//                        'format' => 'raw',
//                        'value' => function ($model) {
//                            $text = '';
//                            $completeTaskTrails = $model->taskAssignElecCompleteTrails;
//                            $completeTaskDeleted = $model->taskAssignElecCompleteDeletes;
//
//                            if (!empty($completeTaskTrails)) {
//                                $text .= '<table class="table table-bordered">';
//                                $text .= '<thead>';
//                                $text .= '<tr>';
//                                $text .= '<th>Name</th>';
//                                $text .= '<th>Panel Amounts</th>';
//                                $text .= '<th>Comment</th>';
//                                $text .= '<th>Reverted By</th>';
//                                $text .= '<th></th>';
//                                $text .= '</tr>';
//                                $text .= '</thead>';
//                                $text .= '<tbody>';
//
//                                foreach ($completeTaskTrails as $completeTaskTrail) {
//                                    $isDeleted = false;
//                                    $deletedByInfo = '';
//                                    if (!empty($completeTaskDeleted)) {
//                                        foreach ($completeTaskDeleted as $deletedTask) {
//                                            if ($deletedTask->task_assign_elec_complete_id === $completeTaskTrail->task_assign_elec_complete_id) {
//                                                $isDeleted = true;
//                                                $deletedByInfo = $deletedTask->deletedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($deletedTask->deleted_at);
//                                                break;
//                                            }
//                                        }
//                                    }
//
//                                    if (!$isDeleted) {
//                                        $text .= '<tr>';
//                                        $text .= '<td>' . $completeTaskTrail->completeCreatedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($completeTaskTrail->complete_created_at) . '</td>';
//                                        $text .= '<td class="text-right">' . $completeTaskTrail->quantity . '</td>';
//                                        $text .= '<td>' . $completeTaskTrail->comment . '</td>';
//                                        $text .= '<td></td>';
//                                        if ($model->active_sts && MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnFab_Executive, AuthItem::ROLE_Director])) {
//                                            $text .= '<td>' . Html::a('Revert <i class="fas fa-undo"></i>', 'javascript:void(0)', [
//                                                        'value' => 'ajax-action-revert-set-complete?id=' . $completeTaskTrail->task_assign_elec_complete_id . '&taskId=' . $completeTaskTrail->task_assign_elec_id,
//                                                        'class' => 'btn btn-sm btn-danger modalButtonMedium',
//                                                        'title' => "Revert Completed Panel Amount",
//                                                        'data-modaltitle' => "Revert Completed Panel Amount"])
//                                                    . '</td>';
//                                        } else {
//                                            $text .= '<td>' . Html::a('Revert <i class="fas fa-undo"></i>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-secondary', 'disabled' => true]) . '</td>';
//                                        }
//                                        $text .= '</tr>';
//                                    } else {
//                                        $text .= '<tr>';
//                                        $text .= '<td style="text-decoration: line-through; color: red;">' . $completeTaskTrail->completeCreatedBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($completeTaskTrail->complete_created_at) . '</td>';
//                                        $text .= '<td class="text-right" style="text-decoration: line-through; color: red;">' . $completeTaskTrail->quantity . '</td>';
//                                        $text .= '<td style="text-decoration: line-through; color: red;">' . $completeTaskTrail->comment . '</td>';
//                                        $text .= '<td>' . $deletedByInfo . '</td>';
//                                        $text .= '<td></td>';
//                                        $text .= '</tr>';
//                                    }
//                                }
//
//                                $text .= '</tbody>';
//                                $text .= '</table>';
//                            }
//                            return $text;
//                        }
//                    ],
                        //'updated_at',
                        //'updated_by',
                        [
                            'format' => 'raw',
                            'value' => function ($model) {
//                                if ((Yii::$app->user->can(AuthItem::ROLE_Director) || $model->created_by == Yii::$app->user->id) && $model->active_sts) {
                                if ((MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_Director]) || $model->created_by == Yii::$app->user->id) && $model->active_sts) {
                                    return Html::a("<i class='far fa-edit text-success'></i>", ['update-assign-task', 'taskAssignId' => $model->id])
                                            . Html::a('<i class="fas fa-times-circle text-danger"></i>', ['deactivate-assign-task', 'taskAssignId' => $model->id], ['data' => ['method' => 'post', 'confirm' => 'Are you sure to deactivate?'], 'class' => 'mx-2']);
                                } else {
                                    return "<i class='far fa-edit text-secondary'></i>" . '<i class="fas fa-times-circle text-secondary mx-2"></i>';
                                }
                            }
                        ],
//                    ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]));
                ?>
            </div>
        </fieldset>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2  m-0 ">Complaints:</legend>
            <?=
            Html::a('Make a complaint <i class="fas fa-plus"></i>', 'javascript:void(0)', ['value' => 'ajax-make-complaint?taskId=' . $task->id, 'class' => 'btn btn-sm btn-success modalButton mb-2']);
            ?>
            <?=
            GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
                'dataProvider' => $errorData,
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => ['width' => '15%;'],
                    ],
                    [
                        'label' => 'Error',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $refProjErr = $model->errorCode;
                            return $refProjErr->description;
                        }
                    ],
                    [
                        'format' => 'raw',
                        'headerOptions' => ['width' => '15%;'],
                        'value' => function ($model) {
//                            if ((Yii::$app->user->can(AuthItem::ROLE_Director) || $model->created_by == Yii::$app->user->id)) {
                            if ((MyCommonFunction::checkRoles([AuthItem::ROLE_PrdnElec_Executive, AuthItem::ROLE_Director]) || $model->created_by == Yii::$app->user->id)) {
                                return Html::a("<i class='far fa-edit text-success'></i>", 'javascript:void(0)', ['value' => 'ajax-make-complaint?complaintId=' . $model->id, 'class' => 'modalButton'])
                                        . Html::a('<i class="fas fa-times-circle text-danger"></i>', ['delete-complaint', 'id' => $model->id], ['data' => ['method' => 'post', 'confirm' => 'Are you sure to delete this complaint?'], 'class' => 'mx-2']);
                            }
                        }
                    ],
                ],
            ]));
            ?>
        </fieldset>
    </div>
</div>
