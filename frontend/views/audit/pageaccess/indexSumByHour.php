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
    echo $this->render('__pageaccessNavBar', ['module' => 'admin', 'pageKey' => '1']);
    ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

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
                'attribute' => 'fullname',
            ],
            [
                'attribute' => 'theDate',
            ],
            [
                'attribute' => 'theTime',
            ],
            [
                'attribute' => 'times',
            ],
        ],
    ]);
    ?>


</div>
