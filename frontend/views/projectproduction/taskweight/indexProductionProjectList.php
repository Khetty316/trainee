<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectProduction\ProjectProductionMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project List';
$this->params['breadcrumbs'][] = ['label' => 'Panel Task Weight'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>
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
                'attribute' => 'project_production_code',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->project_production_code, ['view-production-project-panels', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'name',
                'contentOptions' => ['style' => 'white-space:normal!important']
            ],
            [
                'attribute' => 'client_id',
                'contentOptions' => ['class' => 'col-sm-1'],
                'headerOptions' => ['class' => 'col-sm-1'],
                'filter' => \frontend\models\client\Clients::getDropDownList(),
                'value' => function ($model) {
                    return $model->clientName;
                }
            ],
            [
                'attribute' => 'remark',
                'contentOptions' => ['style' => 'white-space:normal!important']
            ],
            [
                'attribute' => 'fab_complete_percent',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    return MyFormatter::asDecimal2_emptyZero($model->fab_complete_percent) . " %";
                }
            ],
            [
                'attribute' => 'elec_complete_percent',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    return MyFormatter::asDecimal2_emptyZero($model->elec_complete_percent) . " %";
                }
            ],
            [
                'attribute' => 'quotation_id',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->quotation->quotation_display_no . ' <i class="fas fa-external-link-alt"></i>', ['/projectquotation/view-projectquotation', 'id' => $model->quotation_id], ['target' => '_blank']);
                }
            ],
            [
                'attribute' => 'created_by',
                'label' => 'Pushed By',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->createdBy->fullname);
                }
            ],
        //'client_id',
        //'proj_prod_category',
        //'created_at',
        //'updated_by',
        //'updated_at',
        ],
    ]);
    ?>


</div>
