<?php

use yii\helpers\Html;
use common\models\User;
use common\models\myTools\MyFormatter;
use frontend\models\RefGeneralStatus;
?>
<td class="text-center"><?= $model->quantity_approved ?></td>
<td class="text-center"><?= $model->currency_approved ?></td>
<td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->unit_price_approved) ?></td>
<td class="text-right"><?= \common\models\myTools\MyFormatter::asDecimal2($model->total_price_approved) ?></td>
<td>
    <div class="decision-result">
        <?php if ($worklist->status == RefGeneralStatus::STATUS_Approved): ?>
            <span class="text-success">Approved</span><br>
            <?php
            $responder = User::findOne($worklist->responded_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
            <?php endif; ?>
            @ <?= MyFormatter::asDateTime_ReaddmYHi($worklist->created_at) ?>

        <?php elseif ($worklist->status == RefGeneralStatus::STATUS_SuperiorRejected): ?>
            <!--Rejected Display--> 
            <span class="text-danger">Rejected</span><br>
            <?php
            $responder = User::findOne($worklist->responded_by);
            if ($responder):
                ?>
                by <?= Html::encode($responder->fullname) ?>
                @ <?= MyFormatter::asDateTime_ReaddmYHi($worklist->created_at) ?>
                <br>
                <small class="text-danger">
                    <strong>Reject Reason:</strong><br>
                    <?= Html::encode($worklist->remark) ?>
                </small>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</td>
