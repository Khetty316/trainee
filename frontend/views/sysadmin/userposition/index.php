<?php

use frontend\models\common\RefUserDesignation;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\common\RefUserDesignationSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'User Position';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ref-user-designation-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Position', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);    ?>

    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'id',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->id,['view','id'=>$model->id]);
                }
            ],
            'design_name',
            'created_at',
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return (\common\models\User::findOne($model)->fullname) ?? null;
                }
            ],
        ],
    ]));
    ?>


</div>
