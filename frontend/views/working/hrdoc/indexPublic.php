<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\documentreminder\HrPublicDocumentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'HR Public Documents';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-reminder-master-index">

    <?= $this->render('_hrNavBar', ['module' => 'hrdoc', 'pageKey' => '2']) ?>

    <p class="mt-3">
        <?= Html::a('Public Document <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>    
    </p>

    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'options' => ['style' => 'overflow-x: auto;'], // Enable horizontal scrolling if needed
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'id',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:1%'],
                'value' => function ($model) {
                    return $model->id;
                }
            ],
            'category',
//            [
//                'attribute' => 'active_sts',
//                'format' => 'raw',
//                'value' => function($model) {
//                    return $model->active_sts ? "Yes" : "No";
//                }
//            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->description, ['view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'filename',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->filename) {
                        return Html::a(substr($model->filename, 15) . " <i class='far fa-file-alt m-1' ></i>", ["/working/hr-public-document/get-file", 'id' => $model->id], ['target' => "_blank"]);
                    } else {
                        return null;
                    }
                }
            ],
            [
                'attribute' => 'file_date',
                'format' => 'html',
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->file_date);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'file_date',
                    'language' => 'en',
                    'dateFormat' => "dd-MM-yyyy",
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'headerOptions' => ['style' => 'width: 40%;'],
                'attribute' => 'remark',
                'format' => 'ntext',
                'value' => function ($model) {
                    return $model->remark;
                }
            ],
//            [
//                'attribute' => 'show_alert',
//                'contentOptions' => ['class' => 'text-center'],
//                'value' => function ($model) {
//                    return $model->show_alert == 0 ? 'No' : 'Yes';
//                },
//                'filter' => Html::activeDropDownList(
//                        $searchModel,
//                        'show_alert',
//                        [
//                            '' => 'All',
//                            '0' => 'No',
//                            '1' => 'Yes'
//                        ],
//                        ['class' => 'form-control text-center']
//                )
//            ]
        ],
    ]));
    ?>


</div>
