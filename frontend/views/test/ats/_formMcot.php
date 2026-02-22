<?php

use frontend\models\test\RefTestStatus;
use yii\helpers\Html;
use frontend\models\test\TestFormAts;
?>
<table class="table table-sm table-bordered">
    <tr>
        <?= $currSts ? "<th></th>" : "" ?>
        <th class="vmiddle">Mode</th>
        <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
            <th class="vmiddle">Result</th>
        <?php } ?>
    </tr>
    <?php foreach ($details as $key => $detail) { ?>
        <tr>
            <?= $form->field($detail, 'id')->hiddenInput(['value' => $detail->id, 'id' => "testDetailAts[$key]-id", 'name' => "testDetailAts[$key][id]"])->label(false); ?>
            <?php if ($currSts) { ?>
                <td>
                    <?= Html::a("<i class='fa fa-minus-circle text-danger' ></i>", "javascript:deleteRow($detail->id,'mode')", ['title' => 'Delete row']) ?>
                </td>
            <?php } ?>
            <td class="p-0"><?= $form->field($detail, 'mode')->textInput(['value' => $detail->mode, 'id' => "testDetailAts[$key]-mode", 'name' => "testDetailAts[$key][mode]", 'data-formid' => $detail->id, 'data-attribute' => "mode", 'class' => 'form-control textInput'])->label(false); ?></td>
            <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
                <td class="p-0">
                    <?= Html::button(is_null($detail->res_mcot) ? "<span class='text-muted'>(PASS/FAIL)</span>" : ($detail->res_mcot == 1 ? 'PASS' : 'FAIL'), ['class' => 'btn text-black toggleButtonResult w-100', 'data-id' => 'res_mcot', 'data-detailid' => $detail->id, 'id' => "testDetailAts[$key]-res_mcot", 'name' => "testDetailAts[$key][res_mcot]"]) ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="10" class="text-center p-0">
            <h4 class="mb-0">
                <?= Html::a("<i class='fa fa-plus-circle text-success' ></i>", "javascript:addRow($model->id, '" . TestFormAts::FORM_TYPE_MCOT . "')", ['title' => 'Add row']) ?>
            </h4>
        </td>
    </tr>
</table>