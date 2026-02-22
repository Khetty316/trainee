<?php

use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormAts;
?>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th class="vmiddle text-center">Mode</th>
            <?php
            $availNum = [];
            for ($i = 1; $i < 11; $i++) {
                $attributeToRender = TestFormAts::HEAD_ACOT . $i;
                if (!empty($ats->$attributeToRender)) {
                    $availNum[] = $i;
                    ?>
                    <th class="p-0 text-center">
                        <?= $ats->$attributeToRender; ?>
                    </th>
                    <?php
                }
            }
            ?>
            <th class="vmiddle text-center">Result</th>
        </tr>
    </thead>
    <?php
    if (empty($detailAcots)) {
        for ($k = 0; $k < 3; $k++) {
            ?>
            <tr height="20">
                <td class="p-0 text-center"></td>
                <?php
                foreach ($availNum as $i) {
                    $attributeToRender = TestFormAts::VAL_ACOT . $i;
                    ?>
                    <td class="p-0 text-center" width="20%"></td>
                <?php } ?>
                <td class="p-0 text-center"></td>
            </tr>
            <?php
        }
    } else {
        foreach ($detailAcots as $key => $detail) {
            ?>
            <tr>
                <td class="p-0 text-center"><?= $detail->mode ?></td>
                <?php
                foreach ($availNum as $i) {
                    $attributeToRender = TestFormAts::VAL_ACOT . $i;
                    ?>
                    <td class="p-0 text-center">
                        <?= $detail->$attributeToRender ? 'ON' : 'OFF' ?>
                    </td>
                <?php } ?>
                <td class="p-0 text-center">
                    <?= is_null($detail->res_acot) ? "<span class='text-muted'>(PASS/FAIL)</span>" : ($detail->res_acot == 1 ? 'PASS' : 'FAIL') ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>