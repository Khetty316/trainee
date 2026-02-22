<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\common\RefProjectQPanelUnit;
use frontend\models\ProjectProduction\RefProjProdBqStatus;
?>
<?= $this->render('__navBarProjProdProc', ['pageKey' => '1']) ?>
<div class="row">
    <div class="col-12">
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
        <?=
        GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'project_production_panel_code',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a($model->project_production_panel_code, ['view-pending-order', 'panelId' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'proj_prod_master',
                    'format' => 'raw',
                    'label' => 'Project Code',
                    'value' => function($model) {
                        return $model->projProdMaster->project_production_code;
                    }
                ],
                [
                    'attribute' => 'quantity',
                    'format' => 'raw',
                    'headerOptions' => ['class' => 'text-right'],
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function($model) {
                        return MyFormatter::asDecimal2($model->quantity);
                    }
                ],
                [
                    'attribute' => 'unit_code',
                    'format' => 'raw',
                    'filter' => RefProjectQPanelUnit::getDropDownList(),
                    'value' => function($model) {
                        return $model->unitCode->unit_name;
                    }
                ],
                [
                    'attribute' => 'item_dispatch_status',
                    'label' => 'Dispatch Status',
                    'format' => 'raw',
                    'filter' => RefProjProdBqStatus::getDropDownList(),
                    'value' => function($model) {
                        return $model->itemDispatchStatus->status_name ?? null;
                    }
                ],
//            'id',
//            'dispatch_no',
//            'proj_prod_panel_id',
//            'remarks:ntext',
//            'dispatched_at',
            //'dispatched_by',
            //'received_at',
            //'received_by',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
//            ['class' => 'yii\grid\ActionColumn'],
            ],
        ]));
        ?>
    </div>
</div>
