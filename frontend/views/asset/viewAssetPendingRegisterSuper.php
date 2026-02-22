<?php

use yii\helpers\Html;

//$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Asset Management'];
$this->params['breadcrumbs'][] = ['label' => 'New Asset (Pending)', 'url' => ['/asset/asset-pending-register-super']];
$this->params['breadcrumbs'][] = "Awaiting for approval";
\yii\web\YiiAsset::register($this);
?>
<div class="asset-master-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Approve <i class="fas fa-check"></i>', ['super-approve-asset', 'id' => $model->id], ['class' => 'btn btn-success', 'data-method' => 'post', 'data-confirm' => 'Approve?']) ?>
        <?= Html::a('Reject <i class="fas fa-times fa-lg"></i>', ['super-reject-asset', 'id' => $model->id], ['class' => 'btn btn-danger', 'data-method' => 'post', 'data-confirm' => 'Reject?']) ?>
    </p>
    <?=
    $this->render('_viewAssetDetailView', [
        'model' => $model,
    ])
    ?>


</div>
