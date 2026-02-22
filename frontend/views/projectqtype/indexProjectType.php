<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\projectquotation\ProjectQTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Q Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-qtypes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Project Q Types', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'project_id',
            'type',
            'remark:ntext',
            'is_finalized',
            //'created_at',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
