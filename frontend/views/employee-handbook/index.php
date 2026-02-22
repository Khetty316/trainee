<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\employeeHandbook\EmployeeHandbookMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Employee Handbook Masters';
$this->params['breadcrumbs'][] = $this->title . ' - Super User';
?>
<div class="employee-handbook-master-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?php //= Html::a('Create Employee Handbook Master', ['create'], ['class' => 'btn btn-success']) ?>
        <?=
        Html::a("Create New Employee Handbook",
                "javascript:",
                [
                    "onclick" => "event.preventDefault();",
                    "value" => \yii\helpers\Url::to(['create']),
                    "class" => "modalButtonMedium btn btn-success mx-1",
                    'data-modaltitle' => "Create New Employee Handbook"
                ]
        )
        ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-super-user'],
                ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
                'attribute' => 'name',
                'value' => function ($model) {
                    return $model->name;
                }
            ],
            [
                'attribute' => 'edition_no',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return ($model->edition_no);
                }
            ],
            [
                'attribute' => 'edition_date',
                'contentOptions' => ['class' => 'col-sm-2'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Yii::$app->formatter->asDate($model->edition_date, 'php:d M Y');
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'edition_date',
                    'language' => 'en',
                    'dateFormat' => 'php:d M Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'edition_date'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'filter' => \frontend\models\office\employeeHandbook\EmployeeHandbookMaster::IS_ACTIVE,
                'value' => function ($model) {
                    return \frontend\models\office\employeeHandbook\EmployeeHandbookMaster::IS_ACTIVE_HTML[$model->is_active] ?? null;
                }
            ],
            [
                'attribute' => 'created_at',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    $date = date('d/m/Y', strtotime($model->created_at));
                    return "By " . ($model->createdBy->fullname) . " @ " . $date;
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
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->updated_at) {
                        return "By " . ($model->updatedBy->fullname) . " @ " . Yii::$app->formatter->asDate($model->updated_at, 'php:d M Y');
                    } else {
                        return null;
                    }
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d M Y',
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
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    return Html::a('View <i class="far fa-eye"></i>', ['view', 'id' => $model->id], ['class' => 'btn btn-sm btn-success mx-1']);
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
