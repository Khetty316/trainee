<?php

use yii\helpers\Html;
?> 
<tr>
    <td>
        <?= Html::textInput('itemId[]', $item->id, ['class' => 'hidden']) ?>
        <?= Html::textInput('itemDescription[]', $item->item_description, ['class' => 'form-control isItemDesc','type'=>'search']) ?>
    </td>
    <td>    
        <?= Html::textInput('quantity[]', $item->quantity, ['class' => 'form-control text-right isQty', 'type' => 'number', 'step' => '0.01']) ?>
        <span class='qtyError text-danger hidden'>Please insert Qty</span>
    </td>
    <td>
        <?= Html::dropDownList('unitCode[]', $item->unit_code, $unitList, ['class' => 'form-control']) ?>
    </td>
</tr>