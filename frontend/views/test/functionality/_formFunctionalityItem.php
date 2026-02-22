<?php

use yii\helpers\Html;
?>
<tr class="p-0 m-0" id='tr_<?= $key ?>'>
    <td>
        <?= Html::textInput("testItemFunctionality[$key][functionalityId]", $functionality->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("testItemFunctionality[$key][toDelete]", $functionality->id, ['class' => 'hidden', 'id' => "toDelete-$key"]) ?>
        <?= Html::textInput("testItemFunctionality[$key][functionalityNo]", $functionality->no, ['class' => 'form-control functionality-input']) ?>
    </td>
    <td> 
        <?= Html::textInput("testItemFunctionality[$key][functionalityFeeder]", $functionality->feeder_tag, ['class' => 'form-control functionality-input']) ?>
    </td>
    <td> 
        <?= Html::textInput("testItemFunctionality[$key][functionalityPower]", $functionality->voltage_apt, ['class' => 'form-control', 'type' => 'number']) ?>
    </td>
    <td class="text-center vmiddle"> 
        <?= Html::radio("testItemFunctionality[$key][functionalityVPass]", $functionality->voltage_apt_sts === 1, ['class' => 'succ-volt', 'value' => 'pass']) ?> <i class="fas fa-check text-success"></i>
        <?= Html::radio("testItemFunctionality[$key][functionalityVPass]", $functionality->voltage_apt_sts === 0, ['value' => 'fail']) ?> <i class="fas fa-times text-danger"></i>
        <?= Html::radio("testItemFunctionality[$key][functionalityVPass]", $functionality->voltage_apt_sts === null, ['value' => 'na']) ?> <i class="fas fa-not-equal text-warning"></i>
    </td>
    <td> 
        <?= Html::textInput("testItemFunctionality[$key][functionalityWiring]", $functionality->wiring_tc, ['class' => 'form-control', 'type' => 'number']) ?>
    </td>
    <td class="text-center vmiddle"> 
        <?= Html::radio("testItemFunctionality[$key][functionalityWPass]", $functionality->wiring_tc_sts === 1, ['class' => 'succ-wire', 'value' => 'pass']) ?> <i class="fas fa-check text-success"></i>
        <?= Html::radio("testItemFunctionality[$key][functionalityWPass]", $functionality->wiring_tc_sts === 0, ['value' => 'fail']) ?> <i class="fas fa-times text-danger"></i>
        <?= Html::radio("testItemFunctionality[$key][functionalityWPass]", $functionality->wiring_tc_sts === null, ['value' => 'na']) ?> <i class="fas fa-not-equal text-warning"></i>
    </td>
    <td class="border-left border-top-0">
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2 vmiddle' ></i>", "javascript:removeRow($key)") ?>
    </td>
</tr>