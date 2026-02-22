<?php

use frontend\models\test\TestMain;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use \common\models\myTools\MyFormatter;
?>

<div class="test-lists-index">
    <?= $this->render('_PanelTestNavBar', ['pageKey' => '2']) ?>
    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary mt-3']) ?> 

    <div class="col-lg-12 col-md-12 col-sm-12" style="overflow: auto">
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn',
                ],
                [
                    'attribute' => 'tc_ref',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->tc_ref, ['index-master-detail', 'id' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'project_name',
                    'value' => function ($model) {
                        return $model->project_name;
                    }
                ],
                [
                    'attribute' => 'prod_panel_code',
                    'value' => function ($model) {
                        return $model->prod_panel_code;
                    }
                ], [
                    'attribute' => 'panel_desc',
                    'value' => function ($model) {
                        return $model->panel_desc;
                    }
                ],
                [
                    'attribute' => 'panel_type',
                    'value' => function ($model) {
                        return $model->panel_type;
                    }
                ],
                [
                    'attribute' => 'test_type',
                    'value' => function ($model) {
                        return $model->test_type;
                    }
                ],
                [
                    'attribute' => 'test_num',
                    'value' => function ($model) {
                        return $model->test_num;
                    }
                ],
                [
                    'attribute' => 'panel_qty',
                    'value' => function ($model) {
                        return $model->panel_qty;
                    }
                ],
                [
                    'attribute' => 'date',
                    'value' => function ($model) {
                        return MyFormatter::asDate_Read($model->date);
                    }
                ],
                [
                    'attribute' => 'venue',
                    'value' => function ($model) {
                        return $model->venue;
                    }
                ],
                [
                    'attribute' => 'client',
                    'value' => function ($model) {
                        return $model->client;
                    }
                ],
                [
                    'attribute' => 'elec_consultant',
                    'value' => function ($model) {
                        return $model->elec_consultant;
                    }
                ],
                [
                    'attribute' => 'elec_contractor',
                    'value' => function ($model) {
                        return $model->elec_contractor;
                    }
                ],
                [
                    'attribute' => 'tested_by',
                    'value' => function ($model) {
                        return $model->tested_by;
                    }
                ],
                [
                    'attribute' => 'certified_by',
                    'value' => function ($model) {
                        return $model->certified_by;
                    }
                ],
                [
                    'attribute' => 'status',
                    'contentOptions' => ['class' => 'col-1 text-center'],
                    'format' => 'raw',
                    'filter' => \frontend\models\test\RefTestStatus::getDropDownListFiltered(),
                    'value' => function ($model) {
                        return $model->status;
                    }
                ],
            ],
        ]);
        ?>
    </div>
</div>
