<?php

use frontend\models\test\RefTestStatus;
use yii\helpers\Html;
use frontend\models\test\TestFormAts;
use frontend\models\test\TestDetailAts;
?>
<style>
    .cbvcheader{
        padding:0.25rem;
    }
</style>
<table class="table table-sm table-bordered">
    <tr>
        <?= $currSts ? "<th></th>" : "" ?>
        <th class="vmiddle text-center">Mode</th>
        <?php
        $availNum = [];
        $availNumSec = [];
        for ($i = 1; $i < 11; $i++) {
            $attributeToRender = TestFormAts::HEAD_CBVC . $i;
            if (!empty($model->$attributeToRender)) {
                $availNum[] = $i;
                ?>
                <th class="p-0 text-center">
                    <?= Html::a("<i class='fa fa-minus-circle text-danger'></i>", "javascript:deleteColumn($model->id,'$attributeToRender','" . TestFormAts::FORM_TYPE_CBVC . "')", ['title' => 'Delete column']) ?>
                    <?= $form->field($model, $attributeToRender)->textInput(['value' => $model->$attributeToRender, 'id' => "testformats-$attributeToRender", 'data-formid' => $model->id, 'data-attribute' => "$attributeToRender", 'class' => 'form-control cbvcheader textInput text-center'])->label(false); ?>
                </th>
                <?php
            }
        }
        ?>
        <th rowspan="<?= count($details) + 1 ?>" class="vmiddle text-center p-0">
            <h4 class="mb-0">
                <?php
                if (count($availNum) == 10) {
                    echo Html::a("<i class='fa fa-plus-circle text-secondary'></i>", "javascript:maxColumn()", ['title' => 'Maximum number of column']);
                } else {
                    echo Html::a("<i class='fa fa-plus-circle text-success'></i>", "javascript:addColumn($model->id,'" . strval(TestDetailAts::TYPE_BREAKER) . "', '" . TestFormAts::FORM_TYPE_CBVC . "')", ['title' => 'Add Column']);
                }
                ?>
            </h4>
        </th>
        <?php
        for ($i = 11; $i < 16; $i++) {
            $attributeToRender = TestFormAts::HEAD_CBVC . $i;
            if (!empty($model->$attributeToRender)) {
                $availNum[] = $i;
                $availNumSec[] = $i;
                ?>
                <th class="p-0 text-center">
                    <?= Html::a("<i class='fa fa-minus-circle text-danger'></i>", "javascript:deleteColumn($model->id,'$attributeToRender','" . TestFormAts::FORM_TYPE_CBVC . "')", ['title' => 'Delete column']) ?>
                    <?= $form->field($model, $attributeToRender)->textInput(['value' => $model->$attributeToRender, 'id' => "testformats-$attributeToRender", 'data-formid' => $model->id, 'data-attribute' => "$attributeToRender", 'class' => 'form-control cbvcheader textInput text-center'])->label(false); ?>
                </th>
                <?php
            }
        }
        ?>
        <th rowspan="<?= count($details) + 1 ?>" class="vmiddle text-center p-0">
            <h4 class="mb-0">
                <?php
                if (count($availNumSec) == 5) {
                    echo Html::a("<i class='fa fa-plus-circle text-secondary'></i>", "javascript:maxColumn()", ['title' => 'Maximum number of column']);
                } else {
                    echo Html::a("<i class='fa fa-plus-circle text-success'></i>", "javascript:addColumn($model->id,'" . strval(TestDetailAts::TYPE_BUSBAR) . "', '" . TestFormAts::FORM_TYPE_CBVC . "')", ['title' => 'Add Column']);
                }
                ?>
            </h4>
        </th>
        <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
            <th class="vmiddle">Result</th>
        <?php } ?>
    </tr>
    <?php foreach ($details as $key => $detail) { ?>
        <tr>
            <?php if ($currSts) { ?>
                <td>
                    <?= Html::a("<i class='fa fa-minus-circle text-danger' ></i>", "javascript:deleteRow($detail->id,'" . TestFormAts::FORM_TYPE_CBVC . "')", ['title' => 'Delete row']) ?>
                </td>
            <?php } ?>
            <td class="p-0"><?= $form->field($detail, 'mode')->textInput(['value' => $detail->mode, 'id' => "testDetailAts[$key]-mode", 'name' => "testDetailAts[$key][mode]", 'data-formid' => $detail->id, 'data-attribute' => "mode", 'class' => 'form-control textInput text-center'])->label(false); ?></td>
            <?php
            foreach ($availNum as $i) {
                $attributeToRender = TestFormAts::VAL_CBVC . $i;
                echo $form->field($detail, 'id')->hiddenInput(['value' => $detail->id, 'id' => "testDetailAts[$key]-id", 'name' => "testDetailAts[$key][id]"])->label(false);
                ?>
                <td class="p-0">
                    <?= Html::button($detail->$attributeToRender ? 'ON' : 'OFF', ['class' => 'btn text-black toggleButton w-100', 'data-id' => $attributeToRender, 'data-detailid' => $detail->id, 'id' => "testDetailAts[$key]-$attributeToRender", 'name' => "testDetailAts[$key][$attributeToRender]"]) ?>
                </td>
            <?php } ?>
            <?php if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
                <td class="p-0">
                    <?= Html::button(is_null($detail->res_cbvc) ? "<span class='text-muted'>(PASS/FAIL)</span>" : ($detail->res_cbvc == 1 ? 'PASS' : 'FAIL'), ['class' => 'btn text-black toggleButtonResult w-100', 'data-id' => 'res_cbvc', 'data-detailid' => $detail->id, 'id' => "testDetailAts[$key]-res_cbvc", 'name' => "testDetailAts[$key][res_cbvc]"]) ?>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="19" class="text-center p-0">
            <h4 class="mb-0">
                <?= Html::a("<i class='fa fa-plus-circle text-success' ></i>", "javascript:addRow($model->id, '" . TestFormAts::FORM_TYPE_CBVC . "')", ['title' => 'Add row']) ?>
            </h4>
        </td>
    </tr>
</table>