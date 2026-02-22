<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use yii\grid\GridView;
use common\models\User;

//$this->registerJsFile('/js/vue.js', ['position' => $this::POS_HEAD]);




$this->title = Html::encode($bqMaster->bq_no);
$this->params['breadcrumbs'][] = ['label' => 'B.Q. List (All)', 'url' => ['index-material-bq']];
$this->params['breadcrumbs'][] = $this->title;

$dataProvider->sort = false;
$dataProvider->getModels();
$dataProvider->pagination = false;
?>

<div class="project-qpanels-view">
    <h3><?= Html::encode($this->title . " (" . ($bqMaster->bqStatus->status_name ?? null) . ")") ?></h3>
    <div class="row">
        <div class="col-md-5 order-md-2">
            <?= $this->render("_detailViewProjProdDetail", ['projProdMaster' => $bqMaster->projProdPanel->projProdMaster, 'panel' => $bqMaster->projProdPanel]) ?>
        </div>
        <div class="col-md-7 order-md-1">
            <?=
            $this->render("_gridViewMaterialBqItems", [
                'dataProvider' => $dataProvider
            ])
            ?>
        </div>
    </div>
</div>