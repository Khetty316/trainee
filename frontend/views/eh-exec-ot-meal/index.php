<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\employeeHandbook\EhExecOtMealSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Eh Exec Ot Meals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="eh-exec-ot-meal-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Eh Exec Ot Meal', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'eh_master_id',
            'created_by',
            'created_at',
            'updated_by',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
