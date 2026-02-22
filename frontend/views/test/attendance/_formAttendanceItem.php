<?php

use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
?>
<tr class="p-0 m-0" id='tr_<?= $key ?>'>
    <td>
        <?= Html::textInput("testDetailAttendance[$key][attendeeId]", $attendance->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("testDetailAttendance[$key][toDelete]", $attendance->id, ['class' => 'hidden', 'id' => "toDelete-$key"]) ?>
        <?= Html::textarea("testDetailAttendance[$key][attendeeName]", $attendance->name, ['class' => 'form-control autocomplete-input']) ?>
    </td>
    <td> 
        <?= Html::textarea("testDetailAttendance[$key][attendeeOrg]", $attendance->org, ['class' => 'form-control']) ?>
    </td>
    <td> 
        <?= Html::textarea("testDetailAttendance[$key][attendeeDesign]", $attendance->designation, ['class' => 'form-control']) ?>
    </td>
    <td> 
        <?= Html::textarea("testDetailAttendance[$key][attendeeRole]", $attendance->role, ['class' => 'form-control']) ?>
    </td>
    <td> 
        <?php if ($attendance->signature): ?>
            <div class="sign-block">
                <div style="margin-left: 5px;"><img src="<?= $attendance->signature ?>" id="signature-image-<?= $key ?>" alt="Image" style="margin-right: 70px;"></div>
            </div>
        <?php endif; ?>
        
        <div class="sign-block">
            <div style="margin-left: 5px;"><canvas id="signature-pad-<?= $key ?>" width="200" height="200" style="margin: 0 auto; display: block; margin-right: 70px;"></canvas></div>
            <?= Html::hiddenInput("testDetailAttendance[$key][attendeeSign]", $attendance->signature, ['id' => "signature-data-$key"]) ?>
            
            <div class="mt-2" style="position: absolute; bottom: 5px; right: 5px;">
                <?= Html::button('Clear', ['class' => 'btn btn-sm btn-danger', 'id' => "clear-signature-$key"]) ?>
            </div>
        </div>

    </td>

    <td>
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2' ></i>", "javascript:removeRow($key)") ?>
    </td>
</tr>