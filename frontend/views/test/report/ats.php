<meta charset="UTF-8">
<?php

use frontend\models\test\RefTestStatus;

$ats = $model1;
$detailAcots = $model2;
$detailMcots = $model3;
$detailCbvcs = $model4;
$customContents = $model5;
$currSts = ($ats->status != RefTestStatus::STS_READY_FOR_TESTING && $ats->status != RefTestStatus::STS_FAIL && $ats->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <p><b>1. Auto Change Over Test (5 sec)</b></p>
    <?= $this->render('_formAcot', ['ats' => $ats, 'detailAcots' => $detailAcots]); ?>

    <?php
    $limit = 100;
    $allNumbersMcots = [];
    $totalAcots = count($detailAcots);
    if ($totalAcots === 0) {
        $totalAcots = 3;
    }
    for ($number = 30, $increment = 0; $number <= $limit; $number++) {
        $allNumbersMcots[] = $number;
        $increment++;

        if ($increment == 25) {
            $number += 30;
            $increment = 0;
        }
    }

    if (in_array($totalAcots, $allNumbersMcots)):
        ?>
        <div id="mcot-form" class="nextPage">
            <p><b>2. Manual Change Over Test (With no control on power supply)</b></p>
            <?= $this->render('_formMcot', ['ats' => $ats, 'detailMcots' => $detailMcots]); ?>
        </div>
    <?php else: ?>
        <p><b>2. Manual Change Over Test (With no control on power supply)</b></p>
        <?= $this->render('_formMcot', ['ats' => $ats, 'detailMcots' => $detailMcots]); ?>
    <?php endif; ?>

    <?php
    $allNumbersCbvcs = [];
    $totalMcots = count($detailMcots);
    if ($totalMcots === 0) {
        $totalMcots = 3;
    }

    $totalAcotsMcots = $totalAcots + $totalMcots;
    for ($num = 30, $increment = 0; $num <= $limit; $num++) {
        $allNumbersCbvcs[] = $num;
        $increment++;

        if ($increment == 25) {
            $num += 30;
            $increment = 0;
        }
    }

    if (in_array($totalAcotsMcots, $allNumbersCbvcs)):
        ?>
        <div id="cbvc-form" class="nextPage">
            <p><b>3. Circuit Breaker Voltage Check</b></p>
            <?= $this->render('_formCbvc', ['ats' => $ats, 'detailCbvcs' => $detailCbvcs]); ?>
        </div>
    <?php else: ?>
        <p><b>3. Circuit Breaker Voltage Check</b></p>
        <?= $this->render('_formCbvc', ['ats' => $ats, 'detailCbvcs' => $detailCbvcs]); ?>
    <?php endif; ?>

    <?php
    $allNumbers = [];
    $totalCbvcs = count($detailCbvcs);
    $totalAcotsMcotsCbvcs = $totalAcotsMcots + $totalCbvcs;
    for ($number = 40, $increment = 0; $number <= $limit; $number++) {
        $allNumbers[] = $number;
        $increment++;

        if ($increment == 11) {
            $number += 30;
            $increment = 0;
        }
    }
    ?>

    <?php
    if (!empty($customContents)) {
        foreach ($customContents as $index => $content) {
            ?>
            <div class="content-wrapper nextPage">
                <?= $content->content ?>
            </div>
            <?php
        }
    }
    ?>

    <?php
//    if (in_array($totalAcotsMcotsCbvcs, $allNumbers)):
    ?>
    <!--<div class="nextPage">-->
    <?php //= $this->render('sign', ['master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_ATS)]) ?>
    <!--</div>-->
    <?php // else: ?>
    <!--<br/><br/>-->
    <?php //= $this->render('sign', ['master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_ATS)]) ?>
    <?php // endif; ?>

    <?php
    // Determine if signature needs nextPage div
    $isFatFailOrComplete = ($main->test_type === \frontend\models\test\TestMain::TEST_FAT_TITLE &&
            ($master->status == frontend\models\test\RefTestStatus::STS_FAIL ||
            $master->status == frontend\models\test\RefTestStatus::STS_COMPLETE));

    $needsNextPageDiv = false;

    if (!empty($customContents)) {
        // If customContents exists, only FAT FAIL/COMPLETE gets nextPage
        $needsNextPageDiv = $isFatFailOrComplete;
    } else {
        // If no customContents, check array condition OR FAT FAIL/COMPLETE
        $needsNextPageDiv = in_array($totalAcotsMcotsCbvcs, $allNumbers) || $isFatFailOrComplete;
    }

    // Render signature
    if ($needsNextPageDiv):
        ?>
        <div class="nextPage">
            <?= $this->render('sign', ['formStatus' => $ats, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_ATS)]) ?>
        </div>
    <?php else: ?>
        <?= $this->render('sign', ['formStatus' => $ats, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_ATS)]) ?>
    <?php endif; ?>
</body>
