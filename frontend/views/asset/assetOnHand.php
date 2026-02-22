<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\asset\AssetMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$this->title = 'Asset On Hand';
//$this->params['breadcrumbs'][] = $this->title;
//\yii\web\YiiAsset::register($this);
?>
<div class="asset-master-index">
    <?php
    echo $this->render('__assetNavBar', ['module' => 'personal', 'pageKey' => '1']);
    ?>

    <p>
        <?php //= Html::a('Register New Asset <i class="fas fa-plus"></i>', ['create-asset-personal'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>


    <div class="pt-2">
        <?=
        $this->render('_viewAssetListGridView', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ])
        ?>
    </div>


</div>
