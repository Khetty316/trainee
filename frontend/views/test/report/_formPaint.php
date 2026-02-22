<?php

use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormVisualpaint;
?>

<table class="table table-sm table-bordered text-center" style="vertical-align: middle;">
    <thead>
        <tr>
            <td rowspan="2" width="15%"></td>
            <td colspan="6">UNIT(<span>&micro;</span>m)</td>
        </tr>
        <tr>
            <?php foreach (range('A', 'F') as $char) : ?>
                <td><?= $char ?></td>
            <?php endforeach; ?>
        </tr>    
    </thead>
    <tbody>
        <?php for ($i = 1; $i <= 3; $i++) : ?>
            <tr>
                <td width="15%" height="20">Measurement <?= $i ?></td>
                <?php foreach (range('a', 'f') as $char) : ?>
                    <td style="padding: 0;">
                        <?= $visualpaint->{"{$char}_measure{$i}"} ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endfor; ?>
        <tr>
            <td height="20">Average</td>
            <?php foreach (range('a', 'f') as $char) : ?>
                <td style="padding: 0;">
                    <?= $visualpaint->{"{$char}_average"} ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php if ($visualpaint->status != RefTestStatus::STS_SETUP && $visualpaint->status != RefTestStatus::STS_READY_FOR_TESTING) { ?>
            <tr>
                <td height="10">Result</td>
                <?php foreach (range('a', 'f') as $char) : ?>
                    <td style="padding: 0;">
                        <?= ($visualpaint->{"res_ave_{$char}"} === null) ? '' : ($visualpaint->{"res_ave_{$char}"} == TestFormVisualpaint::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?>
                        <span id="resAve<?= $char ?>"></span>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php } ?>
    </tbody>
</table>
