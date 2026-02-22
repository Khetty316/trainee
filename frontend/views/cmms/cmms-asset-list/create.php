<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsAssetList */

$this->title = 'Create Asset List';
$this->params['breadcrumbs'][] = ['label' => 'Cmms Asset Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cmms-asset-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'vModel' => $vModel,
        'isUpdate' => $isUpdate,
        'faults' => $faults
    ]) ?>

</div>
