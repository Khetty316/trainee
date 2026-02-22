<?php

use yii\bootstrap4\Html;
use frontend\models\test\RefTestStatus;

$currSts = ($status != RefTestStatus::STS_SETUP && $status != RefTestStatus::STS_READY_FOR_TESTING) ? 4 : 3;
?>
<tr class="p-0 m-0 tr_<?= $key ?>">
    <td rowspan="<?= $currSts ?>">
        <?= Html::textInput("testDetailDimension[$key][dimensionId]", $dimension->id, ['class' => 'hidden']) ?>
        <?= Html::textInput("testDetailDimension[$key][toDelete]", $dimension->id, ['class' => 'hidden', 'id' => "toDelete-$key"]) ?>
        <?= Html::textarea("testDetailDimension[$key][dimensionPanel]", $dimension->panel_name ?: $panel->panel_description, ['class' => 'form-control custom-dropdown panel_desc', 'style' => 'font-size: smaller;', 'rows' => 5]) ?>
    </td>
    <td class="vmiddle">Drawing</td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionDrawingH]", $dimension->drawing_h, ['class' => 'form-control custom-dropdown hDraw', 'type' => 'number', 'data-key' => $key]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionDrawingW]", $dimension->drawing_w, ['class' => 'form-control custom-dropdown wDraw', 'type' => 'number', 'data-key' => $key]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionDrawingD]", $dimension->drawing_d, ['class' => 'form-control custom-dropdown dDraw', 'type' => 'number', 'data-key' => $key]) ?>
    </td>
    <td rowspan="<?= $currSts ?>" class="vmiddle">
        <?= \yii\helpers\Html::a("<i class='fa fa-minus-circle text-danger mt-2 px-3' ></i>", "javascript:removeRow($key)") ?>
    </td>
</tr>
<tr class="p-0 m-0 tr_<?= $key ?>">
    <td class="vmiddle">As-built</td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionBuiltH]", $dimension->built_h, ['class' => 'form-control custom-dropdown hBuilt', 'type' => 'number', 'data-key' => $key]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionBuiltW]", $dimension->built_w, ['class' => 'form-control custom-dropdown wBuilt', 'type' => 'number', 'data-key' => $key]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionBuiltD]", $dimension->built_d, ['class' => 'form-control custom-dropdown dBuilt', 'type' => 'number', 'data-key' => $key]) ?>
    </td>
</tr>

<tr class="p-0 m-0 tr_<?= $key ?>" >
    <td class="vmiddle">Error (%)</td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionErrorH]", $dimension->error_h, ['class' => 'form-control custom-dropdown custom-disabled hError', 'data-key' => $key]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionErrorW]", $dimension->error_w, ['class' => 'form-control custom-dropdown custom-disabled wError', 'data-key' => $key]) ?>
    </td>
    <td>
        <?= Html::textInput("testDetailDimension[$key][dimensionErrorD]", $dimension->error_d, ['class' => 'form-control custom-dropdown custom-disabled dError', 'data-key' => $key]) ?>
    </td>
</tr>
<?php if ($currSts != 3) { ?>
    <tr class="p-0 m-0 tr_<?= $key ?>" >
        <td class="vmiddle"><b>Result</b></td>
        <td>
            <?= Html::textInput("testDetailDimension[$key][dimensionResHText]", $dimension->result_text, ['class' => 'form-control custom-dropdown h_res_sts', 'data-key' => $key]) ?>
            <?= Html::hiddenInput("testDetailDimension[$key][dimensionResH]", $dimension->res_h, ['class' => 'form-control custom-dropdown h_res', 'data-key' => $key]) ?>
        </td>
        <td>
            <?= Html::textInput("testDetailDimension[$key][dimensionResWText]", $dimension->result_text, ['class' => 'form-control custom-dropdown w_res_sts', 'data-key' => $key]) ?>
            <?= Html::hiddenInput("testDetailDimension[$key][dimensionResW]", $dimension->res_w, ['class' => 'form-control custom-dropdown w_res', 'data-key' => $key]) ?>
        </td>
        <td>
            <?= Html::textInput("testDetailDimension[$key][dimensionResDText]", $dimension->result_text, ['class' => 'form-control custom-dropdown d_res_sts', 'data-key' => $key]) ?>
            <?= Html::hiddenInput("testDetailDimension[$key][dimensionResD]", $dimension->res_d, ['class' => 'form-control custom-dropdown d_res', 'data-key' => $key]) ?>
        </td>
    </tr>
<?php }
?>