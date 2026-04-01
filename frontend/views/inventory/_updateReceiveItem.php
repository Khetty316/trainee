<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
use common\models\myTools\MyFormatter;

if ($moduleIndex === 'execPendingReceiving') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'execAllReceiving') {
    $pageName = 'Receiving - Executive';
} else if ($moduleIndex === 'assistPendingReceiving') {
    $pageName = 'Receiving - Assistant';
} else if ($moduleIndex === 'assistAllReceiving') {
    $pageName = 'Receiving - Assistant';
}else if ($moduleIndex === 'maintenanceHeadPendingReceiving') {
    $pageName = 'Receiving - Head of Maintenance';
} else if ($moduleIndex === 'maintenanceHeadAllReceiving') {
    $pageName = 'Receiving - Head of Maintenance';
}

$url = 'po?type=' . $moduleIndex;

$this->title = 'Updates Order Receive';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName, 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => $po->po_no];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mb-5">
    <?php
    $form = ActiveForm::begin([
        'id' => 'myForm',
        'method' => 'post',
    ]);
    ?>

    <div class="row">
        <div class="col-md-8">
            <h5><?= Html::encode($this->title) ?></h5>
        </div>
        <div class="col-md-4 text-right">
            <?php
            if ($po->status !== frontend\models\RefInventoryStatus::STATUS_FullyReceived) {
                echo Html::button('Proceed', ['class' => 'btn btn-primary px-3 mb-3', 'onclick' => 'validateAndSubmit()']);
            }
            ?>
        </div>
    </div>

    <div class="alert alert-info border-left-primary mb-3">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-2">
                        <strong class="text-muted">PO No.:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= Html::encode($po->po_no) ?> 
                        </span>                      
                    </div>
                    <div class="col-md-2">
                        <strong class="text-muted">PO Date:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= MyFormatter::asDate_Read($po->po_date) ?> 
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Company Group:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?php $companyGroup = frontend\models\common\RefCompanyGroupList::findOne($po->company_group); ?>
                            <?= Html::encode($companyGroup->company_name) ?>
                        </span>                      
                    </div>
                    <div class="col-md-4">
                        <strong class="text-muted">Status:</strong>
                        <span class="ml-1 text-dark font-weight-bold">
                            <?= $po->status0->name ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0">
            <thead class="table-dark">
                <tr>
                    <th class="text-center">#</th>
                    <th>Item No.</th>
                    <th>Description</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Order Qty</th>
                    <th class="text-center">Received Qty</th>
                    <th class="text-center">Remaining Qty</th>
                    <th class="text-center">Receive Qty</th>
                    <th class="text-center">Select</br>All
                        </br><input type="checkbox" style="transform: scale(1.3)" id="select_all" onchange="selectAll()">
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($poItems as $key => $item):
                    $fullyReceived = ($item->order_qty == $item->received_qty);
                    $disabled = ($fullyReceived) ? 'disabled' : '';
                    ?>
                    <tr>
                <input type="hidden" name="receive[<?= $key ?>][<?= $item->id ?>][id]" class="form-control form-control-sm" value="<?= $item->id ?>" />
                <td class="text-center"><?= $key + 1 ?></td>
                <td><?= Html::encode($item->inventoryDetail->code) ?></td>
                <td>
                    <?= Html::encode($item->brand->name) ?>, 
                    <?= Html::encode($item->model_description) ?>
                    <br>
                    MODEL: <?= Html::encode($item->model_type) ?>
                </td>
                <td class="text-right"><?= number_format($item->unit_price, 2) ?></td>
                <td class="text-right"><?= number_format($item->discount_amt, 2) ?></td>
                <td class="text-right"><?= number_format($item->total_price, 2) ?></td>
                <td class="text-center"><?= $item->order_qty ?? 0 ?></td>
                <td class="text-center"><?= $item->received_qty ?? 0 ?></td>
                <td class="text-center"><?= $item->remaining_qty ?? 0 ?></td>
                <td>                                        
                    <input type="number" name="receive[<?= $key ?>][<?= $item->id ?>][new_receive_qty]" class="form-control form-control-sm qty-input" placeholder="<?= $fullyReceived ? '' : 'Enter quantity' ?>" max="<?= $fullyReceived ? '' : ($item->remaining_qty) ?>" min="0" <?= $disabled ?>/>
                </td>
                <td class="text-center">
                    <?php if (!$fullyReceived): ?>
                        <input type="checkbox" style="transform: scale(1.3)", class="select-row" data-key="<?= $key ?>" data-item-id="<?= $item->id ?>" data-max-qty="<?= $item->remaining_qty ?>" onchange="fillQuantity(this)">
                    <?php endif; ?>
                </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    </div>
    <?php ActiveForm::end(); ?>
</div> 

<script>
    function fillQuantity(checkbox) {
        const row = checkbox.closest('tr');
        const qtyInput = row.querySelector('.qty-input');
        const maxQty = parseInt(checkbox.getAttribute('data-max-qty'));

        if (checkbox.checked) {
            qtyInput.value = maxQty;
        } else {
            qtyInput.value = '';
        }
    }

    function selectAll() {
        const masterCheckbox = document.getElementById('select_all');
        const checkboxes = document.querySelectorAll('.select-row');

        checkboxes.forEach((checkbox) => {
            checkbox.checked = masterCheckbox.checked;
            fillQuantity(checkbox);
        });
    }

    function validateAndSubmit() {
        const checkboxes = document.querySelectorAll('.select-row');
        let hasChecked = false;
        let hasInvalidQty = false;
        let invalidItems = [];

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                hasChecked = true;
                const row = checkbox.closest('tr');
                const qtyInput = row.querySelector('.qty-input');
                const qty = parseFloat(qtyInput.value) || 0;
                const itemNo = row.querySelector('td:nth-child(2)').textContent.trim();

                if (qty <= 0) {
                    hasInvalidQty = true;
                    invalidItems.push(itemNo);
                }
            }
        });

        // Validate at least one item is checked
        if (!hasChecked) {
            alert('Please select at least one item to receive.');
            return false;
        }

        // Validate all checked items have quantity greater than 0
        if (hasInvalidQty) {
            alert('The following selected items must have a receive quantity greater than 0:\n\n' + invalidItems.join('\n'));
            return false;
        }

        // Remove unchecked items from submission
        checkboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                const row = checkbox.closest('tr');
                const inputs = row.querySelectorAll('input[name^="receive"]');
                inputs.forEach(input => input.removeAttribute('name'));
            }
        });

        // Submit the form
        document.getElementById('myForm').submit();
    }

    // Optional: Real-time validation feedback
    document.addEventListener('DOMContentLoaded', function () {
        const qtyInputs = document.querySelectorAll('.qty-input');

        qtyInputs.forEach(input => {
            input.addEventListener('input', function () {
                const qty = parseFloat(this.value) || 0;
                const max = parseFloat(this.getAttribute('max')) || 0;

                if (qty > max) {
                    this.value = max;
                }

                if (qty < 0) {
                    this.value = 0;
                }
            });
        });
    });
</script>
