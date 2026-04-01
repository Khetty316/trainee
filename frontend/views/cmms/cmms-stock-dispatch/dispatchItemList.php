<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\cmms\CmmsWoMaterialRequestDetails;
use common\models\User;
use frontend\models\bom\StockDispatchMaster;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<div>
    <div class="row mt-3 mb-3">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="d-flex justify-content-left align-items-center">
                <h6 class="mb-0 mr-2 text-nowrap">Received By: </h6>
                <span><?= $dispatchMaster->received_by ?></span>
                <?php
                if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal]) && $moduleIndex === "superuser") {
                    if (!empty($pendingDispatch) || !empty($pendingAdjust) || !empty($pendingReturn)) {
                        ?>
                        <?=
                        Html::a(
                                "<i class='far fa-edit ml-2'></i>",
                                "javascript:",
                                [
                                    'title' => "Change Receiver",
                                    "value" => yii\helpers\Url::to(['change-receiver', 'dispatchId' => $dispatchMaster->dispatch_id]),
                                    "class" => "modalButtonMedium",
                                    'data-modaltitle' => "Change Receiver"
                                ]
                        );
                        ?>
                    <?php } else { ?>
                        <i class='far fa-edit ml-2'></i>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php if (!empty($pendingDispatch)) { ?>
        <fieldset class="border p-1">
            <legend class="w-auto px-2 m-0">Pending Items: </legend>
            <?php
            ActiveForm::begin([
                'id' => 'myForm',
                'method' => 'post',
                'action' => yii\helpers\Url::to(['dispatch-acknowledgement', 'productionPanelId' => $dispatchMaster->wo_id, 'dispatchId' => $dispatchMaster->dispatch_id]),
            ]);

            if ($status !== null && $moduleIndex !== "superuser") {
                echo Html::submitButton('Confirm All <i class="fa fa-check"></i>', ['class' => 'btn btn-success px-3 float-right proceed']);
            }
            ?>
            <div class="table-responsive">
                <div class="card mt-2 bg-light">
                    <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                         id="heading_dispatch" 
                         data-toggle="collapse" 
                         data-target="#collapse_dispatch" 
                         aria-expanded="true" 
                         aria-controls="collapse_dispatch">
                        <span class="p-0 m-0 accordionHeader">Dispatch</span>
                    </div>
                    <div id="collapse_dispatch" class="collapse show" aria-labelledby="heading_dispatch">
                        <div class="card-body p-1" style="background-color:white">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Model Type</th>
                                        <th>Brand</th>
                                        <th>Description</th>
                                        <th>Dispatch Quantity</th>
                                        <th>Remark</th>
                                        <th>Dispatch By</th>
                                        <th>Status</th>
                                        <th>Status Updated At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingDispatch as $key => $dispatch): ?>
                                        <?php
                                        $detail = CmmsWoMaterialRequestDetails::findOne($dispatch['request_detail_id']);
                                        $createdBy = User::findOne($dispatch['trial_created_by']);
                                        ?>
                                        <tr style="<?= ($detail->active_sts == 0) ? 'text-decoration: line-through; color: red;' : '' ?>">
                                            <td class="text-center"><?= $key + 1 ?></td> 
                                            <td><?= $detail->model_type ?></td>
                                            <td><?= $detail->brand ?></td>
                                            <td><?= $detail->descriptions ?></td>
                                            <td><?= $dispatch['dispatch_qty'] ?></td>
                                            <td><?= $dispatch['remark'] ?></td>
                                            <td><?= $createdBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($dispatch['trial_created_at']) ?></td>
                                            <td><span class="text-warning"><?= ($dispatch['current_sts'] == StockDispatchMaster::TO_BE_COLLECTED ? 'To Be Collected' : 'To Be Acknowledged') ?></span></td>
                                            <td><?= MyFormatter::asDateTime_ReaddmYHi($dispatch['status_updated_at']) ?></td> 
                                            <?php if ($status !== null && $detail->active_sts == 1) { ?>
                                                <?= Html::hiddenInput("dispatch[{$dispatch['trial_id']}][detailId]", $dispatch['request_detail_id']) ?>
                                            <?php } ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <?php ActiveForm::end(); ?>
    <?php } else if (!empty($pendingAdjust) || !empty($pendingReturn)) { ?>
        <fieldset class="border p-1">
            <legend class="w-auto px-2 m-0">Pending Items:</legend>
            <?php
            ActiveForm::begin([
                'id' => 'myForm',
                'method' => 'post',
                'action' => yii\helpers\Url::to(['adjust-and-return-acknowledgement', 'productionPanelId' => $dispatchMaster->wo_id, 'dispatchId' => $dispatchMaster->dispatch_id]),
            ]);

            if ($status !== null && $moduleIndex !== "superuser") {
                echo Html::submitButton('Save', ['class' => 'btn btn-success px-3 float-right proceed']);
            }
            ?>
            <div class="table-responsive">
                <!-- Adjustment Table -->
<!--                <div class="card mt-2 bg-light">
                    <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                         id="heading_adjust" 
                         data-toggle="collapse" 
                         data-target="#collapse_adjust" 
                         aria-expanded="true" 
                         aria-controls="collapse_adjust">
                        <span class="p-0 m-0 accordionHeader">Adjustment</span>
                    </div>
                    <div id="collapse_adjust" class="collapse show" aria-labelledby="heading_adjust">
                        <div class="card-body p-1" style="background-color:white">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Model Type</th>
                                        <th>Brand</th>
                                        <th>Description</th>
                                        <th>Adjusted Quantity</th>
                                        <th>Remark</th>
                                        <th>Adjusted By</th>
                                        <th>Status</th>
                                        <th>Status Updated At</th>
                                        <?php // if ($status !== null && $moduleIndex !== "superuser") { ?>
                                            <th class="text-center">
                                                <input type="checkbox" id="select_all_adjust" onclick="selectAll('adjust')">
                                            </th>
                                        <?php // } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
//                                    if (!empty($pendingAdjust)) {
//                                        foreach ($pendingAdjust as $key => $adjust):
//                                            $detail = CmmsWoMaterialRequestDetails::findOne($adjust['request_detail_id']);
//                                            $createdBy = User::findOne($adjust['trial_created_by']);
                                            ?>
                                            <tr style="<?php //= ($detail->active_sts == 0) ? 'text-decoration: line-through; color: red;' : '' ?>">
                                                <td class="text-center"><?php //= $key + 1 ?></td>
                                                <td><?php //= $detail->model_type ?></td>
                                                <td><?php //= $detail->brand ?></td>
                                                <td><?php //= $detail->descriptions ?></td>
                                                <td><?php //= $adjust['dispatch_qty'] ?></td>
                                                <td><?php //= $adjust['remark'] ?></td>
                                                <td><?php //= $createdBy->fullname ?> @ <?php //= MyFormatter::asDateTime_ReaddmYHi($adjust['trial_created_at']) ?></td>
                                                <td><span class="text-warning"><?php //= ($adjust['current_sts'] == StockDispatchMaster::TO_BE_COLLECTED ? 'To Be Collected' : 'To Be Acknowledged') ?></span></td>
                                                <td><?php //= MyFormatter::asDateTime_ReaddmYHi($adjust['status_updated_at']) ?></td> 
                                                <?php // if ($status !== null && $detail->active_sts == 1) { ?>
                                                    <td class="text-center">
                                                        <?php // if ($adjust['current_sts'] == StockDispatchMaster::TO_BE_ACKNOWLEDGED) { ?>
                                                            <input type="checkbox" 
                                                                   class="select-row" 
                                                                   data-master="adjust" 
                                                                   data-id="<?php //= $adjust['trial_id'] ?>">
                                                               <?php // } ?>
                                                    </td>
                                                    <?php
//                                                    =
//                                                    Html::hiddenInput("adjust[{$adjust['trial_id']}][detailId]", $adjust['request_detail_id'], [
//                                                        'id' => "hidden_{$adjust['trial_id']}",
//                                                        'disabled' => true
//                                                    ])
                                                    ?>
                                                <?php // } ?>
                                            </tr>
                                            <?php
//                                        endforeach;
//                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="10"><div class="empty">No results found.</div></td>
                                        </tr>
                                    <?php // } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>-->

                <!-- Return Table -->
                <div class="card mt-2 bg-light">
                    <div class="p-1 pl-2 pr-2 m-0 card-header hoverItem border-dark btn-header-link" 
                         id="heading_return" 
                         data-toggle="collapse" 
                         data-target="#collapse_return" 
                         aria-expanded="true" 
                         aria-controls="collapse_return">
                        <span class="p-0 m-0 accordionHeader">Return</span>
                    </div>
                    <div id="collapse_return" class="collapse show" aria-labelledby="heading_return">
                        <div class="card-body p-1" style="background-color:white">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Model Type</th>
                                        <th>Brand</th>
                                        <th>Description</th>
                                        <th>Return Quantity</th>
                                        <th>Remark</th>
                                        <th>Dispatch By</th>
                                        <th>Status</th>
                                        <th>Status Updated At</th>
                                        <?php if ($status !== null) { ?>
                                            <th class="text-center">
                                                <input type="checkbox" id="select_all_return" onclick="selectAll('return')">
                                            </th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($pendingReturn)) {
                                        foreach ($pendingReturn as $key => $return):
                                            $detail = CmmsWoMaterialRequestDetails::findOne($return['request_detail_id']);
                                            $createdBy = User::findOne($return['trial_created_by']);
                                            ?>
                                            <tr style="<?= ($detail->active_sts == 0) ? 'text-decoration: line-through; color: red;' : '' ?>">
                                                <td class="text-center"><?= $key + 1 ?></td>
                                                <td><?= $detail->model_type ?></td>
                                                <td><?= $detail->brand ?></td>
                                                <td><?= $detail->descriptions ?></td>
                                                <td><?= $return['dispatch_qty'] ?></td>
                                                <td><?= $return['remark'] ?></td>
                                                <td><?= $createdBy->fullname ?> @ <?= MyFormatter::asDateTime_ReaddmYHi($return['trial_created_at']) ?></td>
                                                <td><span class="text-warning"><?= ($return['current_sts'] == StockDispatchMaster::TO_BE_COLLECTED ? 'To Be Collected' : 'To Be Acknowledged') ?></span></td>
                                                <td><?= MyFormatter::asDateTime_ReaddmYHi($return['status_updated_at']) ?></td> 
                                                <?php if ($status !== null && $detail->active_sts == 1) { ?>
                                                    <td class="text-center">
                                                        <?php if ($return['current_sts'] == StockDispatchMaster::TO_BE_ACKNOWLEDGED) { ?>
                                                            <input type="checkbox" 
                                                                   class="select-row" 
                                                                   data-master="return" 
                                                                   data-id="<?= $return['trial_id'] ?>">
                                                               <?php } ?>
                                                    </td>
                                                    <?=
                                                    Html::hiddenInput("return[{$return['trial_id']}][detailId]", $return['request_detail_id'], [
                                                        'id' => "hidden_{$return['trial_id']}",
                                                        'disabled' => true
                                                    ])
                                                    ?>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        endforeach;
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="10"><div class="empty">No results found.</div></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="border p-1 mt-3">
            <legend class="w-auto px-2 m-0">Acknowledged Items:</legend>
            <div class="table-responsive">
                <?=
                $this->render('dispatchItemConfirmed', [
                    'confirmedDispatch' => $confirmedDispatch ?? null,
                    'confirmedAdjust' => $confirmedAdjust ?? null,
                    'confirmedReturn' => $confirmedReturn ?? null
                ]);
                ?>
            </div>
        </fieldset>
        <?php ActiveForm::end(); ?>
    <?php } else { ?>
        <fieldset class="border p-1">
            <legend class="w-auto px-2 m-0">Acknowledged Items:</legend>
            <div class="table-responsive">
                <?=
                $this->render('dispatchItemConfirmed', [
                    'confirmedDispatch' => $confirmedDispatch ?? null,
                    'confirmedAdjust' => $confirmedAdjust ?? null,
                    'confirmedReturn' => $confirmedReturn ?? null
                ]);
                ?>
            </div>
        </fieldset>
    <?php } ?>
</div>

<script>
    function selectAll(tableType) {
        const checkboxes = document.querySelectorAll(`.select-row[data-master="${tableType}"]`);
        const selectAllCheckbox = document.getElementById(`select_all_${tableType}`);
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            toggleHiddenInput(checkbox);
        });
    }

    function toggleHiddenInput(checkbox) {
        const hiddenInput = document.getElementById(`hidden_${checkbox.dataset.id}`);
        if (checkbox.checked) {
            hiddenInput.disabled = false;
        } else {
            hiddenInput.disabled = true;
        }
    }

    document.querySelectorAll('.select-row').forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            toggleHiddenInput(this);
        });
    });

    $('.proceed').click(function (e) {
        const userConfirmation = confirm('Are you sure you want to proceed?');
        if (!userConfirmation) {
            e.preventDefault();
        }
    });
</script>


