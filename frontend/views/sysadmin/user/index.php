<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\sysadmin\user\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'System Admin - Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a('Create User <i class="fas fa-plus"></i>', ['signup'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'id',
                'contentOptions' => ['style' => 'width:10px']
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => function ($model) {
                    $str = Html::a($model->username, "/sysadmin/user/view?id=" . $model->id);
                    return $str;
                }
            ],
            'staff_id',
            'fullname',
            'ic_no',
            [
                'attribute' => 'sex',
                'format' => 'raw',
                'filter' => frontend\models\common\RefUserSex::getDropDownList(),
                'value' => function ($model) {
                    return $model->sex0->sex_name ?? null;
                }
            ],
            [
                'attribute' => 'date_of_join',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return common\models\myTools\MyFormatter::asDate_Read($model->date_of_join);
                },
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'date_of_join',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => [
                        'class' => 'form-control',
                        'autocomplete' => 'off',
                        'onchange' => '$("#w0").yiiGridView("applyFilter")',
                    ],
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd', // Format for sending to the server
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'date_of_join'), // Hidden input for sending formatted date
                    ],
                ]),
            ],
//            'email:email',
            'contact_no',
            [
                'attribute' => 'employment_type',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->employmentType->employment_type ?? null;
                }
            ],
//            [
//                'attribute' => 'company_name',
//                'format' => 'raw',
//                'value' => function($model) {
//                    return $model->companyName->company_name ?? null;
//                }
//            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    if ($data->status == 0) {
                        return "Deleted";
                    } else if ($data->status == 9) {
                        return "Inactive";
                    } else if ($data->status == 10) {
                        return "Active";
                    }
                },
                'filter' => ["10" => "Active", "9" => "Inactive", "0" => "Deleted"]
            ],
        ],
    ]);
    ?>


</div>
