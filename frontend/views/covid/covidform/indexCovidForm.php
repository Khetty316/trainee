<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\covid\form\CovidStatusFormSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Covid-19 Health Declaration List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="covid-status-form-index">

    <h3><?= Html::encode($this->title) ?></h3>


    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'columns' => [
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function($model) {
                    return Html::a($model->user->fullname, ['/covidform/view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function($model) {
                    return MyFormatter::asDateTime_Read($model->created_at);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => 'dd/MM/yyyy',
                    'options' => ['class' => 'form-control'],
                ]),
            ],
            'body_temperature',
            [
                'attribute' => 'to_take_action',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model['to_take_action'] <= 1) {
                        return '<span class="text-success">' . $model['toTakeAction']['description'] . '</span>';
                    } else {
                        return '<span class="text-danger">' . $model['toTakeAction']['description'] . '</span>';
                    }
                }
            ],
        //'self_symptom_list',
        //'self_symptom_other',
        //'self_place_list',
        //'self_place_other',
        //'self_test_is',
        //'self_test_date',
        //'self_test_reason',
        //'self_test_kit_type',
        //'self_covid_kit_id',
        //'other_how_many',
        //'other_vaccine_two_dose',
        //'other_symptom_list',
        //'other_symptom_other',
        //'other_place_list',
        //'other_place_other',
        //'other_test_is',
        //'other_test_reason',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
