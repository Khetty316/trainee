<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsAssetList */

$this->title = 'Update Cmms Asset List: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Asset Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="cmms-asset-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'vModel' => $vModel,
        'faults' => $faults,
        'isUpdate' => true,
    ]) ?>

</div>
