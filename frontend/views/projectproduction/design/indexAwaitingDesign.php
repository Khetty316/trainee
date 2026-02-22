<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectProduction\ProjectProductionMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Design - Project List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-index">

    <h3><?= Html::encode($this->title) ?></h3>


    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'project_production_code',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->project_production_code, ['view-awaiting-design', 'id' => $model->id]);
                }
            ],
            'name',
            'remark:ntext',
            'quotation_id',
        ],
    ]));
    ?>


</div>
