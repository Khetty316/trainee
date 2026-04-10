<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventorySupplierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'execStock') {
    $pageName = 'Inventory Master - Executive';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Inventory Master - Assistant';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Inventory Master - Project Coordinator';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Inventory Master - Head of Maintenance';
}

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $pageName;
?>
<div class="inventory-brand-index">

    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => '3']) ?>

    <p>
        <?php if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'projcoorStock' || $moduleIndex === 'maintenanceHeadStock') { ?>
            <?=
            Html::a("Add New Brand <i class='fas fa-plus'></i>", "javascript:void(0)", [
                'title' => "Add Brand",
                "value" => yii\helpers\Url::to(['add-new-brand', 'type' => $moduleIndex]),
                "class" => "modalButtonMedium btn btn-success ml-1",
                'data-modaltitle' => 'Add Brand',
            ]);
            ?>
            <?= Html::a('Upload Template <i class="fas fa-upload"></i>', ['add-by-template-brand', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
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
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) use ($moduleIndex) {
                    if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'projcoorStock' || $moduleIndex === 'maintenanceHeadStock') {
                        return Html::a($model->code, "javascript:void(0)", [
                                    'title' => "View Brand",
                                    'value' => yii\helpers\Url::to(['view-brand', 'id' => $model->id, 'type' => $moduleIndex]),
                                    'class' => 'modalButtonMedium',
                                    'data-modaltitle' => 'View Brand',
                        ]);
                    } else {
                        return $model->code;
                    }
                }
            ],
            'name',
            [
                'attribute' => 'active_sts',
                'contentOptions' => ['class' => 'text-center col-sm-1'],
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
//                    return Html::a('View <i class="fa fa-eye"></i>', "javascript:void(0)", [
//                        'title' => "View Brand",
//                        'value' => yii\helpers\Url::to(['view-brand', 'id' => $model->id]),
//                        'class' => 'modalButtonMedium btn btn-sm btn-success text-center',
//                        'data-modaltitle' => 'View Brand',
//                    ]);
//                }
//            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>

</div>
