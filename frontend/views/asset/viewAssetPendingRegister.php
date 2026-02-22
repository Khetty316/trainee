<?php

use yii\helpers\Html;

//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Asset Management'];
$this->params['breadcrumbs'][] = ['label' => 'New Asset (Pending)', 'url' => ['/asset/asset-pending-register']];
$this->params['breadcrumbs'][] = "Awaiting for approval";
\yii\web\YiiAsset::register($this);
?>
<div class="asset-master-view">

    <p>
        <?= Html::a('Edit <i class="fas fa-pencil-alt"></i>', ['edit-pending-asset', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel <i class="fas fa-times fa-lg"></i>', ['cancel-asset-personal', 'id' => $model->id], ['class' => 'btn btn-danger', 'data-method' => 'post', 'data-confirm' => 'Are you sure to cancel?']) ?>
    </p>
    <?=
    $this->render('_viewAssetDetailView', [
        'model' => $model,
    ])
    ?>


</div>
