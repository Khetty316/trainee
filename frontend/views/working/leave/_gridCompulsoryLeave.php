<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use frontend\models\office\leave\LeaveMaster;
?>

<div class="leave-master-compulsory">

    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'requestor',
                'filter' => \common\models\User::getActiveDropDownList(),
                'value' => function ($model) {
                    return ucwords(strtolower($model->requestor0->fullname));
                }
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Submitted At',
                'contentOptions' => ['class' => 'col-sm-1'],
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
                    ],
                ]),
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
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
                        'altFormat' => 'yy-mm-dd',
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'start_date'),
                    ],
                ]),
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
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
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'days',
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'requestor_remark',
                'value' => function ($model) {
                    return Html::encode($model->requestor_remark);
                }
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => \frontend\models\office\leave\RefLeaveStatus::getDropDownListFiltered(),
                'value' => function ($model) {
                    if ($model->status == \frontend\models\office\leave\LeaveMaster::STATUS_Approved) {
                        return '<div class="text-success">' . $model->status0->remark . '</div>';
                    } else if ($model->status == \frontend\models\office\leave\LeaveMaster::STATUS_Rejected) {
                        return '<div class="text-danger">' . $model->status0->remark . '</div>';
                    } else {
                        return '<div class="text-warning">' . $model->status0->remark . '</div>';
                    }
                }
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'approval_by',
                'value' => function ($model) {
                    return ucwords(strtolower($model->approvalBy->fullname ?? null));
                }
            ],
            [
                'attribute' => 'approved_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->approved_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'approved_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'approved_at'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'approval_remark',
                'value' => function ($model) {
                    return Html::encode($model->approval_remark);
                }
            ],
            [
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'attribute' => 'Action',
                'format' => 'html',
                'value' => function ($model) {
                    $html = '';
                    $html .= '<td>' . Html::a('View', ['/working/leavemgmt/view-compulsory-leave', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary mr-1', 'title' => 'Click to View']) . '</td>';

                    if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior, AuthItem::ROLE_SystemAdmin]) && !MyCommonFunction::checkRoles([AuthItem::ROLE_Director]) && $model->status == LeaveMaster::STATUS_GetDirectorApproval) {
                        $html .= '<td>' . Html::a('Update', ['/working/leavemgmt/update-compulsory-leave', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mr-1', 'title' => 'Click to Update']) . '</td>';
                    } else if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director]) && $model->status == LeaveMaster::STATUS_GetDirectorApproval) {
                        $html .= '<td>' . Html::a('Approve', ['/working/leavemgmt/dir-compulsory-leave-approval', 'id' => $model->id], ['class' => 'btn btn-sm btn-success']) . '</td>';
                    }

                    return $html;
                }
            ],
        ],
    ]));
    ?>
</div>