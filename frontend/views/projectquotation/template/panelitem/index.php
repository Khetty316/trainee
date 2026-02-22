<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\projectquotation\ProjectQPanelItemsTemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Q Panel Items Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qpanel-items-template-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Project Q Panel Items Template', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'panel_template_id',
            'item_description',
            'cost',
            'markup',
            //'amount',
            //'product_id',
            //'sort',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
