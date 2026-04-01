<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\cmms\CmmsAssetList */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Asset Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cmms-asset-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
<fieldset class="form-group border p-3">
    <legend class="w-auto px-2 m-0">Asset Details:</legend>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'area',
            'section',
            'manufacturer',
            'serial_no',
            'date_of_purchase',
            'date_of_installation',
        ],
    ]) ?>
</fieldset>
    <fieldset class="form-group border p-3">
    <legend class="w-auto px-2 m-0">Asset Fault Details:</legend>
    <table class="table table-sm mt-2" width="100%">
        <thead class="table-dark">
            <tr>
                <th class="text-center">Fault Type</th>
                <th class="text-center">Primary Fault</th>
                <th class="text-center">Secondary Fault</th>
            </tr>
        </thead>
        <tbody id="listTBody">  
            <?php
                $faults = array_filter($faults, function ($m) {
                    return (int)$m->active_sts === 1;
                });
            ?>
            <?php foreach ($faults as $mD => $fault): ?>
                <?php $key = $fault->id ?? $index; ?>
                <tr data-index="<?= $key ?>">
                    <td class="text-center"><?= $fault->fault_type; ?></td>
                    <td class="text-center"><?= $fault->fault_primary_detail; ?></td>
                    <td class="text-center"><?= $fault->fault_secondary_detail; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </fieldset>

</div>
