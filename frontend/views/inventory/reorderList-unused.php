<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefUserDepartments;

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .grid-wrap {
        white-space: normal;
        word-break: break-word;
        text-align: left;
    }
    .w-300 {
        max-width: 300px;
    }
</style>

<?=
$this->render('__inventoryNavBar', [
    'module' => 'superior',
    'pageKey' => '5'
])
?>

<p>
    <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
</p>

<?=
GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => ['class' => yii\bootstrap4\LinkPager::class],
    'tableOptions' => ['class' => 'table table-bordered table-striped table-sm'],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'prereq_form_master_id',
            'label' => 'Pre-requisition Form Code',
            'value' => fn($model) => $model->prereqFormMaster->prf_no ?? '-',
        ],
        [
            'attribute' => 'department_code',
            'label' => 'Department',
            'contentOptions' => ['class' => 'grid-wrap w-300'],
            'value' => fn($model) => $model->department->department_name ?? '-',
            'filter' => Html::activeDropDownList(
                    $searchModel,
                    'department_name',
                    ArrayHelper::map(
                            RefUserDepartments::find()->orderBy('department_name')->all(),
                            'department_name',
                            'department_name'
                    ),
                    ['class' => 'form-control', 'prompt' => 'All']
            ),
        ],
        [
            'attribute' => 'requested_by',
            'value' => fn($model) =>
            $model->requestedBy->fullname . ' @ ' .
            MyFormatter::asDateTime_ReaddmYHi($model->requested_at),
        ],
        [
            'attribute' => 'approved_by',
            'value' => fn($model) => $model->approvedBy->fullname ?? '-',
        ],
        [
    'attribute' => 'status',
    'contentOptions' => ['class' => 'text-center'],
    'value' => fn ($model) => $model->status0->name ?? '-',
    'filter' => Html::activeDropDownList(
        $searchModel,
        'status',
        ArrayHelper::map(
                frontend\models\RefInventoryStatus::find()->all(),
            'id',
            'name'
        ),
        [
            'class' => 'form-control',
            'prompt' => 'All Status'
        ]
    ),
],
        [
            'attribute' => 'created_by',
            'value' => fn($model) =>
            $model->createdBy->fullname ?? '-' . ' @ ' .
                    MyFormatter::asDateTime_ReaddmYHi($model->created_at),
        ],
        [
    'format' => 'raw',
    'contentOptions' => ['class' => 'text-center'],
    'value' => fn ($model) =>
        Html::a(
            'View <i class="far fa-eye"></i>',
            ['view-reorder-item', 'id' => $model->id],
            ['class' => 'btn btn-sm btn-primary mx-1']
        ),
],

    ],
]);
?>
