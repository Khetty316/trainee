<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\asset\AssetMaster */

$this->title = 'Create Asset Master';
$this->params['breadcrumbs'][] = ['label' => 'Asset Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="asset-master-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'userType'=>'normalUser'
    ]) ?>

</div>
