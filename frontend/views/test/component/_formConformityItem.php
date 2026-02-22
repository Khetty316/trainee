<?php

use yii\helpers\Html;
?>
<tr class="p-0 m-0" id='tr_<?= $key2 ?>'>
    <td class="py-0">
        <?= Html::textInput("testDetailConform[$key2][conformityId]", $conformity->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("testDetailConform[$key2][toDelete]", $conformity->id, ['class' => 'hidden', 'id' => "toDelete-$key2"]) ?>
        <?= Html::textInput("testDetailConform[$key2][conformityComponent]", $conformity->non_conform, ['class' => 'form-control dynamic-table nonconform']) ?>
    </td>
    <td class="py-0"> 
        <?= Html::textInput("testDetailConform[$key2][conformityRemark]", $conformity->remark, ['class' => 'form-control dynamic-table remark']) ?>
    </td>
    <td class="text-center">
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2' ></i>", "javascript:removeRow($key2)") ?>
    </td>
</tr>