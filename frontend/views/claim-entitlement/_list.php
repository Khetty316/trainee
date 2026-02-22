<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use frontend\models\office\claim\ClaimEntitleWorklist;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\claim\ClaimEntitlementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Claim Entitlements';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="claim-entitlement-index">
    <p>
        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior]) && $hr) { ?>
            <?= Html::a('Create Claim Entitlement', ['create'], ['class' => 'btn btn-success mx-1']) ?>
        <?php } ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior]) && $hr) { ?>
            <?=
            Html::a(
                    'User Manual <i class="fas fa-book"></i>',
                    ['user-manual'],
                    ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
            )
            ?>        
        <?php } else { ?>
            <?=
            Html::a(
                    'User Manual <i class="fas fa-book"></i>',
                    ['user-manual'],
                    ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
            )
            ?>
        <?php } ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

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
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->user->fullname;
                }
            ],
            [
                'attribute' => 'year',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->year;
                }
            ],
            [
                'attribute' => 'created_at',
//                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return "By " . ($model->createdBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
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
//                'contentOptions' => ['class' => 'col-sm-1 d-none d-md-table-cell'],
//                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
//                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'label' => 'Superior\'s Response',
                'format' => 'raw',
                'value' => function ($model) {
                    $entitleStatuses = ClaimEntitleWorklist::find()
                            ->where(['claim_entitle_id' => $model->id])
                            ->orderBy(['created_at' => SORT_DESC])
                            ->all();
                    $text = '';
                    if ($entitleStatuses) {

                        if ($model->status == frontend\models\RefGeneralStatus::STATUS_GetSuperiorApproval) {
                            $text .= 'Superior: ' . Html::encode($model->superior->fullname) . '<br/>';
                            $text .= "<span class='text-warning'>Pending</span><br/>";
                            $text .= '@ ' . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) . '<br/>';
                            $text .= '<hr class="my-2">';
                        }

                        foreach ($entitleStatuses as $index => $entitleStatus) {
                            if ($index > 0) {
                                $text .= '<hr class="my-3">'; // Separator between records
                            }

                            // Add superior name
                            if ($entitleStatus->responsedBy) {
                                $text .= 'Superior: ' . Html::encode($entitleStatus->responsedBy->fullname) . '<br/>';
                            }

                            // Add status
                            if ($entitleStatus->claimEntitleStatus) {
                                $isApproved = ($entitleStatus->claimEntitleStatus->id == frontend\models\RefGeneralStatus::STATUS_Approved);
                                $statusClass = $isApproved ? 'text-success' : 'text-danger';
                                $statusText = $isApproved ? 'Approved' : 'Rejected';
                                $text .= "<span class='{$statusClass}'>{$statusText}</span><br/>";
                            }

                            // Add timestamp
                            if ($entitleStatus->created_at) {
                                $text .= '@ ' . MyFormatter::asDateTime_ReaddmYHi($entitleStatus->created_at) . '<br/>';
                            }

                            // Add remarks
                            if (!empty($entitleStatus->remark)) {
                                $text .= "Remarks: <p class='text-wrap'>" . Html::encode($entitleStatus->remark) . '</p>';
                            }
                        }
                    } else {
                        $text .= 'Superior: ' . Html::encode($model->superior->fullname) . '<br/>';
                        $text .= "<span class='text-warning'>Pending</span><br/>";
                    }
                    return $text;
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'filter' => false,
//                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->status0->status_name;
                }
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'filter' => \frontend\models\office\employeeHandbook\EmployeeHandbookMaster::IS_ACTIVE,
                'value' => function ($model) {
                    return \frontend\models\office\employeeHandbook\EmployeeHandbookMaster::IS_ACTIVE_HTML[$model->is_active] ?? null;
                }
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($hr) {
                    if (MyCommonFunction::checkRoles([AuthItem::ROLE_CE_HR]) && $hr) {
                        return Html::a('Edit <i class="far fa-edit"></i>', ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                    } else if (!$hr) {
                        return Html::a('View <i class="far fa-eye"></i>', ['superior-view-detail', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                    }
                }
            ],
        //'created_by',
        //'created_at',
        //'updated_by',
        //'updated_at',
        //            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
