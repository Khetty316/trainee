<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\StockOutboundMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Outbound';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-outbound-master-index">

    <h4><?= Html::encode($this->title) ?></h4>

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
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
                    return Html::a($model->project_production_code, ['view-panels', 'id' => $model->id]);
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
