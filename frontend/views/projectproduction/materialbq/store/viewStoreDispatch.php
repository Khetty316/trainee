<?php

use yii\helpers\Html;

$this->title = $model->dispatch_no;
$this->params['breadcrumbs'][] = ['label' => 'Store Dispatch', 'url' => ['/production/material-bq-store/index-to-dispatch']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= Html::encode($this->title) ?></h3>
<div class="row">
    <div class="col-md-5 order-md-2">
        <?= $this->render("../_detailViewProjProdDetail", ['projProdMaster' => $model->fabBqMaster->projProdPanel->projProdMaster, 'panel' => $model->fabBqMaster->projProdPanel]) ?>
    </div>
    <div class="col-md-7 order-md-1">
        <?php
        echo $this->render('_ajaxViewStoreDispatch', ['model' => $model]);
        ?>
    </div>
</div>