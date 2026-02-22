<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Asset Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="asset-master-view">

    <!--<h1><?= Html::encode($this->title) ?></h1>-->

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>
  <?= $this->render('_viewAssetDetailView', [
        'model' => $model,
    ]) ?>


</div>
