<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\covid\form\CovidStatusForm */

$this->title = $model->user->fullname . ' (' . MyFormatter::asDate_Read($model->created_at) . ')';
$this->params['breadcrumbs'][] = ['label' => 'Covid-19 Health Declaration List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<style>
    table.detail-view th {
        width: 30%;
    }

    table.detail-view td {
        width: 70%;
    }
</style>
<div class="covid-status-form-view">
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Staff information:</legend>
        <div >
            <?=
            DetailView::widget([
                'model' => $model,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'options' => ['class' => 'table table-striped table-bordered detail-view fix-width table-sm'],
                'attributes' => [
                    [
                        'attribute' => 'user_id',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->user->fullname;
                        }
                    ],
                    [
                        'attribute' => 'to_take_action',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model['to_take_action'] <= 1) {
                                return '<span class="text-success">' . $model['toTakeAction']['description'] . '</span>';
                            } else if ($model['to_take_action'] == 2) {
                                return '<span class="text-warning">' . $model['toTakeAction']['description'] . '</span>';
                            } else {
                                return '<span class="text-red">' . $model['toTakeAction']['description'] . '</span>';
                            }
                        }
                    ],
                    [
                        'attribute' => 'body_temperature',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->body_temperature > $model::HIGH_FEVER) {
                                return "<span class='text-danger'>" . $model->body_temperature . "</span>";
                            } else {
                                return $model->body_temperature;
                            }
                        }
                    ],
                    [
                        'attribute' => 'spo2',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->spo2 >= 96) {
                                return $model->spo2;
                            } else if ($model->spo2 >= 95) {
                                return "<span class='text-success'>" . $model->spo2 . "</span>";
                            } else if ($model->spo2 >= 93) {
                                return "<span class='text-warning'>" . $model->spo2 . "</span>";
                            } else if ($model->spo2 <= 92) {
                                return "<span class='text-danger'>" . $model->spo2 . "</span>";
                            }
                        }
                    ],
                    'self_vaccine_dose',
                    [
                        'attribute' => 'created_at',
                        'format' => 'raw',
                        'value' => function($model) {
                            return MyFormatter::asDateTime_Read($model->created_at);
                        },
                    ],
                    [
                        'attribute' => 'self_symptom_list',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->generateSickList($model->self_symptom_list);
                        }
                    ],
                    [
                        'attribute' => 'self_symptom_other',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model->self_symptom_other ) ? "<span class='text-danger'>" . $model->self_symptom_other . "</span>" : "";
                        }
                    ],
                    [
                        'attribute' => 'self_place_list',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->generatePlaceList($model->self_place_list);
                        }
                    ],
                    [
                        'attribute' => 'self_place_other',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model->self_place_other ) ? "<span class='text-warning'>" . $model->self_place_other . "</span>" : "";
                        }
                    ],
                    [
                        'attribute' => 'self_test_is',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model['self_test_is']) ? "<span class='text-danger'>Yes</span>" : "No";
                        }
                    ],
                    [
                        'attribute' => 'self_test_date',
                        'format' => 'raw',
                        'value' => function($model) {
                            return MyFormatter::asDate_Read($model->self_test_date);
                        }
                    ],
                    'self_test_reason',
                    [
                        'attribute' => 'self_test_kit_type',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model['selfTestKitType']['description'];
                        }
                    ],
                    [
                        'attribute' => 'self_test_result',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->self_test_result == 'negative') {
                                return ucfirst($model->self_test_result);
                            } else {
                                return "<span class='text-danger'>" . ucfirst($model->self_test_result) . "</span>";
                            }
                        }
                    ],
                    [
                        'attribute' => 'self_test_result_attachment',
                        'format' => 'raw',
                        'value' => function($model) {
//                            return ;
                            if ($model->self_test_result_attachment) {
                                return Html::a(" <i class='far fa-file-alt m-1' ></i>", "/covidform/get-file?filename=" .
                                                urlencode($model->self_test_result_attachment), ['target' => "_blank", 'class' => 'mr-2']);
                            }
                        }
                    ],
                ],
            ])
            ?>
        </div>
    </fieldset>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2 m-0 font-weight-bold">Persons under same roof:</legend>
        <div >
            <?=
            DetailView::widget([
                'model' => $model,
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'options' => ['class' => 'table table-striped table-bordered detail-view fix-width table-sm'],
                'attributes' => [
                    'other_how_many',
                    'other_vaccine_two_dose',
                    [
                        'attribute' => 'other_symptom_list',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->other_symptom_list ? $model->generateSickList($model->other_symptom_list) : '-';
                        }
                    ],
                    [
                        'attribute' => 'other_symptom_other',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->other_symptom_other; // ($model->other_symptom_other ) ? "<span class='text-danger'>" . $model->other_symptom_other . "</span>" : "";
                        }
                    ],
                    [
                        'attribute' => 'other_place_list',
                        'format' => 'raw',
                        'value' => function($model) {
                            return $model->generatePlaceListOthers($model->other_place_list);
                        }
                    ],
                    [
                        'attribute' => 'other_place_other',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model->other_place_other ) ? "<span class='text-warning'>" . $model->other_place_other . "</span>" : "";
                        }
                    ],
                    [
                        'attribute' => 'other_test_is',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model['other_test_is']) ? "<span class='text-danger'>Yes</span>" : "No";
                        }
                    ],
                    [
                        'attribute' => 'other_test_reason',
                        'format' => 'raw',
                        'value' => function($model) {
                            return ($model->other_test_reason ) ? "<span class='text-danger'>" . $model->other_test_reason . "</span>" : "";
                        }
                    ],
                    [
                        'attribute' => 'other_test_result',
                        'format' => 'raw',
                        'value' => function($model) {
                            if ($model->other_test_result == 'negative') {
                                return ucfirst($model->other_test_result);
                            } else {
                                return "<span class='text-danger'>" . ucfirst($model->other_test_result) . "</span>";
                            }
                        }
                    ],
                ],
            ])
            ?>
        </div>
    </fieldset>
</div>
