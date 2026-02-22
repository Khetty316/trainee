<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */

$this->title = 'New Asset';
$this->params['breadcrumbs'][] = ['label' => 'Asset Management', 'url' => ['/asset/index-asset-super']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asset-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_formAssetMaster',[
        'model' => $model,
        'modelTracking'=>$modelTracking,
        'userType'=>'superUser'
    ]) ?>

</div>
