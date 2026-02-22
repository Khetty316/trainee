<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\project\ProjectMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Project';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-master-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <!--<p>-->
    <?php //= Html::a('Create Project Master', ['create'], ['class' => 'btn btn-success']) ?>
    <!--</p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            [
                'attribute' => 'proj_code',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->proj_code, \yii\helpers\Url::to('view?id=' . $model->id));
//                            . Html::a('<i class="far fa-edit text-primary"></i>', \yii\helpers\Url::to('update?id=' . $model->id), ['class' => 'float-right pr-1']);
                }
            ],
            'title_short',
            'project_status',
            [
                'attribute' => 'location',
                'value' => function($model) {
                    return $model['location0']['area_name'];
                }
            ],
            [
                'attribute' => 'client_id',
                'value' => function($model) {
                    return $model['client']['company_name'];
                }
            ],
            'service',
            [
                'attribute' => 'contract_sum',
                'value' => function($model) {
                    return \common\models\myTools\MyFormatter::asDecimal2($model['contract_sum']);
                }
            ],
            'client_pic_name',
            'client_pic_contact',
            //'award_date',
            //'commencement_date',
            //'eot_date',
            //'handover_date',
            //'dlp_expiry_date',
            //'proj_director',
            [
                'attribute' => 'proj_director',
//                'label' => 'Person In Charge',
                'value' => function($model) {
                    return $model['projDirector']['fullname'];
                }
            ],
        //'proj_manager',
        //'site_manager',
        //'proj_coordinator',
        //'project_engineer',
        //'site_engineer',
        //'site_supervisor',
        //'project_qs',
        //'created_by',
        //'created_at',
        //'updated_by',
        //'updated_at',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
