<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use yii\jui\DatePicker;
?>
<div class="vfab-staff-production-index">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('__navbarTask', ['pageKey' => '2']) ?>
    <div class="table-responsive">
        <?=
        GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'task_type',
                    'filter' => ['elec' => "Electrical", 'fab' => "Fabrication"],
                    'value' => function ($model) {
                        return $model->task_type == "elec" ? "Electrical" : "Fabrication";
                    }
                ],
                [
                    'attribute' => 'task_name',
                    'format' => 'raw',
                    'filter' => array_merge(RefProjProdTaskFab::getActiveDropDownList1(), RefProjProdTaskElec::getActiveDropDownList1()),
//                'value' => function ($model) {
//                    return Html::a($model->task_name, 'javascript:void(0)', ['value' => 'ajax-fab-set-complete?id=' . $model->task_assign_elec_id, 'class' => 'modalButton']);
//                }
                ],
                'panel_code',
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
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'assigned_start_date',
                        'language' => 'en',
                        'dateFormat' => 'php:d-m-Y',
                        'options' => ['class' => 'form-control'],
                    ]),
                    'value' => function ($model) {
                        return MyFormatter::asDate_Read($model->assigned_start_date);
                    },
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
                        'model' => $searchModel,
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
                [
                    'attribute' => 'assigned_complete_date_individual',
                    'label' => 'Assigned Complete Date',
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'assigned_complete_date_individual',
                        'dateFormat' => 'php:d-m-Y',
                        'options' => ['class' => 'form-control'],
                    ]),
                    'value' => function ($model) {
                        return MyFormatter::asDate_Read($model->assigned_complete_date_individual);
                    }
                ],
//                [
//                    'attribute' => 'assigned_comments',
//                    'format' => 'ntext',
//                    'value' => function ($model) {
//                        $text = $model->assigned_comments;
//                        $completeTasks = ($model->task_type == "fab") ? $model->taskAssignFabCompletes : $model->taskAssignElecCompletes;
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

                        if ($model->task_type == "fab") {
                            $taskAssignFab = \frontend\models\projectproduction\fabrication\TaskAssignFab::findOne($model->task_assign_elec_id);
                            $taskAssignFabStaffs = $taskAssignFab->taskAssignFabStaff;
                            foreach ($taskAssignFabStaffs as $taskAssignFabStaff) {
                                $completeTasks = $taskAssignFabStaff->taskAssignFabStaffCompletes;
                                foreach ($completeTasks as $completeTask) {
                                    $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
                                            . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
                                }
                            }
                        } else {
                            // Assuming "elec" type
                            $taskAssignElec = \frontend\models\projectproduction\electrical\TaskAssignElec::findOne($model->task_assign_elec_id);
                            $taskAssignElecStaffs = $taskAssignElec->taskAssignElecStaff;
                            foreach ($taskAssignElecStaffs as $taskAssignElecStaff) {
                                $completeTasks = $taskAssignElecStaff->taskAssignElecStaffCompletes;
                                foreach ($completeTasks as $completeTask) {
                                    $text .= $completeTask->comment ? ("\r\n -----------------------------\r\n" . trim($completeTask->comment) . "\r\nBy: "
                                            . $completeTask->createdBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($completeTask->created_at)) : null;
                                }
                            }
                        }

                        return $text;
                    }
                ],
                'assigner_fullname',
                [
                    'attribute' => 'created_at',
                    'filter' => DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'dateFormat' => 'php:d-m-Y',
                        'options' => ['class' => 'form-control'],
                    ]),
                    'value' => function ($model) {
                        return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                ],
                [
                    'attribute' => 'deactivated_by_fullname',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if (!$model->assigned_active_status) {
                            return $model->deactivated_by_fullname . "<br/>@ " . MyFormatter::asDateTime_ReaddmYHi($model->deactivated_at);
                        } else {
//                        return $model->deactivated_by_fullname . "<br/>@ " . MyFormatter::asDateTime_ReaddmYHi($model->deactivated_at);
                        }
                    }
                ],
            ],
        ]));
        ?>
    </div>
</div>
<script>
    $(document).on('pjax:beforeSend', function (event, xhr, settings) {
        var formData = new URLSearchParams(settings.data);
        var dateInputName = 'VStaffProductionAllSearch[assigned_start_date]';
        var dateValue = formData.get(dateInputName);

        if (dateValue) {
            var parts = dateValue.split('/');
            var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
            formData.set(dateInputName, formattedDate);
            settings.data = formData.toString();
        }
    });
</script>