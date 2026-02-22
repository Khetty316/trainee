<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\common\AuditTrailPageVisitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Audit Trail Page Visits';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-trail-page-visit-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table-sm table table-striped table-bordered'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'columns' => [
            'id',
            'page',
            [
                'attribute' => 'user_id',
                'value' => function($model) {
                    return $model->user_id . "-" . $model->user->fullname;
                }
            ],
            'created_at',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
