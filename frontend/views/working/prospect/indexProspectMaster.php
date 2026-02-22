<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\project\ProspectMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Prospect Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prospect-master-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Create Prospect <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>

    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

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
            'attribute' => 'proj_code',
            'format' => 'raw',
            'value' => function($model) {
                return Html::a($model->proj_code, \yii\helpers\Url::to('view?id=' . $model->id))
                        . Html::a('<i class="far fa-edit text-primary"></i>', \yii\helpers\Url::to('update?id=' . $model->id), ['class' => 'float-right pr-1']);
            }
        ],
        'title_short',
            'title_long',
            [
                'attribute' => 'due_date',
                'value' => function($model) {
                    return \common\models\myTools\MyFormatter::asDate_Read($model->due_date);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'due_date',
                    'language' => 'en',
//                    'dateFormat' => 'dd-MM-yyyy',
                    'dateFormat' => 'yyyy-MM-dd',
//                    'dateFormat'=>'php:d/m/Y',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            [
                'attribute' => 'area',
                'value' => function($model) {
                    return $model['area0']['area_name'];
                }
            ],
            [
                'attribute' => 'staff_pic',
                'label' => 'Person In Charge',
                'value' => function($model) {
                    return $model['staffPic']['fullname'];
                }
            ],
            [
                'attribute' => 'other_pic',
                'label' => 'Person In Charge (Other)',
                'value' => function($model) {
                    return $model->other_pic;
                }
            ],
            [
                'attribute' => 'project_type',
                'value' => function($model) {
                    return $model->projectType->project_type_name;
                },
                'filter' => $projectTypeList
            ],
//            'created_by',
//            'created_at',
        ],
    ]);
    ?>


</div>
