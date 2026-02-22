<?php

use frontend\models\common\RefProjectQTypes;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefProjectQTypeSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Ref Project Q Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ref-project-qtypes-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Ref Project Q Types', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'code',
            'project_type_name',
            'fab_dept_percentage',
            'elec_dept_percentage',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, RefProjectQTypes $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'code' => $model->code]);
                 }
            ],
        ],
    ]); ?>


</div>
