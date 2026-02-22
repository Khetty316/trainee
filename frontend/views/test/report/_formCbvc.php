<?php

use frontend\models\test\RefTestStatus;
use yii\helpers\Html;
use frontend\models\test\TestFormAts;
?>
<style>
    .cbvcheader{
        padding:0.25rem;
    }
</style>
<table class="table table-sm table-bordered">
    <thead>
        <tr>
            <th class="vmiddle text-center" width="15%">Mode</th>
            <?php
            $availNum = [];
            $availNumSec = [];
            for ($i = 1; $i < 11; $i++) {
                $attributeToRender = TestFormAts::HEAD_CBVC . $i;
                if (!empty($ats->$attributeToRender)) {
                    $availNum[] = $i;
                    ?>
                    <th class="p-0 text-center" width="10%">
                        <?= $ats->$attributeToRender ?>
                    </th>
                    <?php
                }
            }
            ?>
            <?php
            for ($i = 11; $i < 16; $i++) {
                $attributeToRender = TestFormAts::HEAD_CBVC . $i;
                if (!empty($ats->$attributeToRender)) {
                    $availNum[] = $i;
                    $availNumSec[] = $i;
                    ?>
                    <th class="p-0 text-center" width="10%">
                        <?= $ats->$attributeToRender ?>
                    </th>
                    <?php
                }
            }
            ?>
            <th class="vmiddle text-center" width="15%">Result</th>
        </tr>
    </thead>
    <?php
    if (empty($detailCbvcs)) {
        for ($k = 0; $k < 3; $k++) {
            ?>
            <tr height="20">
                <td class="p-0 text-center" width="15%"></td>
                <?php
                foreach ($availNum as $i) {
                    $attributeToRender = TestFormAts::VAL_CBVC . $i;
                    ?>
                    <td class="p-0 text-center" width="10%"></td>
                <?php } ?>
                <td class="p-0 text-center" width="15%"></td>
            </tr>
            <?php
        }
    } else {
        foreach ($detailCbvcs as $key => $detail) {
            ?>
            <tr>
                <td class="p-0 text-center" width="15%"><?= $detail->mode ?></td>
                <?php
                foreach ($availNum as $i) {
                    $attributeToRender = TestFormAts::VAL_CBVC . $i;
                    ?>
                    <td class="p-0 text-center" width="10%">
                        <?= $detail->$attributeToRender ? 'ON' : 'OFF' ?>
                    </td>
                <?php } ?>
                <td class="p-0 text-center" width="15%">
                    <?= is_null($detail->res_cbvc) ? "<span class='text-muted'>(PASS/FAIL)</span>" : ($detail->res_cbvc == 1 ? 'PASS' : 'FAIL') ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
</table>