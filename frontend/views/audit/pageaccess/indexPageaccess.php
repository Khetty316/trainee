<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\User;
use common\models\myTools\MyFormatter;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\common\AuditTrailPageAccessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Audit Trail Page Accesses';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-trail-page-access-index">


    <?php
    echo $this->render('__pageaccessNavBar', ['module' => 'admin', 'pageKey' => '2']);
    ?>

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
            'id',
            'page',
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => function($model) {
                    return User::findOne($model->user_id)->fullname;
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function($model) {
                    return MyFormatter::asDateTime_Read($model->created_at);
                }
            ],
        ],
    ]);
    ?>


</div>
