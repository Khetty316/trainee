<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\leave\VMasterLeave;
use frontend\models\office\leave\RefLeaveType;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$this->title = 'All Leave';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>
<p>
<?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
</p>
<?=
GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'class' => 'yii\grid\ActionColumn',
            'template' => '{process}',
            'header' => 'Approve/<br>Reject',
            'buttons' => [
                'process' => function ($url, $model, $key) {
                    return Html::a(
                            '<i class="fas fa-check fa-lg text-success"></i>',
                            "#",
                            [
                                'title' => 'Process',
                                'data-toggle' => 'modal',
                                'data-target' => '#workingModel',
                                'data-id' => $model->id,
                                'data-requestor' => $model->requestor,
                                'data-leavetype' => $model->leave_type_name,
                                'data-from' => MyFormatter::asDate_Read($model->start_date) . ' (' . MyFormatter::asDay_Read($model->start_date) . ') ' . $model->start_sec_name,
                                'data-to' => MyFormatter::asDate_Read($model->end_date) . ' (' . MyFormatter::asDay_Read($model->end_date) . ') ' . $model->end_sec_name,
                            ]
                    );
                },
            ],
            'visible' => $Tick ?? false,
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'leave_code',
            'value' => function ($model) {
                return $model->leave_code;
            }
        ],
//        [
//            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
//            'class' => 'yii\grid\ActionColumn',
//            'template' => '{recall}',
//            'header' => 'Recall',
//            'buttons' => [
//                'recall' => function ($url, $model, $key) {
//                    // Check if this leave master has any breakdown leave is confirmed
////                    $confirmed = VMasterLeave::find()->where(["id" => $model->id])->andWhere("leave_status != 4")->count();
//
//                    if ($model->leave_status == 4) {
//                        return Html::a(
//                                        '<i class="fas fa-times fa-lg text-danger"></i>',
//                                        ["recall-leave", 'id' => $model->id],
//                                        [
//                                            'title' => 'Recall by HR',
//                                            'data-method' => 'post',
//                                            'data-confirm' => 'Are you sure to recall?'
//                                        ]
//                                );
//                    } else {
//                        return '<i class="fas fa-times text-secondary fa-lg "></i>';
//                    }
//                }
//            ],
//            'visible' => $HrCancelLeave ?? false,
//        ],
        [
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'class' => 'yii\grid\ActionColumn',
            'template' => '{recall}',
            'header' => 'Recall',
            'buttons' => [
                'recall' => function ($url, $model, $key) {
                    if ($model->leave_status == 4) {
                        return Html::a(
                                        '<i class="fas fa-times fa-lg text-danger"></i>',
                                        ["recall-leave", 'id' => $model->id],
                                        [
                                            'title' => 'Recall by HR',
                                            'data-method' => 'post',
                                            'data-confirm' => 'Are you sure to recall?'
                                        ]
                                );
                    } else {
                        return '<i class="fas fa-times text-secondary fa-lg "></i>';
                    }
                }
            ],
            'visible' => ((isset($HrCancelLeave) && $HrCancelLeave) && MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])),
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'requestor',
            'value' => function ($model) {
                return ucwords(strtolower($model->requestor));
            }
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'width:5%;'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'leave_type_name',
            'label' => 'Leave Type',
            'filter' => frontend\models\office\leave\RefLeaveType::getDropDownList(),
            'format' => 'raw',
            'value' => function ($data) {
                $text = $data->emergency_leave == 1 ? $data->leave_type_name . '.<br/><span class ="text-info">Applied for emergency leave </span>' : $data->leave_type_name;
                return $text;
            },
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'reason',
            'label' => 'Reason',
            'format' => 'raw',
            'value' => function ($data) {
                $text = $data->reason . '.<br/> ' . '<span class = "text-info">Applied at : </span>' . MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                $file = $data->support_doc == '' ? '' : Html::a("<i class='far fa-file-alt fa-lg'></i>", "#",
                                [
                                    'title' => "Click to view me",
                                    "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($data->support_doc)),
                                    "class" => "modalButtonPdf"]);
                return $text . "<br>" . $file;
            },
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'width:7%;'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'start_date',
            'label' => 'From',
            'format' => 'raw',
            'value' => function ($data) {
                return MyFormatter::asDate_Read($data->start_date) . ' (' . MyFormatter::asDay_Read($data->start_date) . ') ';
            },
            'filter' => yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'start_date',
                'language' => 'en',
                'dateFormat' => 'php:d M Y',
                'options' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'onchange' => '$("#w0").yiiGridView("applyFilter")',
                ],
                'clientOptions' => [
                    'altFormat' => 'yy-mm-dd', // Format for sending to the server
                    'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'start_date'), // Hidden input for sending formatted date
                ],
            ]),
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'width:7%;'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'end_date',
            'label' => 'To',
            'format' => 'raw',
            'value' => function ($data) {
                return MyFormatter::asDate_Read($data->end_date) . ' (' . MyFormatter::asDay_Read($data->end_date) . ') ';
            },
            'filter' => yii\jui\DatePicker::widget([
                'model' => $searchModel,
                'attribute' => 'end_date',
                'language' => 'en',
                'dateFormat' => 'php:d M Y',
                'options' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'onchange' => '$("#w0").yiiGridView("applyFilter")',
                ],
                'clientOptions' => [
                    'altFormat' => 'yy-mm-dd', // Format for sending to the server
                    'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'end_date'), // Hidden input for sending formatted date
                ],
            ]),
        ],
//        [
//            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
//            'headerOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'width:12%;'],
//            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
//            'attribute' => 'monthYear',
//            'label' => 'Month/Year',
//            'format' => 'raw',
//            'value' => function ($model) {
//                return date("F", strtotime($model->start_date)) . ', ' . date("Y", strtotime($model->start_date));
//            }
//        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell', 'style' => 'width:3%;'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'total_days',
        ],
//        [
//            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
//            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
//            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
//            'attribute' => 'support_doc',
//            'label' => 'Attachment',
//            'format' => 'raw',
//            'filter' => false,
//            'value' => function ($data) {
//                return $data->support_doc == '' ? '' : Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
//                        [
//                            'title' => "Click to view me",
//                            "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($data->support_doc)),
//                            "class" => "modalButtonPdf m-2"]);
//            }
//        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'label' => 'Relief\'s Response',
            'format' => 'raw',
            'value' => function ($data) {
                if ($data->relief) {
                    if ($data->rep_response_by) {
                        $text = 'Relief: ' . $data->relief;
                        $text .= ($data->rep_response == 1 ? '<br/><span class="text-success">Accepted</span>' : '<br/><span class="text-danger">Rejected</span>') . '<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->rep_response_at);
                        $text .= "<br/>Remarks: <p class='text-wrap'>" . $data->rep_remarks . '</p>';
                        return $text;
                    } else {
                        return $data->relief . '<br> (Pending) ';
                    }
                } else {
                    return '-';
                }
            },
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'label' => 'Superior\'s Response',
            'format' => 'raw',
            'value' => function ($data) {
                if ($data->leave_type_code != RefLeaveType::codeAnnual && $data->leave_type_code != RefLeaveType::codeUnpaid) {
                    return ' - ';
                } else {
                    if ($data->superior_id) {
                        if ($data->sup_response_by) {
                            $text = 'Superior: ' . $data->superior;
                            $text .= ($data->sup_response ? '<br/><span class="text-success">Approved</span>' : '<br/><span class="text-danger">Rejected</span>') . '<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->sup_response_at);
                            $text .= "<br/>Remarks: <p class='text-wrap'>" . $data->sup_remarks . '</p>';
                            return $text;
                        } else if ($data->leave_status == 0 || $data->leave_status == 8 || $data->leave_status == LeaveMaster::STATUS_GetHrApproval) {
                            return ' - ';
                        } else {
                            return $data->superior . '<br> (Pending) ';
                        }
                    } else {
                        return '(No Superior)';
                    }
                }
            },
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'label' => 'HR Dept\'s Response',
            'format' => 'raw',
            'value' => function ($data) {
                if ($data->leave_type_code == RefLeaveType::codeAnnual || $data->leave_type_code == RefLeaveType::codeUnpaid) {
                    return ' - ';
                }
                if ($data->sup_response_by) {
                    return ' - ';
                }

                $text = '';

                if ($data->hr_response_by) {
                    $text .= ($data->hr_response ? '<span class="text-success">Approved by</span>' : '<span class="text-danger">Rejected by</span>') . ': ' . $data->hr_response_by . '<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->hr_response_at);
                    $text .= $data->hr_remarks ? "<br/>Remarks: <p class='text-wrap'>" . $data->hr_remarks . '</p><br/><br/>' : "<br/><br/>";

                    if (isset($data->hr_recall_by)) {
                        $text .= '<span class="text-danger">Recalled by</span>: ' . $data->hr_recall_by . '.<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->hr_recall_at);
                        $text .= $data->hr_recall_remarks ? "<br/>Remarks: <p class='text-wrap'>" . $data->hr_recall_remarks . '</p>' : '';
                    }

                    return $text;
                } else {
                    if ($data->leave_status == LeaveMaster::STATUS_ReliefRejected || $data->leave_status == LeaveMaster::STATUS_Rejected) {
                        return ' - ';
                    } else {
                        return '(Pending)';
                    }
                }
            },
            'visible' => $HrRes ?? true,
        ],
        [
            'contentOptions' => ['class' => 'd-none d-md-table-cell'],
            'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            'filterOptions' => ['class' => 'd-none d-md-table-cell'],
            'attribute' => 'leave_remark'
        ],
        [
            'contentOptions' => ['class' => 'd-md-none px-3'],
            'headerOptions' => ['class' => 'd-none'],
            'filterOptions' => ['class' => 'd-none'],
            'label' => 'Details',
            'format' => 'raw',
            'value' => function ($model) {
//                $returnStr = Html::a($model->todo_module_name, [$model->url, 'id' => $model->id]) . "<br/>";

                $returnStr = "Requestor: " . $model->requestor . "<br/>";
                $returnStr .= "Leave type: " . $model->leave_type_name . "<br/>";
                $returnStr .= 'From: ' . MyFormatter::asDate_Read($model->start_date) . $model->start_sec_name . ' (' . MyFormatter::asDay_Read($model->start_date) . ') <br/>'
                        . 'To: ' . MyFormatter::asDate_Read($model->end_date) . $model->end_sec_name . ' (' . MyFormatter::asDay_Read($model->end_date) . ') ' . "<br/>";
                $returnStr .= "For " . $model->total_days . " days. <br/>";
                $returnStr .= $model->reason . ".<br/> " . '<span class = "text-info">Applied at : </span>' . MyFormatter::asDateTime_ReaddmYHi($model->created_at) . "<br/>";
                $returnStr .= "Leave Status: " . $model->leave_remark . "<br/>";
//                $returnStr .= $model->support_doc == '' ? '' : Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
//                                [
//                                    'title' => "Click to view me",
//                                    "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($model->support_doc)),
//                                    "class" => "modalButtonPdf m-2",
//                                ]
//                );
                return $returnStr;
            }
        ]
//        [
//            'attribute' => '',
//            'format' => 'raw',
//            'value' => function ($model) {
//                return $model->leave_status_name;
//            }
//        ],
//        [
//            'label' => 'Directors\' Response',
//            'format' => 'raw',
//            'value' => function ($data) {
//                if ($data->dir_response_by) {
//                    $text = ($data->dir_response ? 'Approved by :' : '<span class="text-danger">Rejected</span> by :') . $data->dir_response_by . '.<br/>@ ' . MyFormatter::asDateTime_ReaddmYHi($data->dir_response_at);
//                    $text .= $data->dir_remarks == "" ? "" : "<br/>Remarks: <p class='text-wrap'>" . $data['dir_remarks'] . '</p>';
//                    return $text;
//                } else {
//                    return ' (Pending) ';
//                }
//            },
//            'visible' => $Direct ?? true,
//        ],
    ],
]));
?>

<script>
    $(document).ready(function () {
        // Function to check if the current viewport is in mobile view
        function isMobileView() {
            return $(window).width() < 768; // Adjust the breakpoint as needed
        }

        // Hide the table row with id w0-filters when in mobile view
        function toggleFiltersRow() {
            if (isMobileView()) {
                $('#w0-filters').hide();
            } else {
                $('#w0-filters').show();
            }
        }

        // Toggle the visibility of the filters row on page load
        toggleFiltersRow();

        // Toggle the visibility of the filters row on window resize
        $(window).resize(function () {
            toggleFiltersRow();
        });
    });
</script>

