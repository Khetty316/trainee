<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\cmms\CmmsStockDispatchMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Inventory Control - Maintenance';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-stock-dispatch-master-index">
    <?= $this->render('__cmmsStockDispatchNavbar', ['pageKey' => '2']) ?>

    <?php
    echo $this->render('_acknowledgementList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
    ?>
    
</div>
