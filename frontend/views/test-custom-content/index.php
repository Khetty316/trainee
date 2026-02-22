<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\test\TestCustomContentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Test Custom Contents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-custom-content-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Test Custom Content', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'test_form_id',
            'content:ntext',
            'content_order',
            'is_deleted',
            //'created_at',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
