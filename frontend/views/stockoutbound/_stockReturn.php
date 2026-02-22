<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\bom\StockDispatchTrial;

$getStatus = StockDispatchTrial::RETURN_STATUS;
$limit = $dispatchMaster->total_trial_dispatch_qty;
?>

<div class="stock-detail">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myForm',
                'method' => 'post',
                'action' => yii\helpers\Url::to(['return-dispatched-quantity', 'dispatchId' => $dispatchMaster->dispatch_id, 'detailId' => $dispatchMaster->stock_outbound_details_id]),
    ]);
    ?>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Material Detail:</legend>
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered">
                        <tr>
                            <td class="col-3">Model Type</td>
                            <td class="col-9"><?= $dispatchMaster->model_type ?></td>
                        </tr>
                        <tr>
                            <td>Brand</td>
                            <td><?= $dispatchMaster->brand ?></td>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <td><?= $dispatchMaster->descriptions ?></td>
                        </tr>
                        <tr>
                            <td>Total Quantity</td>
                            <td><?= $dispatchMaster->detail_qty ?></td>
                        </tr>
                        <tr>
                            <td>Total Dispatched Quantity</td>
                            <td><?= $dispatchMaster->dispatched_qty == 0 ? 0 : $dispatchMaster->dispatched_qty ?></td>
                        </tr>
                        <tr>
                            <td>Total Unacknowledged Dispatch Quantity</td>
                            <td><?= $dispatchMaster->unacknowledged_qty == 0 ? 0 : $dispatchMaster->unacknowledged_qty ?></td>
                        </tr>
                    </table>
                </div>
            </fieldset>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12">
            <fieldset class="border p-1">
                <legend class="w-auto px-2 m-0">Dispatch Detail:</legend>           
                <div class="row mb-2">
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <input type="hidden" name="dispatch[dispatch_id]" class="form-control form-control-sm" value="<?= $dispatchMaster->dispatch_id ?>" />
                        <input type="hidden" name="dispatch[detail_id]" class="form-control form-control-sm" value="<?= $dispatchMaster->stock_outbound_details_id ?>" />
                        <input type="hidden" name="dispatch[total_trial_qty]" class="form-control form-control-sm" value="<?= $dispatchMaster->total_trial_dispatch_qty ?>" />
                        <?= $form->field($dispatchMaster, 'dispatch_no')->textInput(['disabled' => true]) ?>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12">
                        <?= $form->field($dispatchMaster, 'received_by')->textInput(['disabled' => true]) ?>
                    </div> 
                    <div class="col-lg-3 col-md-12 col-sm-12">
                        <?= $form->field($dispatchMaster, 'total_trial_dispatch_qty')->textInput(['disabled' => true])->label("Dispatched Quantity") ?>
                    </div>
                    <div class="col-lg-4 col-md-12 col-sm-12 mb-3">
                        <label for="addComplete-counter">Return Dispatched Quantity</label>
                        <div class="d-flex align-items-center" id="addComplete-counter">
                            <input type="number" id="counter-input" name="dispatch[dispatch_qty]" value="<?= $dispatchMaster->total_trial_dispatch_qty ?>" min="1" max="<?= $limit ?>" class="form-control" />
                            <button type="button" class="btn btn-warning me-2 ml-2" onmousedown="startDecrement()" onmouseup="stopChanging()" onmouseleave="stopChanging()" ontouchstart="startDecrement()" ontouchend="stopChanging()">-1</button>
                            <button type="button" class="btn btn-warning ms-2 ml-2" onmousedown="startIncrement()" onmouseup="stopChanging()" onmouseleave="stopChanging()" ontouchstart="startIncrement()" ontouchend="stopChanging()">+1</button>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12 col-sm-12 required">
                        <label for="remarkTextarea">Remark</label>
                        <textarea class="form-control" id="remarkTextarea" name="dispatch[remark]"></textarea>
                    </div>
                </div>
            </fieldset>        
            <?= Html::submitButton('Save', ['class' => 'btn btn-success px-3 float-right proceed mt-3']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div> 
</div>
<script>
    $(document).ready(function () {
        $('#counter-input').on('change', function (event) {
            const input = event.target;
            let errorMessage = document.getElementById('error-message');

            if (parseInt(input.value, 10) === 0 || !input.value.trim()) {
                input.classList.add('is-invalid');
                errorMessage.textContent = 'Invalid input. The new dispatch quantity cannot be empty or zero.';
                errorMessage.style.display = 'block';
            } else {
                input.classList.remove('is-invalid');
                errorMessage.style.display = 'none';
            }
        });

        const remarkField = $('#remarkTextarea');
        const parentDiv = remarkField.closest('.col-lg-6');
        parentDiv.addClass('required');
        parentDiv.find('label[for="remarkTextarea"]').addClass('required');
        $('#remarkTextarea').on('input', function () {
            const remarkErrorMessage = this.nextElementSibling;
            if (this.value.trim()) {
                $(this).removeClass('is-invalid');
                if (remarkErrorMessage) {
                    remarkErrorMessage.textContent = '';
                }
            }
        });

        $('.proceed').click(function (e) {
            let isValid = true;

            const dispatchInput = document.getElementById('counter-input');
            const dispatchErrorMessage = document.getElementById('error-message');
            if (parseInt(dispatchInput.value, 10) === initialDispatchedQty) {
                isValid = false;
                dispatchInput.classList.add('is-invalid');
                dispatchErrorMessage.style.display = 'block';
            } else {
                dispatchInput.classList.remove('is-invalid');
                dispatchErrorMessage.style.display = 'none';
            }

            const remarkField = document.querySelector('#remarkTextarea');
            let remarkErrorMessage = remarkField.nextElementSibling;
            if (!remarkErrorMessage || !remarkErrorMessage.classList.contains('invalid-feedback')) {
                remarkErrorMessage = document.createElement('div');
                remarkErrorMessage.className = 'invalid-feedback';
                remarkField.parentNode.appendChild(remarkErrorMessage);
            }

            if (!remarkField.value.trim()) {
                isValid = false;
                remarkField.classList.add('is-invalid');
                remarkErrorMessage.textContent = 'Remark is required.';
            } else {
                remarkField.classList.remove('is-invalid');
                remarkErrorMessage.textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
            } else {
                const userConfirmation = confirm('Are you sure you want to proceed?');
                if (!userConfirmation) {
                    e.preventDefault();
                }
            }
        });

        var counterInput = document.getElementById('counter-input');
        var interval;
        const limit = <?= $limit ?>;
        function startDecrement() {
            interval = setInterval(function () {
                var currentValue = parseInt(counterInput.value) || 0;
                if (currentValue > 1) {
                    counterInput.value = currentValue - 1;

                    $(counterInput).trigger('change');
                }
            }, 70);
        }

        function startIncrement() {
            interval = setInterval(function () {
                var currentValue = parseInt(counterInput.value) || 0;
                if (currentValue < limit) {
                    counterInput.value = currentValue + 1;

                    $(counterInput).trigger('change');
                }
            }, 70);
        }

        function stopChanging() {
            clearInterval(interval);
        }

        window.startDecrement = startDecrement;
        window.startIncrement = startIncrement;
        window.stopChanging = stopChanging;
    });
</script>
