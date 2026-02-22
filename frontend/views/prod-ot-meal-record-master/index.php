<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\prodOtMealRecord\ProdOtMealRecordMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = ($module === 'personal' ? 'My Claims - Personal' : 'Production Overtime Meal Record - Finance');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prod-ot-meal-record-master-index">
    <?= $this->render('/claim/__claimNavBar', ['module' => $module, 'pageKey' => $key]) ?>

    <p>
        <?php if ($module === 'personal') { ?>
            <?=
            Html::a("Create New Monthly Record",
                    "javascript:",
                    [
                        "onclick" => "event.preventDefault();",
                        "value" => \yii\helpers\Url::to(['create']),
                        "class" => "modalButtonMedium btn btn-success",
                        'data-modaltitle' => "Create New Monthly Record"
                    ]
            )
            ?>
        <?php } ?>
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-personal'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
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
//            'id',
            [
                'attribute' => 'ref_code',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    return $model->ref_code;
                }
            ],
            [
                'attribute' => 'month',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    return DateTime::createFromFormat('!m', $model->month)->format('F');
                },
                'filter' => [
                    1 => 'January',
                    2 => 'February',
                    3 => 'March',
                    4 => 'April',
                    5 => 'May',
                    6 => 'June',
                    7 => 'July',
                    8 => 'August',
                    9 => 'September',
                    10 => 'October',
                    11 => 'November',
                    12 => 'December',
                ],
            ],
            [
                'attribute' => 'year',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    return $model->year;
                }
            ],
            [
                'attribute' => 'dateFrom',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    return $model->dateFrom ? Yii::$app->formatter->asDate($model->dateFrom, 'php:d/m/Y') : '-';
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'dateFrom',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'dateFrom'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'dateTo',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    return $model->dateTo ? Yii::$app->formatter->asDate($model->dateTo, 'php:d/m/Y') : '-';
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'dateTo',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'dateTo'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'total_amount',
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'raw',
                'value' => function ($model) {
                    return \common\models\myTools\MyFormatter::asDecimal2($model->total_amount ?? 0.00);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    return "By " . ($model->createdBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'updated_at',
//                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    $update = ($model->updated_by === null ? '-' : "By " . ($model->updatedBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at));
                    return $update;
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'updated_at'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    switch ($model->status) {
                        case 0:
                            return '<span class="text-warning">Save Temporary</span>';
                        case 1:
                            return '<span class="text-primary">Has Been Finalized</span>';
                        case 2:
                            return '<span class="text-success">Claim Submitted</span>';
                        case 3:
                            return '<span class="text-danger">Deleted</span>';
                    }
                },
                'filter' => [
                    0 => 'Save Temporary',
                    1 => 'Finalized',
                    2 => 'Submitted to Claim',
                    3 => 'Deleted'
                ],
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) use ($module) {
                    if($module === 'personal'){
                        $action = 'view';
                    }else{
                        $action = 'finance-view';
                    }
                    return Html::a('View <i class="far fa-eye"></i>', [$action, 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                }
            ],
        ],
    ]);
    ?>


</div>
