<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectProduction\ProjectProductionMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'BQ - Project List';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-index">

    <!--<h3><?= Html::encode($this->title) ?></h3>-->
    <?= $this->render('__materialBqNavBar', ['pageKey' => '1']) ?>

    <?php
    echo $this->render("_gridViewMaterialBq", [
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel,
        'isMain'=>true
    ]);
    ?>

 
</div>
