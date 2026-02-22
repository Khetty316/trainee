<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectProduction\ProjectProductionMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'BQ - Project List';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-index">
    <?= $this->render('__materialBqNavBar', ['pageKey' => '2']) ?>
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
            [
                'attribute' => 'project_production_code',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->project_production_code, ['view-material-bq', 'id' => $model->id]);
                }
            ],
            'name',
            'remark:ntext',
            'quotation_id',
        ],
    ]);
    ?>


</div>
