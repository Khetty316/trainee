<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */


$this->title = 'Edit Pending Asset';
$this->params['breadcrumbs'][] = ['label' => 'Asset Management'];
$this->params['breadcrumbs'][] = ['label' => 'New Asset (Pending)', 'url' => ['/asset/asset-pending-register']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asset-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formAssetMaster',[
        'model' => $model,
        'userType'=>'normalUser'
    ]) ?>

</div>
