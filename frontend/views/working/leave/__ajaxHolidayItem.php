<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
?>
<tr class="p-0 m-0" id='tr_<?= $key ?>'>
    <td>
        <?= Html::textInput("leaveHoliday[$key][holidayId]", $holiday->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("leaveHoliday[$key][toDelete]", $holiday->id, ['class' => 'hidden','id'=>"toDelete-$key"]) ?>
        <?= Html::textInput("leaveHoliday[$key][holidayDate]", MyFormatter::asDate_Read($holiday->holiday_date), ['class' => 'form-control datepicker', 'required' => true, 'onchange'=>'checkDate(this)']) ?>
    </td>
    <td> 
        <?= Html::textInput("leaveHoliday[$key][holidayName]", $holiday->holiday_name, ['class' => 'form-control col-xs-8', 'required' => true]) ?>
    </td>
    <td>
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2' ></i>", "javascript:removeRow($key)") ?>
    </td>
</tr>