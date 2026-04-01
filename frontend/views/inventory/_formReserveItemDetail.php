<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\inventory\InventoryReserveItem */
/* @var $form yii\widgets\ActiveForm */

// Calculate minimum allowed quantity (can't reduce below already dispatched)
$minQty = $model->dispatched_qty ?? 1;
$maxQty = (($model->reserved_qty - $model->dispatched_qty) + $model->inventoryDetail->stock_available);
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
                        <div class="col-md-2">
                            <strong>Model Type:</strong>
                        </div>
                        <div class="col-md-10">
                            <?= Html::encode($model->inventoryDetail->model->type ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <strong>Brand:</strong>
                        </div>
                        <div class="col-md-10">
                            <?= Html::encode($model->inventoryDetail->model->inventoryBrand->name ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <strong>Description:</strong>
                        </div>
                        <div class="col-md-10">
                            <?= Html::encode($model->inventoryDetail->model->description ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <strong>Supplier:</strong>
                        </div>
                        <div class="col-md-10">
                            <?= Html::encode($model->inventoryDetail->supplier->name ?? 'N/A') ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <strong>Currently Reserved:</strong>
                        </div>
                        <div class="col-md-10"><?= (int) ($model->reserved_qty ?? 0) ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <strong>Already Dispatched:</strong>
                        </div>
                        <div class="col-md-10"><?= (int) ($model->dispatched_qty ?? 0) ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-2">
                            <strong>Balance:</strong>
                        </div>
                        <div class="col-md-10"><?= (int) ($model->available_qty ?? 0) ?>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <?=
                            $form->field($model, 'reserved_qty')->textInput([
                                'type' => 'number',
                                'class' => 'form-control text-center',
                                'min' => $minQty,
                                'max' => $maxQty,
                                'step' => 1,
                                'value' => $model->reserved_qty,
                            ])->label('Update Reserved Quantity <small class="text-info font-weight-normal ml-2">** Max: ' . $maxQty . ' (available stock in inventory)</small>')
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div>
                                <?= Html::submitButton('Save Changes', ['class' => 'btn btn-success']) ?>
                                <?= Html::a('Cancel', ['reserved-item-list', 'type' => $moduleIndex], ['class' => 'btn btn-secondary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
// Add validation script
$script = <<< JS
$(document).ready(function() {
    // Additional validation on quantity input
    $('.quantity-input').on('input', function() {
        let min = $(this).attr('min');
        let max = $(this).attr('max');
        let value = parseInt($(this).val()) || 0;
        
        if (value < min) {
            $(this).val(min);
            alert('Quantity cannot be less than ' + min + ' (already dispatched quantity)');
        } else if (value > max) {
            $(this).val(max);
            alert('Quantity cannot exceed ' + max + ' (available stock)');
        }
    });
});
JS;
$this->registerJs($script);
?>