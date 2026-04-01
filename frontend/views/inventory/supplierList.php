<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'execStock') {
    $pageName = 'Stock - Executive';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Stock - Assistant';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Stock - Project Coordinator';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Stock - Head of Maintenance';
}

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $pageName;
?>
<div class="inventory-supplier-index">

    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => '2']) ?>

    <p>
        <?php if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'maintenanceHeadStock') { ?>
            <?= Html::a('Add New Supplier', ['add-new-supplier', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Upload Template', ['add-by-template-supplier', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?type=' . $moduleIndex, ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-inventory'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
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
                'value' => function ($model) use ($moduleIndex) {
                    if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'maintenanceHeadStock') {
                        return Html::a($model->code, ['view-supplier', 'id' => $model->id, 'type' => $moduleIndex]);
                    } else {
                        return $model->code;
                    }
                }
            ],
            'name',
//            'address1',
//            'address2',
//            'address3',
//            'address4',
            'contact_name',
            'contact_number',
            'contact_email:email',
            'contact_fax',
            'agent_terms',
            [
                'attribute' => 'active_sts',
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    return $model->active_sts == 2 ? 'Yes' : 'No';
                },
                'filter' => Html::activeDropDownList(
                        $searchModel,
                        'active_sts',
                        [
                            '' => 'All',
                            '1' => 'No',
                            '2' => 'Yes'
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
                    return $model->updatedBy ? $model->updatedBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : null;
                }
            ],
//            [
//                'format' => 'raw',
//                'contentOptions' => ['class' => 'text-center col-sm-1'],
//                'value' => function ($model) {
//                    return Html::a('View <i class="fa fa-eye"></i>', ['view-supplier', 'id' => $model->id], [
//                        'class' => 'btn btn-sm btn-success text-center'
//                    ]);
//                }
//            ]
        ],
    ]);
    ?>


</div>
