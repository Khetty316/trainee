<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use \common\models\myTools\MyFormatter;
use \frontend\models\test\TestMaster;
use frontend\models\test\TestMain;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\test\TestTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test Templates';
$this->params['breadcrumbs'][] = $this->title;
$array = frontend\models\test\RefTestFormList::getDropDownList();
$mergeArray = array_merge($array, [TestMaster::TEMPLATE_ITP => TestMain::TEST_ITP_TITLE, TestMaster::TEMPLATE_FAT => TestMain::TEST_FAT_TITLE]);
?>
<div class="test-template-index">

    <p>
        <?= Html::a('Create Test Template <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "
{summary}
{pager}
<div class='table-scroll'>
    {items}
</div>
{pager}
",
        'tableOptions' => [
            'class' => 'table table-hover table-striped table-bordered table-sm',
            'style' => 'min-width:1500px;',
        ],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',
                'contentOptions' => ['style' => 'width: 2%; text-align:center;'],
                'headerOptions' => ['style' => 'width: 2%; text-align:center;'],
            ],
            [
                'attribute' => 'doc_ref',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->doc_ref;
                }
            ],
            [
                'attribute' => 'rev_no',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->rev_no;
                }
            ],
            [
                'attribute' => 'formname',
                'contentOptions' => ['class' => 'col-sm-1'],
                'filter' => $mergeArray,
                'value' => function ($model) use ($mergeArray) {
                    return $model->formcode ? $mergeArray[$model->formcode] : '';
                }
            ],
            [
                'attribute' => 'active_sts',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'filter' => ['0' => 'No', '1' => 'Yes'],
                'value' => function ($model) {
                    return $model->active_sts ? "Yes" : "<span class='text-danger'>No</span>";
                },
            ],
            [
                'attribute' => 'created_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return(User::findOne($model->created_by) !== null) ? User::findOne($model->created_by)->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at) : " - ";
                }
            ],
            [
                'attribute' => 'updated_by',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return(User::findOne($model->updated_by) !== null) ? User::findOne($model->updated_by)->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : " - ";
                },
            ],
            [
                'label' => 'Action',
                'format' => 'raw',
                'headerOptions' => [
                    'class' => 'sticky-action text-center',
                    'style' => 'width:50px; min-width:50px; max-width:50px;',
                ],
                'filterOptions' => [
                    'class' => 'sticky-action',
                ],
                'contentOptions' => [
                    'class' => 'sticky-action text-center',
                    'style' => 'width:50px; min-width:50px; max-width:50px; white-space:nowrap;',
                ],
                'value' => function ($model) {
                    return
                    Html::a('View <i class="fas fa-eye"></i>', ['view', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-info',
                        'title' => 'Click to View'
                    ]) . ' ' .
                    Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-primary',
                        'title' => 'Click to Update'
                    ]) . ' ' .
                    Html::a('Delete <i class="fas fa-trash"></i>', ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-sm btn-danger',
                        'title' => 'Click to Delete'
                    ]);
                },
            ],
        ],
    ]);
    ?>
</div>

<style>
    .table-scroll {
        width: 100%;
        max-height: calc(100vh - 320px);
        overflow-x: auto;
        overflow-y: auto;
        position: relative;
        margin-bottom: 20px;
    }

    .table-scroll table {
        width: max-content;
        min-width: 1500px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-scroll th,
    .table-scroll td {
        white-space: nowrap;
        vertical-align: middle;
        padding: 4px !important;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 5;
        border-bottom: 1px solid #dee2e6;
    }

    .table-scroll .filters input,
    .table-scroll .filters select {
        height: 30px !important;
        padding: 4px 6px;
    }

    /* Sticky Action column */
    .table-scroll th.sticky-action,
    .table-scroll td.sticky-action {
        position: sticky;
        right: 0;
        background: #fff;
        white-space: nowrap;
    }

    .table-scroll thead th.sticky-action {
        z-index: 10;
    }

    .table-scroll tr.filters th.sticky-action {
        position: sticky;
        right: 0;
        background: #fff;
        z-index: 9;
    }

    .table-scroll tbody td.sticky-action {
        z-index: 2;
    }
</style>