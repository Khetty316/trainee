<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\cmms\InventorySupplierCmmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventory Control - CMMS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-supplier-cmms-index">

    <?= $this->render('__inventoryCmmsNavBar', ['module' => 'superior', 'pageKey' => '4']) ?>

    <p>
        <?=
        Html::a("Add New Model", "javascript:void(0)", [
            'title' => "Add Model",
            "value" => yii\helpers\Url::to(['add-new-model']),
            "class" => "modalButton btn btn-success ml-1",
            'data-modaltitle' => 'Add Model',
        ]);
        ?>
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
                'attribute' => 'code',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->code;
                }
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->description;
                }
            ],
            [
                'attribute' => 'unit_type',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->unit_type;
                }
            ],
            [
                'attribute' => 'active_sts',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return $model->active_sts == 0 ? 'No' : 'Yes';
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'active_sts',
                        [
                            '' => 'All',
                            '0' => 'No',
                            '1' => 'Yes'
                        ],
                        ['class' => 'form-control text-center']
                )
            ],
            [
                'attribute' => 'created_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->createdBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    ;
                }
            ],
            [
    'attribute' => 'updated_by',
    'contentOptions' => ['class' => 'col-sm-1'],
    'value' => function ($model) {
        return $model->updatedBy 
            ? $model->updatedBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at)
            : null;
    }
],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return Html::a('View <i class="fa fa-eye"></i>', "javascript:void(0)", [
                        'title' => "View Model",
                        'value' => yii\helpers\Url::to(['view-model', 'id' => $model->id]),
                        'class' => 'modalButton btn btn-sm btn-success text-center',
                        'data-modaltitle' => 'View Model',
                    ]);
                }
            ],
        //'updated_at',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
