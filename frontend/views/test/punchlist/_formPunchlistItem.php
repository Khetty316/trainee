<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use frontend\models\test\TestMaster;
use frontend\models\projectproduction\RefProjProdTaskErrors;
?>
<tr class="p-0 m-0" id='tr_<?= $key ?>'>
    <td>
        <?= Html::textInput("testDetailPunchlist[$key][punchlistId]", $punchlist->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("testDetailPunchlist[$key][toDelete]", $punchlist->id, ['class' => 'hidden', 'id' => "toDelete-$key"]) ?>
        <?= Html::dropDownList("testDetailPunchlist[$key][punchlistForm]", $punchlist->test_form_code, TestMaster::getDropdownSelectedForms($master->id), ['class' => 'form-control functionality-input', 'required' => true]) ?>
    </td>
    <td>
        <?= Html::dropDownList("testDetailPunchlist[$key][punchlistError]", $punchlist->error_id, RefProjProdTaskErrors::getDropDownListAll(), ['class' => 'form-control functionality-input', 'required' => true]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailPunchlist[$key][punchlistRemark]", $punchlist->remark, ['class' => 'form-control functionality-input', 'required' => true]) ?>
    </td>
    <td> 
        <?= Html::textInput("testDetailPunchlist[$key][punchlistDate]", MyFormatter::asDate_Read($punchlist->rectify_date), ['class' => 'form-control  datepicker', 'onchange' => 'checkDate(this)']) ?>
    </td>
    <td> 
        <?= Html::textInput("testDetailPunchlist[$key][punchlistVerify]", $punchlist->verify_by, ['class' => 'form-control']) ?>
    </td>
    <td>
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2' ></i>", "javascript:removeRow($key)") ?>
    </td>
</tr>