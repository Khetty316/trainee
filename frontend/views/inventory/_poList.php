<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventoryPurchaseOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
if ($moduleIndex === 'execPendingPurchasing') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
    $key = 5;
} else if ($moduleIndex === 'execAllPurchasing') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
    $key = 6;
}else if ($moduleIndex === 'execPendingReceiving') {
    $pageName = 'Receiving - Executive';
    $module = 'execReceiving';
    $key = 1;
}else if ($moduleIndex === 'execAllReceiving') {
    $pageName = 'Receiving - Executive';
    $module = 'execReceiving';
    $key = 2;
}

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
?>
<div class="po-create">
    <?= $this->render('_purchasingNavBar', ['module' => $module, 'pageKey' => $key]) ?>
    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?type=' . $moduleIndex, ['class' => 'btn btn-primary']) ?> 

    <div class="table-responsive">

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
//            'po_no',
                [
                    'attribute' => 'po_no',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $file = '';
                        if ($model->status !== frontend\models\RefInventoryStatus::STATUS_PoCreated) {
                            $file = Html::a(
                                    "<i class='far fa-file-alt fa-lg m-1'></i>",
                                    "#",
                                    [
                                        'title' => "Generate PDF",
                                        'value' => "/inventory/inventory/get-po?filename=" . urlencode($model->id) . '&id=' . $model->id,
                                        'class' => "docModal"
                                    ]
                            );
                        }

                        return $model->po_no . $file;
                    }
                ],
                [
                    'attribute' => 'po_date',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return MyFormatter::asDate_Read($model->po_date);
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'po_date',
                        'language' => 'en',
                        'dateFormat' => 'php:d/m/Y',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'onchange' => '$("#w0").yiiGridView("applyFilter")',
                        ],
                    ]),
                ],
//            'inventory_pr_id',
                [
                    'attribute' => 'company_group',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'company_group',
                            ['' => 'All'] + frontend\models\common\RefCompanyGroupList::getDropDownList(),
                            ['class' => 'form-control']
                    ),
                    'value' => function ($model) {
                        $companyGroup = frontend\models\common\RefCompanyGroupList::findOne($model->company_group);
                        return ($companyGroup->company_name ?? null);
                    }
                ],
                [
                    'attribute' => 'currency_id',
                    'label' => 'Currency',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'currency_id',
                            ['' => 'All'] + \frontend\models\common\RefCurrencies::getActiveDropdownlist_by_id(),
                            ['class' => 'form-control']
                    ),
                    'value' => function ($model) {
                        return ($model->currency->currency_code ?? null);
                    }
                ],
                [
                    'attribute' => 'total_qty',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'total_amount',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'total_discount',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'net_amount',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'tax_amount',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                ],
                [
                    'attribute' => 'gross_amount',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => ['decimal', 2],
                ],
                'comment',
                [
                    'attribute' => 'status',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'filter' => Html::activeDropDownList(
                            $searchModel,
                            'status',
                            ['' => 'All'] + frontend\models\RefInventoryStatus::getDropDownListPo(),
                            ['class' => 'form-control']
                    ),
                    'value' => function ($model) {
                        return ($model->status0->name ?? null);
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'label' => 'Created By',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => function ($model) {
                        return ($model->createdBy->fullname ?? null);
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return ($model->created_at === '0000-00-00 00:00:00' ? null : MyFormatter::asDateTime_ReaddmYHi($model->created_at));
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
                [
                    'attribute' => 'updated_by',
                    'label' => 'Updated By',
                    'headerOptions' => ['style' => 'width: 180px;'],
                    'contentOptions' => ['class' => 'grid-wrap'],
                    'value' => function ($model) {
                        return ($model->updatedBy->fullname ?? null);
                    }
                ],
                [
                    'attribute' => 'updated_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return ($model->updated_at === '0000-00-00 00:00:00' ? null : MyFormatter::asDateTime_ReaddmYHi($model->updated_at));
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
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
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-sm-1 text-center'],
                    'value' => function ($model) use ($moduleIndex, $module) {
                        $html = Html::a(
                                'Manage PO <i class="fas fa-edit"></i>',
                                ['manage-po', 'id' => $model->id, 'moduleIndex' => $moduleIndex],
                                [
                                    'class' => 'btn btn-sm btn-primary mx-1',
                                    'title' => 'Manage PO Items'
                                ]
                        );

                    if ($module === "execReceiving") {
                        if (($model->status === \frontend\models\RefInventoryStatus::STATUS_AwaitingDelivery || $model->status === \frontend\models\RefInventoryStatus::STATUS_PartiallyReceived)) {
                            $html .= Html::a(
                                    'Receive Items <i class="fas fa-clipboard-check"></i>',
                                    ['update-receive-items', 'id' => $model->id],
                                    ['class' => 'btn btn-sm btn-success mx-1',
                                        'title' => 'Receive Items'],
                            );
                        }
                    }
                        return $html;
                    }
                ],
//            ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </div>
    <?=
    $this->render('/_docModal')
    ?> 

</div>
