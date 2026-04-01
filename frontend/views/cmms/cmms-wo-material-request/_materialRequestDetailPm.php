<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

\yii\web\YiiAsset::register($this);
?>
<style>
    .badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }

    .finalize-checkbox,
    .delete-checkbox,
    .select-all-checkbox {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .finalize-checkbox:hover,
    .delete-checkbox:hover,
    .select-all-checkbox:hover {
        transform: scale(1.2);
    }

    .select-all-checkbox {
        margin: 0 3px;
        vertical-align: middle;
    }
</style>

<div class="cmms-wo-material-request-view mb-5">

    <h4>Work Order #<?= Html::encode($model->id) ?> - Material Requests</h4>

    <!-- Action Buttons -->
    <?php if ($moduleIndex === 'superior'): ?>
        <div>
            <?=
            Html::a("Add Material", "javascript:void(0)", [
                'title' => "Add Material",
                'value' => Url::to(['/cmms/cmms-wo-material-request/create', 'woId' => $model->id, 'faultId' => null, 'wotype' => $wotype]),
                'class' => 'modalButton btn btn-sm btn-success ml-1',
                'data-modaltitle' => 'Add Material',
                'id' => 'AddMaterialBtn',
            ]);
            ?>

            <?=
            Html::button("Finalize Selected Material", [
                'class' => 'btn btn-sm btn-primary ml-1',
                'id' => 'finalizeSelectedBtn',
            ]);
            ?>

            <?=
            Html::button("Delete Selected", [
                'class' => 'btn btn-sm btn-danger float-right',
                'id' => 'deleteSelectedBtn',
            ]);
            ?>
        </div>
    <?php endif; ?>

    <table class="table table-sm table-bordered mb-0 mt-2">
        <thead>
            <tr>
                <th rowspan="2">#</th>
                <th rowspan="2">Part/Tool</th>
                <th rowspan="2">Model Type</th>
                <th rowspan="2">Brand</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Quantity</th>
                <th rowspan="2">Remark</th>
                <th rowspan="2" class="text-center">
                    Finalize
                    <?php if ($moduleIndex === 'superior'): ?>
                        <br><input type="checkbox" id="select-all-finalize" class="select-all-checkbox ml-1" title="Select all">
                    <?php endif; ?>
                </th>
                <?php if ($moduleIndex === 'superior'): ?>
                    <th rowspan="2" class="text-center">
                        Delete
                        <br><input type="checkbox" id="select-all-delete" class="select-all-checkbox ml-1" title="Select all">
                    </th>
                <?php endif; ?>
                <th colspan="<?= (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_CMMS_Superior]) && $moduleIndex === 'inventory') ? 6 : 3 ?>" class="text-center">Inventory</th>
            </tr>
            <tr>
                <th class="text-center">Dispatched Qty</th>
                <th class="text-center">Unacknowledged Qty</th>
                <th class="text-center">Available Qty</th>
                <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_CMMS_Superior]) && $moduleIndex === 'inventory'): ?>
                    <th>Dispatch Quantity</th>
                    <th>Remark</th>
                    <th class="text-center">
                        <input type="checkbox" class="select-all-checkbox" id="select_all" onclick="selectAll()">
                    </th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($materialDetails)): ?>
                <?php foreach ($materialDetails as $detailIndex => $detail): ?>
                    <?php $isInactive = ($detail->active_sts == 0); ?>
                    <tr style="<?= $isInactive ? 'text-decoration: line-through; color: red;' : '' ?>">

                        <td><?= $detailIndex + 1 ?></td>
                        <td><?= $detail->part_or_tool == 1 ? "Part" : "Tool" ?></td>
                        <td>
                            <?php
                            if ($isInactive) {
                                echo Html::encode($detail->model_type);
                            } elseif (
                                    $moduleIndex === 'superior' &&
                                    $detail->is_finalized == 1 &&
                                    ($detail->inventory_sts == 2 || $detail->inventory_sts == 0)
                            ) {
                                echo Html::a($detail->model_type, "javascript:void(0)", [
                                    'title' => 'Edit',
                                    'value' => Url::to(['/cmms/cmms-wo-material-request/update', 'id' => $detail->id, 'wotype' => $wotype]),
                                    'class' => 'modalButton',
                                    'data-modaltitle' => 'Edit Material',
                                ]);
                            } else {
                                echo Html::encode($detail->model_type);
                            }
                            ?>
                        </td>

                        <td><?= Html::encode($detail->brand) ?></td>
                        <td><?= Html::encode($detail->descriptions) ?></td>
                        <td>
                            <?php
                            $editQtyIcon = "";
                            if ($detail->is_finalized == 2 && ($moduleIndex === "superior" || $moduleIndex === "inventory")) {
                                $editQtyIcon = Html::a(
                                        "<i class='far fa-edit ml-2 float-right'></i>",
                                        "javascript:",
                                        [
                                            'title' => "Change Qty",
                                            "value" => yii\helpers\Url::to(['/cmms/cmms-wo-material-request/change-qty-finalized-item', 'detailId' => $detail->id]),
                                            "class" => "modalButtonMedium",
                                            'data-modaltitle' => "Change Qty"
                                        ]
                                );
                            }
                            ?>
                            <?= Html::encode($detail->qty) . $editQtyIcon ?>
                        </td>
                        <td><?= Html::encode($detail->remark) ?></td>

                        <!-- Finalize -->
                        <td class="text-center">
                            <?php if ($isInactive): ?>
                                <span class="text-muted">-</span>
                            <?php else: ?>
                                <?php
                                $canFinalize = ($moduleIndex === 'superior');
                                if ($detail->is_finalized == 2) {
                                    echo '<span class="badge badge-success">Finalized</span>';
                                } elseif ($detail->is_finalized == 3) {
                                    echo '<span class="badge badge-secondary">Outbound</span>';
                                } elseif ($detail->is_finalized == 1) {
                                    if ($canFinalize && $detail->inventory_sts == 2 && $detail->active_sts == 1) {
                                        echo '<span class="badge badge-warning">Pending</span>';
                                        if ($moduleIndex === 'superior') {
                                            echo Html::checkbox('finalize_items[]', false, [
                                                'value' => $detail->id,
                                                'class' => 'finalize-checkbox ml-1',
                                            ]);
                                        }
                                    } else {
                                        echo '<span class="badge badge-warning">Pending</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            <?php endif; ?>
                        </td>

                        <!-- Delete -->
                        <?php if ($moduleIndex === 'superior'): ?>
                            <td class="text-center">
                                <?php if ($isInactive): ?>
                                    <span class="text-muted">-</span>
                                    <?php
                                else:
                                    if ($detail->is_finalized == 1) :
                                        ?>
                                        <?=
                                        Html::checkbox('delete_items[]', false, [
                                            'value' => $detail->id,
                                            'class' => 'delete-checkbox',
                                        ])
                                        ?>
                                        <?php
                                    endif;
                                endif;
                                ?>
                            </td>
                        <?php endif; ?>
                        <td class="text-center">
                            <?= $isInactive ? '<span class="text-muted">-</span>' : Html::encode($detail->dispatched_qty ?? '-') ?>
                        </td>

                        <td class="text-center">
                            <?= $isInactive ? '<span class="text-muted">-</span>' : Html::encode($detail->unacknowledged_qty ?? '-') ?>
                        </td>

                        <td class="text-center">
                            <?php
                            $stockAvailable = $detail->qty_stock_available ?? 0;

                            $showPurchasing = (
                                    $detail->is_finalized == 2 &&
                                    ($detail->qty_stock_available === null || $detail->qty_stock_available == 0) &&
                                    $detail->fully_dispatch_status !== 1 &&
                                    ($detail->dispatched_qty === null || $detail->dispatched_qty == 0)
                            );
                            ?>

                            <?php if ($isInactive): ?>
                                <span class="text-muted">-</span>
                            <?php elseif ($showPurchasing): ?>
                                <span class="badge badge-info">Purchasing in Progress</span>
                            <?php else: ?>
                                <?= Html::encode($stockAvailable) ?>
                            <?php endif; ?>
                        </td>

                        <?php
                        $isFullyDispatched = ($detail->fully_dispatch_status === 1);
                        $hasPendingDispatch = !empty($detail->unacknowledged_qty) && $detail->unacknowledged_qty != 0;

                        $disabled = ($isFullyDispatched || $hasPendingDispatch || $detail->active_sts == 0) ? 'disabled' : '';
                        ?>

                        <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal, AuthItem::ROLE_CMMS_Superior]) && $moduleIndex === 'inventory') { ?>

                            <td>
                                <input type="number"
                                       name="dispatch[<?= $detailIndex ?>][<?= $detail->id ?>][dispatch_qty]"
                                       class="form-control form-control-sm qty-input"
                                       placeholder="<?= $isFullyDispatched ? '' : 'Enter quantity' ?>"
                                       max="<?= $isFullyDispatched ? '' : ($detail->qty) - ($detail->unacknowledged_qty + $detail->dispatched_qty) ?>"
                                       min="0"
                                       <?= $disabled ?> />
                            </td>

                            <td>
                                <input type="text"
                                       name="dispatch[<?= $detailIndex ?>][<?= $detail->id ?>][remark]"
                                       class="form-control form-control-sm"
                                       placeholder="<?= $isFullyDispatched ? '' : 'Remark' ?>"
                                       <?= $disabled ?> />
                            </td>

                            <td class="text-center">
                                <?php if (!$isFullyDispatched && !$hasPendingDispatch && $detail->active_sts == 1 && $detail->is_finalized == 2) { ?>
                                    <?php $stockAvailable = $detail->qty_stock_available; ?>
                                    <input type="checkbox"
                                           class="select-row select-all-checkbox"
                                           data-master="<?= $detailIndex ?>"
                                           data-max-qty="<?= $stockAvailable ?? 0 ?>"
                                           onclick="fillQuantity(this)">
                                       <?php } ?>
                            </td>

                        <?php } ?>

                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-muted" style="padding: 20px;">
                        <i class="glyphicon glyphicon-info-sign"></i>
                        <?php if ($moduleIndex === 'superior'): ?>
                            No materials requested yet. Click <strong>Add Material</strong> to begin.
                        <?php else: ?>
                            No materials requested yet.
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    (function () {
        var $finalizeBtn = $('#finalizeSelectedBtn');
        var $deleteBtn = $('#deleteSelectedBtn');

        // Select all — finalize
        $('#select-all-finalize').on('change', function () {
            $('.finalize-checkbox').prop('checked', this.checked);
            updateFinalizeButton();
        });

        // Select all — delete
        $('#select-all-delete').on('change', function () {
            $('.delete-checkbox').prop('checked', this.checked);
            updateDeleteButton();
        });

        $(document).on('change', '.finalize-checkbox', function () {
            syncSelectAll('#select-all-finalize', '.finalize-checkbox');
            updateFinalizeButton();
        });
        $(document).on('change', '.delete-checkbox', function () {
            syncSelectAll('#select-all-delete', '.delete-checkbox');
            updateDeleteButton();
        });

        function syncSelectAll(headerSel, itemSel) {
            var total = $(itemSel).length, checked = $(itemSel + ':checked').length;
            if (total > 0) {
                $(headerSel)
                        .prop('checked', checked === total)
                        .prop('indeterminate', checked > 0 && checked < total);
            }
        }

        function updateFinalizeButton() {
            var n = $('.finalize-checkbox:checked').length;
            $finalizeBtn.text(n > 0 ? 'Finalize Selected Material (' + n + ')' : 'Finalize Selected Material');
        }

        function updateDeleteButton() {
            var n = $('.delete-checkbox:checked').length;
            $deleteBtn.text(n > 0 ? 'Delete Selected (' + n + ')' : 'Delete Selected');
        }

        $finalizeBtn.on('click', function () {
            var ids = [];
            $('.finalize-checkbox:checked').each(function () {
                ids.push($(this).val());
            });
            if (!ids.length) {
                alert('Please select at least one item to finalize.');
                return;
            }
            if (confirm('Finalize ' + ids.length + ' selected material(s)?')) {
                $.ajax({
                    url: '<?= Url::to(['/cmms/cmms-wo-material-request/finalize-selected-material']) ?>',
                    type: 'POST',
                    data: {
                        woId: <?= $model->id ?>,
                        woType: '<?= $wotype ?>',
                        'ids[]': ids
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                    },
                }).done(function (res) {
                    res.success ? location.reload() : alert('Error: ' + (res.message || 'Failed to finalize.'));
                }).fail(function (xhr) {
                    alert('Server error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                });
            }
        });

        $deleteBtn.on('click', function () {
            var ids = [];
            $('.delete-checkbox:checked').each(function () {
                ids.push($(this).val());
            });
            if (!ids.length) {
                alert('Please select at least one item to delete.');
                return;
            }
            if (confirm('Are you sure you want to delete ' + ids.length + ' selected item(s)?')) {
                $.ajax({
                    url: '<?= Url::to(['/cmms/cmms-wo-material-request/delete-multiple']) ?>',
                    type: 'POST',
                    data: {
                        woId: <?= $model->id ?>,
                        woType: '<?= $wotype ?>',
                        'ids[]': ids
                    },
                    beforeSend: function (xhr) {
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                    },
                }).done(function (res) {
                    res.success ? location.reload() : alert('Error: ' + (res.message || 'Failed to delete.'));
                }).fail(function (xhr) {
                    alert('Server error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Unknown error'));
                });
            }
        });

    }());
</script>