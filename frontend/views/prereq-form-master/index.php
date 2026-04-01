<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\RefGeneralStatus;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\preReqForm\PrereqFormMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if ($moduleIndex === 'personal') {
    $pageName = 'Personal';
} else if ($moduleIndex === 'superior') {
    $pageName = 'Superior';
} else {
    $pageName = 'Super User';
}

$this->title = 'Pre-Requisition Form';
$this->params['breadcrumbs'][] = ['label' => 'Pre-Requisition Form - ' . $pageName];
?>
<div class="prereq-form-master-index">

    <?php if ($approvalStatus === 'pending'): ?>
        <?= $this->render('_prereqformNavBar', ['module' => $moduleIndex, 'pageKey' => '1']) ?>
    <?php else: ?>
        <?= $this->render('_prereqformNavBar', ['module' => $moduleIndex, 'pageKey' => '2']) ?>
    <?php endif; ?>
    <p>
        <?php if ($moduleIndex === 'personal'): ?>
            <?= Html::a('Create', ['create', 'moduleIndex' => $moduleIndex], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 

        <?php
        if ($moduleIndex === 'personal') {
            $actionName = 'user-manual-personal';
        } else if ($moduleIndex === 'superior') {
            $actionName = 'user-manual-superior';
        } else {
            $actionName = 'user-manual-superuser';
        }
        ?>
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                [$actionName],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            [
                'attribute' => 'prf_no',
                'label' => 'PRF Code',
                'format' => 'raw',
                'value' => function ($model) {
//                    return $model->prf_no . ($model->status == RefGeneralStatus::STATUS_Approved ? Html::a(
//                            "<i class='far fa-file-alt fa-lg float-right m-1'></i>",
//                            ["/office/prereq-form-master/get-file", 'filename' => urlencode($model->filename), 'id' => $model->id],
//                            [
//                                'title' => "Generate PDF",
//                                'target' => "_blank",
//                                'data-pjax' => "0",
//                            ]
//                    ) : "");
                    return $model->prf_no . ($model->status == RefGeneralStatus::STATUS_Approved ? ' ' . Html::a(
                            "<i class='far fa-file-alt fa-lg float-right m-1'></i>",
                            "#",
                            [
                                'title' => "Generate PDF",
                                'value' => "/office/prereq-form-master/get-file?filename=" . urlencode($model->filename) . '&id=' . $model->id,
                                'class' => "docModal"
                            ]
                    ) : "");
                }
            ],
            [
                'attribute' => 'date_of_material_required',
                'format' => 'raw',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->date_of_material_required);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_of_material_required',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
//                    'clientOptions' => [
//                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
//                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'date_of_material_required'), // Hidden input for sending formatted date
//                    ],
                ]),
            ],
//            [
//                'attribute' => 'total_amount',
//                'label' => 'Total Amount (RM)',
//                'format' => 'raw',
//                'contentOptions' => ['class' => 'text-right'],
//                'value' => function ($model) {
//                    return \common\models\myTools\MyFormatter::asDecimal2($model->total_amount);
//                }
//            ],
            [
                'attribute' => 'superior_id',
                'label' => 'Superior Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->superior->fullname;
                }
            ],
            //'filename',
//            'status',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => false,
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    $status_desc = RefGeneralStatus::findOne($model->status);
                    return $status_desc->status_name;
                }
            ],
            //'is_deleted',
            //'created_by',
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    $responder = common\models\User::findOne($model->created_by);
                    if ($responder) {
                        return "By " . ($responder->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
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
                ]),
            ],
            //'updated_by',
            //'updated_at',
           [
    'attribute' => 'claim_flag',
    'format' => 'raw',
    'contentOptions' => ['class' => 'col-sm-1', 'style' => 'text-align: center;'],
    'filter' => [
        '0' => "No",
        '1' => "Yes"
    ],
    'value' => function ($model) {
        if ($model->claim_flag == 1) {
            $claimMaster = frontend\models\office\claim\ClaimMaster::findOne(['ref_code' => $model->prf_no]);
            if ($claimMaster) {
                $link = Html::a(
                    '<i class="fas fa-external-link-alt"></i>', 
                    ['/office/claim/personal-view-claim', 'id' => $claimMaster->id],
                    ['class' => 'ml-2', 'target' => '_blank']
                );
                
                return '<div class="d-flex justify-content-center align-items-center">' . 
                       '<span>Yes</span>' . 
                       $link . 
                       '</div>';
            }
        }
        
        return '<div class="text-center">' . ($model->claim_flag == 1 ? "Yes" : "No") . '</div>';
    }
],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($moduleIndex) {
                    if ($moduleIndex === 'personal' || $moduleIndex === 'superuser') {
                        return Html::a(
                                        'View <i class="far fa-eye"></i>',
                                        ['view', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                                        ['class' => 'btn btn-sm btn-success mx-1']
                                );
                    } else {
                        return Html::a(
                                        'View <i class="far fa-eye"></i>',
                                        ['save-superior-update', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                                        ['class' => 'btn btn-sm btn-success mx-1']
                                );
                    }
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
    <?=
    $this->render('/_docModal')
    ?>  
</div>
