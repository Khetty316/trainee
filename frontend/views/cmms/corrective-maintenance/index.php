<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

$this->title = "Corrective Maintenance";
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .no-wrap {
        white-space: nowrap;
        overflow: visible;
    }
</style>
<div class="table-responsive">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Work Request Form', ['create-work-request'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>
    <?= GridView::widget([
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
            'submitted_by',
            [
                'attribute' => 'machine_breakdown_type',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    $machineBreakdownType = $model->getMachineBreakdownType();
                    return $machineBreakdownType->name;
                },
            ],
            'reviewed_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
