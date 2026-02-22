<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\project\MasterProjectsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Code List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="master-projects-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <p class="font-weight-lighter text-success">Project Codes in this list are configured and selectable in multiple modules, e.g. Master Incoming, Claims etc.</p>
    <p>
        <?= Html::a('Create Record <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?=
    GridView::widget([
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            [
                'attribute' => 'project_code',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->project_code, \yii\helpers\Url::to('view?id=' . $model->project_code))
                            . Html::a('<i class="far fa-edit text-primary"></i>', \yii\helpers\Url::to('update?id=' . $model->project_code), ['class' => 'float-right pr-1']);
                }
            ],
            'project_name',
            'project_description',
            [
                'attribute' => 'created_by',
                'value' => function($model) {
                    return $model->createdBy->fullname;
                }
            ],
            [
                'attribute' => 'person_in_charge',
                'value' => function($model) {
                    return $model['personInCharge']['fullname'];
                }
            ],
//      
        ],
    ]);
    ?>


</div>
