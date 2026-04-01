<?php

use yii\helpers\Html;
?>

<div class="inventory-order-request-allocation">
    <?php foreach ($details as $data): ?>
        <div class="card mb-3">
            <div class="card-header">
                <strong>PO No. : <?= Html::encode($data['allocation']->inventoryPoItem->inventoryPo->po_no) ?></strong>
            </div>

            <div class="card-body p-0">

                <table class="table table-bordered table-sm m-0">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Received By</th>
                            <th>Received Date</th>
                            <th class="text-center">Received Qty</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (!empty($data['receives'])): ?>
                            <?php
                            $index = 1;
                            foreach ($data['receives'] as $receive):
                                ?>

                                <tr>
                                    <td class="text-center"><?= $index++ ?></td>

                                    <td><?= Html::encode($receive->receivedBy->fullname ?? '-') ?></td>
                                    <td><?= common\models\myTools\MyFormatter::asDateTime_ReaddmYHi($receive->received_at) ?></td>
                                    <td class="text-center"><?= Html::encode($receive->allocated_qty ?? 0) ?></td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    No Receive Allocation
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>
        </div>
    <?php endforeach; ?>
</div>