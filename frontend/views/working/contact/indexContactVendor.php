<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\working\contact\ContactMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Contact Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-master-index">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->
    <?= $this->render('__ContactNavBar', ['module' => 'contacts', 'pageKey' => '2']) ?>
    <?php $this->params['breadcrumbs'][] = $this->title; ?>
    <p>
        <?= Html::a('Create Contact <i class="fas fa-plus"></i>', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
//    $dataProvider->pagination=['pagesize'=>'100'];
    echo GridView::widget([
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
        'columns' => [
            'company_name',
            'contact_person',
            'contact_position',
            'contact_number',
            'email:email',
            'address',
            'postcode',
            [
                'attribute' => 'area',
                'value' => function($model) {
                    return $model['area0']['area_name'];
                }
            ],
            [
                'attribute' => 'state',
                'value' => function($model) {
                    return $model['state0']['state_name'];
                }
            ],
            [
                'attribute' => 'country',
                'value' => function($model) {
                    return $model['country0']['country_name'];
                }
            ],
//            [
//                'attribute' => 'created_at',
//                'value' => function($model) {
//                    return common\models\myTools\MyFormatter::asDateTime_Read($model->created_at);
//                }
//            ],
//            [
//                'attribute' => 'created_by',
//                'value' => function($model) {
//                    return $model['createdBy']['fullname'];
//                }
//            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
