<?php

use yii\helpers\Html;
?>
<tr class="p-0 m-0" id='tr_<?= $key0 ?>'>
    <td class="py-0">
        <?= Html::textInput("testItemComponent[$key0][idOther]", $item->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("testItemComponent[$key0][idDetail]", $detailId, ['class' => 'hidden']) ?>
        <?= Html::textInput("testItemComponent[$key0][toDelete]", $item->id, ['class' => 'hidden', 'id' => "toDelete-$key0"]) ?>
        <?= Html::textInput("testItemComponent[$key0][attributeOther]", $item->attribute, ['class' => 'form-control dynamic-table compDetail']) ?>
    </td>
    <td class="py-0"> 
        <?= Html::textInput("testItemComponent[$key0][valueOther]", $item->value, ['class' => 'form-control dynamic-table']) ?>
    </td>
    <td class="text-center vmiddle">
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2' ></i>", "javascript:removeRow($key0)", ['class' => 'btn-dis']) ?>
    </td>
</tr>