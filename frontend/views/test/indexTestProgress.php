<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = "Test Progress";
$this->params['breadcrumbs'][] = ['label' => 'Test Project List', 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = $project->project_production_code;
?>

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <fieldset class="form-group border p-3">
            <legend class="w-auto px-2 m-0"><h5 class="m-0">Project Details:</h5></legend>
            <?php
            echo $this->render("../projectproduction/main/_detailviewProjectProduction", [
                'model' => $project
            ]);
            ?>
        </fieldset>
    </div>

</div>
<div class="test-progress-view">        
    <?= $this->render('_indexProjectNavBar', ['project' => $project, 'pageKey' => '2']) ?>
    <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '/test/testing/index-test-progress?id=' . $project->id, ['class' => 'btn btn-primary mt-3']) ?>

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
                [
                    'attribute' => 'tc_ref',
                    'contentOptions' => ['class' => 'col-2'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->tc_ref, ['index-master-detail', 'id' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'panel_desc',
                    'contentOptions' => ['class' => 'col-2 text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        return $model->panel_desc;
                    }
                ],
                [
                    'attribute' => 'prod_panel_code',
                    'contentOptions' => ['class' => 'col-2 text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        return $model->prod_panel_code;
                    }
                ],
                [
                    'attribute' => 'test_type',
                    'contentOptions' => ['class' => 'col-2 text-center'],
                    'headerOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        return $model->test_type;
                    }
                ],
                [
                    'attribute' => 'status',
                    'contentOptions' => ['class' => 'col-1 text-center'],
                    'headerOptions' => ['class' => 'col-1 text-center'],
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

