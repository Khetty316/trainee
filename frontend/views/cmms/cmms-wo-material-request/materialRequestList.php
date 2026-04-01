<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\cmms\CmmsWoMaterialRequestMaster;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsWoMaterialRequestMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Maintenance - Material Request Master List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-wo-material-request-master-index">

    <!--<h1><?php //= Html::encode($this->title)   ?></h1>-->
    <?= $this->render('__cmmsMaterialRequestNavBar', ['module' => $moduleIndex, 'pageKey' => $key]) ?>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?moduleIndex=' . $moduleIndex . '&type=' . $type, ['class' => 'btn btn-primary']) ?> 
    </p>

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
//            'wo_type',
//            'wo_id',
            [
                'attribute' => 'wo_type',
                'format' => 'raw',
                'filter' => [
                    'fl' => 'Corrective Maintenance',
                    'pm' => 'Preventive Maintenance'
                ],
                'value' => function ($model) {
                    if ($model->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                        $woType = 'Corrective Maintenance';
                    } else if ($model->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_PM) {
                        $woType = 'Preventive Maintenance';
                    }
                    return $woType ?? '-';
                },
            ],
            [
                'attribute' => 'wo_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->wo_id ?? '-';
                },
            ],
            [
                'attribute' => 'finalized_status',
                'format' => 'raw',
                'value' => 'finalizedStatusLabel', // calls getFinalizedStatusLabel()
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'finalized_status',
                        CmmsWoMaterialRequestMaster::getFinalizedStatusList(),
                        ['class' => 'form-control', 'prompt' => 'All']
                ),
            ],
            [
                'attribute' => 'fully_dispatched_status',
                'format' => 'raw',
                'value' => 'dispatchedStatusLabel', // calls getDispatchedStatusLabel()
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'fully_dispatched_status',
                        CmmsWoMaterialRequestMaster::getDispatchedStatusList(),
                        ['class' => 'form-control', 'prompt' => 'All']
                ),
            ],
            [
                'attribute' => 'created_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->createdBy->fullname) . " @ " . \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    $name = $model->updatedBy->fullname ?? null;
                    $date = $model->updated_at ? \common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : '-';
                    return $name === null ? '-' : $name . ' @ ' . $date;
                }
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($moduleIndex) {
                    $action = 'view-selected-material-pm';
                    if ($model->wo_type === CmmsWoMaterialRequestMaster::WO_TYPE_CM) {
                        $action = 'view-selected-material-cm';
                    }
                    $html = Html::a(
                            'View',
                            [$action, 'id' => $model->wo_id, 'moduleIndex' => $moduleIndex],
                            [
                                'class' => 'btn btn-sm btn-primary mx-1',
                                'title' => 'Manage PO Items'
                            ]
                    );

//                    if ($module === "execReceiving" || $module === 'assistReceiving') {
//                        if (($model->status === \frontend\models\RefInventoryStatus::STATUS_AwaitingDelivery || $model->status === \frontend\models\RefInventoryStatus::STATUS_PartiallyReceived)) {
//                            $html .= Html::a(
//                                    'Receive Items <i class="fas fa-clipboard-check"></i>',
//                                    ['update-receive-items', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
//                                    ['class' => 'btn btn-sm btn-success mx-1',
//                                        'title' => 'Receive Items'],
//                            );
//                        }
//                    }
                    return $html;
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
