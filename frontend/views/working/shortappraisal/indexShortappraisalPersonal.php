<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\appraisal\ShortAppraisalMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Short Appraisal';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="short-appraisal-master-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Create <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

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
                'attribute' => 'date',
                'format' => 'raw',
                'value' => function($model) {
                    return MyFormatter::asDate_Read($model->date);
                }
            ],
            'content:ntext',
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function($model) {
                    return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
        ],
    ]);
    ?>


</div>
