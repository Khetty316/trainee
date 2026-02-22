<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use frontend\models\office\leave\LeaveMaster;
?>

<div class="leave-master-compulsory">
    <div class="mb-2">
        <?= $this->render('__hrLeaveNavBar', ['module' => 'hr', 'pageKey' => '9']) ?>

        <?=
        Html::a('Schedule Compulsory Leave <i class="fas fa-plus"></i>',
                ['/working/leavemgmt/apply-compulsory-leave'],
                ['class' => 'btn btn-success']
        );
        ?>

        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>

    </div>

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
                    if (MyCommonFunction::checkRoles([AuthItem::ROLE_Director, AuthItem::ROLE_SystemAdmin]) && $model->status == LeaveMaster::STATUS_GetDirectorApproval) {
                        return '<td>' . Html::a('Update', ['/working/leavemgmt/update-compulsory-leave', 'id' => $model->id], ['class' => 'btn btn-sm btn-success', 'title' => 'Click to Update']) . " " .
                                Html::a('Approve', ['/working/leavemgmt/dir-compulsory-leave-approval', 'id' => $model->id], ['class' => 'btn btn-sm btn-success']) . '</td>';
                    } else if ($model->status == LeaveMaster::STATUS_GetDirectorApproval) {
                        return '<td>' . Html::a('Update', ['/working/leavemgmt/update-compulsory-leave', 'id' => $model->id], ['class' => 'btn btn-sm btn-success', 'title' => 'Click to Update']) . '</td>';
                    } else {
                        return '<td>' . Html::a('View', ['/working/leavemgmt/view-compulsory-leave', 'id' => $model->id], ['class' => 'btn btn-sm btn-primary', 'title' => 'Click to View']) . '</td>';
                    }
                }
            ],
        ],
    ]));
    ?>
</div>