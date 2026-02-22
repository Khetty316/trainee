<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;
?>
<?= $this->render('__navBarProjProdProc', ['pageKey' => '2']) ?>
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
                    'attribute' => 'dispatch_no',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::a($model->dispatch_no, ['view-proc-dispatched', 'dispatchId' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'proj_prod_panel_id',
                    'format' => 'raw',
                    'value' => function($model) {
                        return $model->projProdPanel->project_production_panel_code;
                    }
                ],
                [
                    'attribute' => 'dispatched_at',
                    'format' => 'raw',
                    'value' => function($model) {
                        return MyFormatter::asDateTime_ReaddmYHi($model->dispatched_at);
                    }
                ],
                [
                    'attribute' => 'dispatched_by',
                    'format' => 'raw',
                    'value' => function($model) {
                        return (User::findOne($model->dispatched_by)->fullname ?? null);
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'filter' => \frontend\models\ProjectProduction\RefProdDispatchStatus::getDropDownList(),
                    'value' => function($model) {
                        return $model->status0->status_name;
                    }
                ],
                [
                    'attribute' => 'responded_at',
                    'format' => 'raw',
                    'value' => function($model) {

                        return MyFormatter::asDateTime_ReaddmYHi($model->responded_at);
                    }
                ],
                [
                    'attribute' => 'responded_by',
                    'format' => 'raw',
                    'value' => function($model) {
                        return (User::findOne($model->responded_by)->fullname ?? null);
                    }
                ],
            ],
        ]));
        ?>
    </div>
</div>