<?php
/* @var $model app\models\Asset */
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<!--<div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h6 class="mb-1 font-weight-bold">
                Fault List – <? Html::encode($faultModel->id ?? 'New') ?>
            </h6>
            <small class="text-muted">
                Created on: <? Yii::$app->formatter->asDate($faultModel->reported_at, 'php:d M Y') ?>
            </small>
        </div>

        <div class="text-right mt-2 mt-md-0">
            <small class="text-muted d-block">Status</small>
            <span class="badge badge-info">
                <? Html::encode($faultModel->status ?? 'Draft') ?>
            </span>
        </div>
</div>-->
<div class="card-body p-2 table-responsive">
<div class="cmms-fault-list-form table-responsive" id="fault-form-container">
<table class="table table-bordered align-middle" id="item_table">
    <thead class="table-dark text-center">
        <th>Area</th>
        <th>Section</th>
        <th>Machine Description</th>
        <th>Manufacturer</th>
        <th>Serial Number</th>
        <th>Date of Purchase</th>
        <th>Date of Installation</th>
    </thead>
        <tr>
            <td><?= $model->area ?></td>
            <td><?= $model->section ?></td>
            <td><?= $model->name ?></td>
            <td><?= $model->manufacturer ?></td>
            <td><?= $model->serial_no ?></td>
            <td><?= $model->date_of_purchase ?></td>
            <td><?= $model->date_of_installation ?></td>
        </tr>
    </table>
</div>