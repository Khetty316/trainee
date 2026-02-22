<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\RefGeneralStatus;

$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - New Item'];
?>
<div class="prereq-form-master-index">
    <?= $this->render('_purchasingNavBar', ['module' => "newItem", 'pageKey' => '3']) ?>
    <p>        
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
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
            [
                'attribute' => 'inventory_supplier_id',
                'label' => 'Supplier Name',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->inventorySupplier->name;
                }
            ],
            [
                'attribute' => 'source_id',
                'label' => "PRF Code",
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->source_id;
                }
            ],
            [
                'attribute' => 'quotation_no',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->quotation_no;
                }
            ],
            [
                'attribute' => 'quotation_date',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->quotation_date;
                }
            ],
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
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    $html = Html::a(
                            'View <i class="far fa-eye"></i>',
                            ['view-pre-requisition', 'id' => $model->id, 'moduleIndex' => 'inventory'],
                            ['class' => 'btn btn-sm btn-primary mx-1']
                    );

                    $html .= Html::a(
                            'Issue PO <i class="fas fa-arrow-right"></i>',
                            ['issue-po', 'id' => $model->id, 'type' => "new"],
                            ['class' => 'btn btn-sm btn-success mx-1']
                    );
                    return $html;
                }
            ],
        ],
    ]);
    ?>
    <?=
    $this->render('/_docModal')
    ?>  
</div>
