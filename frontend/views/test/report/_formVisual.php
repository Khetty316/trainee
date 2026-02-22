<?php

use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormVisualpaint;

$formList = [" A (Front)" => "a", " B (Left Side)" => "b", " C (Right Side)" => "c", " D (Top)" => "d", " E (Rear)" => "e", " F (Interior)" => "f"];
?>

<table class="table table-sm table-bordered text-center">
    <thead>
        <tr>
            <td width="14%"></td>
            <td width="10%">Severe<br/>Scratches</td>
            <td width="10%">Rusts</td>
            <td width="23%">Color</td>
            <td width="10%">Finishing</td>
            <td width="26%">Remark</td>
            <?php if ($visualpaint->status != RefTestStatus::STS_SETUP && $visualpaint->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
                <td width="7%">Result</td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($formList as $key => $value) { ?>
            <tr>
                <td width="14%" class="text-left" height="20"><?= $key ?></td>
                <td width ="10%">
                    <?= ($visualpaint->{$value . "_scratch"} === null) ? '' : ($visualpaint->{$value . "_scratch"} == 1 ? 'Yes' : 'No')?>
                </td>
                <td width="10%">
                    <?= ($visualpaint->{$value . "_rust"} === null) ? '' : ($visualpaint->{$value . "_rust"} == 1 ? 'Yes' : 'No') ?>
                </td>
                <td width="23%">
                    <?= $visualpaint->{$value . "_color"} ?>
                </td>
                <td width="10%">
                    <?= $visualpaint->{$value . "_finishing"} ?>
                </td>
                <td width="26%">
                    <?= $visualpaint->{$value . "_remark"} ?>
                </td>
                <?php if ($visualpaint->status != RefTestStatus::STS_SETUP && $visualpaint->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
                    <td class="text-center vmiddle" width="7%">
                        <?= ($visualpaint->{"res_" . $value} === null) ? '' : ($visualpaint->{"res_" . $value} == TestFormVisualpaint::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
