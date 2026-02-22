<meta charset="UTF-8">
<?php

use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormInsuhipot;

$insuhipot = $model1;
$insuhipotProcedures = $model2;
$showResult = ($insuhipot->status != RefTestStatus::STS_SETUP && $insuhipot->status != RefTestStatus::STS_READY_FOR_TESTING) ? 1 : 0;
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <div class="p1">
        <?php
        $modelProcedures = explode('|', $insuhipotProcedures);
        echo '<p>' . (isset($modelProcedures[0]) ? $modelProcedures[0] : '') . '</p>';
        ?>
        <?php
        echo '<p>' . (isset($modelProcedures[1]) ? $modelProcedures[1] : '') . '</p>';
        ?>

    </div>

    <!--<div class="nextPage my-4">-->
    <?php
//        echo '<p>' . (isset($modelProcedures[1]) ? $modelProcedures[1] : '') . '</p>';
    ?>
    <div class="nextPage"></div>
    <p><b>Insulation Resistance Test Results</b></p>
    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <td rowspan="2" width="10%">Phase </td>
                <td width="40%">Before Pressure Test</td>
                <td width="40%">After Pressure Test</td>
                <?php if ($showResult) { ?>
                    <td rowspan="2" class="result" width="10%">Result</td>
                <?php } ?>
            </tr>
            <tr>
                <td width="40%">Insulation Resistance (MOhms)</td>
                <td width="40%">Insulation Resistance (MOhms)</td>
            </tr>
        </thead>
        <tbody>
            <tr id="tr_1">
                <td width="10%" height="19">R-E</td>
                <td width="40%"><?= $insuhipot->re1 ?></td>
                <td width="40%"><?= $insuhipot->re2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_re === null) ? '' : ($insuhipot->res_re == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_2">
                <td width="10%" height="19">Y-E</td>  
                <td width="40%"><?= $insuhipot->ye1 ?></td>
                <td width="40%"><?= $insuhipot->ye2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_ye === null) ? '' : ($insuhipot->res_ye == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_3">
                <td width="10%" height="19">B-E</td>   
                <td width="40%"><?= $insuhipot->be1 ?></td>
                <td width="40%"><?= $insuhipot->be2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_be === null) ? '' : ($insuhipot->res_be == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_4">
                <td width="10%" height="19">N-E</td> 
                <td width="40%"><?= $insuhipot->ne1 ?></td>
                <td width="40%"><?= $insuhipot->ne2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_ne === null) ? '' : ($insuhipot->res_ne == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_5">
                <td width="10%" height="19">R-N</td>
                <td width="40%"><?= $insuhipot->rn1 ?></td>
                <td width="40%"><?= $insuhipot->rn2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_rn === null) ? '' : ($insuhipot->res_rn == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?> 
            </tr>
            <tr id="tr_6">
                <td width="10%" height="19">Y-N</td>
                <td width="40%"><?= $insuhipot->yn1 ?></td>
                <td width="40%"><?= $insuhipot->yn2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_yn === null) ? '' : ($insuhipot->res_yn == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?> 
            </tr>
            <tr id="tr_7">
                <td width="10%" height="19">B-N</td>
                <td width="40%"><?= $insuhipot->bn1 ?></td>
                <td width="40%"><?= $insuhipot->bn2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_bn === null) ? '' : ($insuhipot->res_bn == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_8">
                <td width="10%" height="19">R-Y</td>
                <td width="40%"><?= $insuhipot->ry1 ?></td>
                <td width="40%"><?= $insuhipot->ry2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_ry === null) ? '' : ($insuhipot->res_ry == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_9">
                <td width="10%" height="19">Y-B</td>
                <td width="40%"><?= $insuhipot->yb1 ?></td>
                <td width="40%"><?= $insuhipot->yb2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_yb === null) ? '' : ($insuhipot->res_yb == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?>
            </tr>
            <tr id="tr_10">
                <td width="10%" height="19">B-R</td>
                <td width="40%"><?= $insuhipot->br1 ?></td>
                <td width="40%"><?= $insuhipot->br2 ?></td>
                <?php if ($showResult) { ?>
                    <td width="10%"><?= ($insuhipot->res_br === null) ? '' : ($insuhipot->res_br == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                <?php } ?> 
            </tr>
        </tbody>
    </table>

    <br>
    <p><b>Hipot Test Results</b></p>
    <table class="table table-sm table-bordered text-center">
        <thead>
            <tr>
                <td>Between </td>
                <td>Leakage Current Starting <br>(mA)</td>
                <td>Leakage Current Ending <br>(mA)</td>
                <td>Lapsed Time</td>
                <?php if ($showResult) { ?>
                    <td id="result">Result</td>
                <?php } ?> 
            </tr>
        </thead>
        <tbody>
            <tr id="tr_11">
                <td height="18">R and Y+B+N+earth</td>
                <td><?= $insuhipot->r_start ?></td>
                <td><?= $insuhipot->r_end ?></td>
                <td><?= $insuhipot->r_time ?></td>
                <?php if ($showResult) { ?>
                    <td><?= ($insuhipot->res_r === null) ? '' : ($insuhipot->res_r == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td> 
                <?php } ?> 
            </tr>
            <tr id="tr_12">
                <td height="18">Y and B+R+N+earth</td>  
                <td><?= $insuhipot->y_start ?></td>
                <td><?= $insuhipot->y_end ?></td>
                <td><?= $insuhipot->y_time ?></td>
                <?php if ($showResult) { ?>
                    <td><?= ($insuhipot->res_y === null) ? '' : ($insuhipot->res_y == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td> 
                <?php } ?> 
            </tr>
            <tr id="tr_13">
                <td height="18">B and R+Y+N+earth</td>   
                <td><?= $insuhipot->b_start ?></td>
                <td><?= $insuhipot->b_end ?></td>
                <td><?= $insuhipot->b_time ?></td>
                <?php if ($showResult) { ?>
                    <td><?= ($insuhipot->res_b === null) ? '' : ($insuhipot->res_b == TestFormInsuhipot::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>  
                <?php } ?> 
            </tr>
        </tbody>
    </table>
    <div class="">
        <p><b>Remarks :</b></p>
        <p><?= $insuhipot->remark ?></p>
    </div>
    <!--</div>-->
    <?php
    if ($main->test_type === \frontend\models\test\TestMain::TEST_FAT_TITLE) {
        if ($insuhipot->status == frontend\models\test\RefTestStatus::STS_FAIL || $insuhipot->status == frontend\models\test\RefTestStatus::STS_COMPLETE) {
            ?>
            <div class="nextPage">
                <?= $this->render('sign', ['formStatus' => $insuhipot, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_INSUHIPOT)]) ?>
            </div>
        <?php } else { ?>
            <?= $this->render('sign', ['formStatus' => $insuhipot, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_INSUHIPOT)]) ?>
            <?php
        }
    } else {
        ?>
        <?= $this->render('sign', ['formStatus' => $insuhipot, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_INSUHIPOT)]) ?>
    <?php } ?>
</div>

</body>

