<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventoryReserveItem */
/* @var $form yii\widgets\ActiveForm */
$maxQty = ($model->inventoryDetail->stock_available + $model->request_qty);
?>

<div class="edit-qty-reserve-item-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pt-2 pb-1 pl-3 pr-1">
                    <h5>Item Details</h5>
                </div>
                <div class="card-body p-3">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <strong>Model Type:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= Html::encode($model->inventoryDetail->model->type ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <strong>Brand:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= Html::encode($model->inventoryDetail->model->inventoryBrand->name ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= Html::encode($model->inventoryDetail->model->description ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <strong>Supplier:</strong>
                        </div>
                        <div class="col-md-9">
                            <?= Html::encode($model->inventoryDetail->supplier->name ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-3">
                            <strong>Currently Requested:</strong>
                        </div>
                        <div class="col-md-9"><?= (int) ($model->request_qty ?? 0) ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'request_qty')->textInput([
                                'type' => 'number',
                                'class' => 'form-control text-center',
                                'min' => 1,
                                'max' => $maxQty,
                                'step' => 1,
                                'value' => $model->request_qty,
                            ])->label('Update Requested Quantity <small class="text-info font-weight-normal ml-2">** Max: ' . $maxQty . ' (available stock in inventory)</small>')
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div>
                                <?= Html::submitButton('Save Changes', ['class' => 'btn btn-success']) ?>
                                <?= Html::a('Cancel', ['material-request-list', 'type' => $moduleIndex], ['class' => 'btn btn-secondary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
