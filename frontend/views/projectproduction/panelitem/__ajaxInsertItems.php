<?php

use yii\helpers\Html;
?> 
<tr>
    <td class="p-0 m-0">
        <?= Html::textInput('itemId[]', $item->id, ['class' => 'hidden']) ?>
        <?= Html::textInput('itemDescription[]', $item->item_description, ['class' => 'form-control form-control-sm isItemDesc','type'=>'search']) ?>
    </td>
    <td class="p-0 m-0">    
        <?= Html::textInput('quantity[]', $item->quantity, ['class' => 'form-control form-control-sm text-right isQty', 'type' => 'number', 'step' => '0.01']) ?>
        <span class='qtyError text-danger hidden'>Please insert Qty</span>
    </td>
    <td class="p-0 m-0">
        <?= Html::dropDownList('unitCode[]', $item->unit_code, $unitList, ['class' => 'form-control form-control-sm ']) ?>
    </td>
</tr>