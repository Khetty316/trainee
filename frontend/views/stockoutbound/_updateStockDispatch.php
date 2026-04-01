<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$this->title = 'Stock Dispatch';
$production = $bomMaster->productionPanel->projProdMaster;
$this->params['breadcrumbs'][] = ['label' => 'Stock Outbound', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $production->project_production_code, 'url' => ['view-panels', 'id' => $production->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="stock-dispatch mb-5">
    <?php
    $form = ActiveForm::begin([
        'id' => 'myForm',
        'method' => 'post',
    ]);
    ?>
    <h4>
        Updates Stock Dispatch: <?= $bomMaster->productionPanel->project_production_panel_code ?>
        <?php
        $showButton = false;
        foreach ($stockMasters as $stockMaster) {
            foreach ($stockMaster->stockOutboundDetails as $detail) {
                if ($detail->fully_dispatch_status == 0) {
                    $showButton = true;
                    break 2;
                }
            }
        }
        ?>
        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal])) { ?>
            <div class="row mt-3">
                <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
                    <h5 for="receiver" class="mb-0 pr-3 text-nowrap">Received By: </h5>
                    <div class="w-100">
                        <select name="receiver[id]" id="receiver" class="form-control form-control-sm <?= empty($receivers) ? 'is-invalid' : '' ?>">
                            <?php if (!empty($receivers)): ?>
                                <?php foreach ($receivers as $key => $receiver): ?>
                                    <option value="<?= $receiver['id'] ?>"><?= $receiver['fullname'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (empty($receivers)): ?>
                            <small class="invalid-feedback" style="font-size: 10pt">No staff available to select.</small>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 d-flex align-items-center mb-2">
                    <h5 class="mb-0 pr-3 text-nowrap">Status: </h5>
                    <div class="w-100">
                        <select name="current_sts" id="current_sts" class="form-control form-control-sm">
                            <?php foreach (\frontend\models\bom\StockDispatchMaster::pending_status as $value => $label) : ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12">
                    <?php if ($showButton): ?>
                        <?= Html::submitButton('Proceed', ['class' => 'btn btn-primary px-3 float-right proceed']) ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php } ?>
    </h4>

    <?php foreach ($stockMasters as $keyMaster => $master): ?>
        <div class="table-responsive">
            <div class="card mt-2 bg-light">
                <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                     id="heading_<?= $keyMaster ?>" 
                     data-toggle="collapse" 
                     data-target="#collapse_<?= $keyMaster ?>" 
                     aria-expanded="true" 
                     aria-controls="collapse_<?= $keyMaster ?>">
                    <span class="p-0 m-0 accordionHeader">
                        #<?= $master->order ?>
                    </span>
                </div>

                <div id="collapse_<?= $keyMaster ?>" 
                     class="collapse <?= $keyMaster === 0 ? 'show' : '' ?>" 
                     aria-labelledby="heading_<?= $keyMaster ?>">
                    <div class="card-body p-1" style="background-color:white">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Model Type</th>
                                    <th>Brand</th>
                                    <th>Description</th>
                                    <th>Total Quantity</th>
                                    <th>Dispatched Quantity</th>
                                    <th>Unacknowledged Quantity</th>
                                    <th>Available Qty</th>
                                    <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal])) { ?>
                                        <th>Dispatch Quantity</th>
                                        <th>Remark</th>
                                        <th class="text-center">
                                            <input type="checkbox" id="select_all_<?= $keyMaster ?>" onclick="selectAll(<?= $keyMaster ?>)">
                                        </th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($master->stockOutboundDetails as $keyDetail => $detail): ?>
                                    <tr style="<?= ($detail->active_sts == 0) ? 'text-decoration: line-through; color: red;' : '' ?>">
                                <input type="hidden" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][id]" class="form-control form-control-sm" value="<?= $detail->id ?>" />
                                <input type="hidden" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][model_type]" class="form-control form-control-sm" value="<?= $detail->model_type ?>" />
                                <input type="hidden" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][brand]" class="form-control form-control-sm" value="<?= $detail->brand ?>" />
                                <input type="hidden" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][descriptions]" class="form-control form-control-sm" value="<?= $detail->descriptions ?>" />
                                <input type="hidden" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][qty]" class="form-control form-control-sm" value="<?= $detail->qty ?>" />
                                <input type="hidden" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][dispatched_qty]" class="form-control form-control-sm" value="<?= $detail->dispatched_qty ?>" />
                                <td class="text-center"><?= $keyDetail + 1 ?></td>
                                <td><?= $detail->model_type ?></td>
                                <td><?= $detail->brand ?></td>
                                <td><?= $detail->descriptions ?></td>
                                <td><?= $detail->qty ?></td>
                                <td><?= ($detail->dispatched_qty === null) ? 0 : $detail->dispatched_qty ?></td>
                                <td><?= ($detail->unacknowledged_qty === null) ? 0 : $detail->unacknowledged_qty ?></td>
                                <td><?= ($detail->qty_stock_available === null) ? 0 : $detail->qty_stock_available ?></td>
                                <?php
                                $isFullyDispatched = ($detail->fully_dispatch_status === 1);
                                $disabled = ($isFullyDispatched || $detail->active_sts == 0) ? 'disabled' : '';
                                ?>
                                <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal])) { ?>
                                    <td>
                                        <input type="number" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][dispatch_qty]" class="form-control form-control-sm qty-input" placeholder="<?= $isFullyDispatched ? '' : 'Enter quantity' ?>" max="<?= $isFullyDispatched ? '' : ($detail->qty) - ($detail->unacknowledged_qty + $detail->dispatched_qty) ?>" min="0" <?= $disabled ?>/>
                                    </td>
                                    <td>
                                        <input type="text" name="dispatch[<?= $keyMaster ?>][<?= $detail->id ?>][remark]" class="form-control form-control-sm" placeholder="<?= $isFullyDispatched ? '' : 'Remark' ?>" <?= $disabled ?>/>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!$isFullyDispatched && $detail->active_sts == 1) { ?>
                                                        <!--<input type="checkbox" class="select-row" data-master="<?php //= $keyMaster       ?>" data-max-qty="<?php //= ($detail->qty) - ($detail->unacknowledged_qty + $detail->dispatched_qty)       ?>" onclick="fillQuantity(this)">-->
                                            <?php
                                            if ($detail->qty_stock_available === null && $detail->inventory_model_id === null) {
                                                $stockAvailable = ($detail->qty) - ($detail->unacknowledged_qty + $detail->dispatched_qty);
                                            } else {
                                                $stockAvailable = $detail->qty_stock_available;
                                            }
                                            ?>
                                            <input type="checkbox" class="select-row" data-master="<?= $keyMaster ?>" data-max-qty="<?= ($stockAvailable ?? 0) ?>" onclick="fillQuantity(this)">
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php ActiveForm::end(); ?>
</div> 

<script>
    $('.proceed').click(function (e) {
        const receiverSelect = document.getElementById('receiver');
        if (!receiverSelect.value) {
            receiverSelect.classList.add('is-invalid');
            e.preventDefault();
        } else {
            receiverSelect.classList.remove('is-invalid');
        }
    });

    function fillQuantity(checkbox) {
        const row = checkbox.closest('tr');
        const qtyInput = row.querySelector('.qty-input');
        const maxQty = checkbox.getAttribute('data-max-qty');

        if (checkbox.checked) {
            qtyInput.value = maxQty;
        } else {
            qtyInput.value = '';
        }
    }

    function selectAll(masterId) {
        const masterCheckbox = document.getElementById('select_all_' + masterId);
        const checkboxes = document.querySelectorAll(`.select-row[data-master="${masterId}"]`);

        checkboxes.forEach((checkbox) => {
            checkbox.checked = masterCheckbox.checked;
            fillQuantity(checkbox);
        });
    }

//    document.getElementById('myForm').addEventListener('submit', function (e) {
//        // Find all checkboxes in the form
//        const checkboxes = document.querySelectorAll('.select-row');
//
//        checkboxes.forEach((checkbox) => {
//            const row = checkbox.closest('tr');
//            const qtyInput = row.querySelector('.qty-input');
//            const remarkInput = row.querySelector('input[name^="dispatch"][type="text"]');
//
//            if (!checkbox.checked) {
//                // Remove 'name' attribute for unchecked rows
//                qtyInput.removeAttribute('name');
//                remarkInput.removeAttribute('name');
//            }
//        });
//    });

    document.getElementById('myForm').addEventListener('submit', function (e) {
        const checkboxes = document.querySelectorAll('.select-row');

        checkboxes.forEach((checkbox) => {
            if (!checkbox.checked) {
                const row = checkbox.closest('tr');
                // Remove all inputs with name attribute containing this detail ID
                const inputs = row.querySelectorAll('input[name^="dispatch"]');
                inputs.forEach(input => input.removeAttribute('name'));
            }
        });
    });

</script>
