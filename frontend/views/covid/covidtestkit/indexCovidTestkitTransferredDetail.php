<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>
<div class="covid-testkit-inventory-index">

    <?php
    echo $this->render('__covidTestkitNavBar', ['module' => 'admin', 'pageKey' => '3']);
    ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'label' => 'Staff Name',
                'value' => function($model) {
                    return $model->user->fullname;
                }
            ],
            'brand',
            [
                'attribute' => 'record_date',
                'format' => 'raw',
                'label' => 'Stock Out Date',
                'value' => function($model) {
                    return \common\models\myTools\MyFormatter::asDate_Read($model->inventory->record_date);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function($model) {
                    return \common\models\myTools\MyFormatter::asDate_Read($model->created_at);
                }
            ],
            [
                'attribute' => 'complete_status',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->complete_status ? "Yes" : "<span class='text-red'>No</span>";
                }
            ],
            [
                'attribute' => 'result_attachment',
                'format' => 'raw',
                'filter' => 'false',
                'value' => function($model) {
//                            return ;
                    if ($model->result_attachment) {
                        return Html::a(" <i class='far fa-file-alt m-1' ></i>", "/covidform/get-file?filename=" .
                                        urlencode($model->result_attachment), ['target' => "_blank", 'class' => 'mr-2']);
                    }
                }
            ],
            [
                'attribute' => 'remark',
                'format' => 'raw',
                'value' => function($model) {
                    return nl2br(Html::encode($model->remark)); 
                }
            ],
        ],
    ]);
    ?>

</div>
