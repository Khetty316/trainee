<?php

use frontend\models\test\RefTestStatus;
use yii\helpers\Html;
?>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th class="vmiddle text-center">Mode</th>
            <th class="vmiddle text-center">Result</th>
        </tr>
    </thead>
    <?php
    if (empty($detailMcots)) {
        for ($k = 0; $k < 3; $k++) {
            ?>
            <tr height="20">
                <td class="p-0 text-center"></td>
                <td class="p-0 text-center"></td>
            </tr>
            <?php
        }
    } else {
        foreach ($detailMcots as $key => $detail) {
            ?>
            <tr>
                <td class="p-0 text-center"><?= $detail->mode; ?></td>
                <td class="p-0 text-center">
                    <?= is_null($detail->res_mcot) ? "<span class='text-muted'>(PASS/FAIL)</span>" : ($detail->res_mcot == 1 ? 'PASS' : 'FAIL') ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>