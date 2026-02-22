<meta charset="UTF-8">
<?php
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
$functionalities = $model1;
$customContents = $model2;
$funcForm = $model3;
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <?php
    $num = 1;
    $totalItems = 0;
    $limit = 1000;
    $allNumbers = [];

    for ($number = 20, $increment = 0; $number <= $limit; $number++) {
        $allNumbers[] = $number;
        $increment++;

        if ($increment == 6) {
            $number += 14;
            $increment = 0;
        }
    }

    $detailperpage = [];
    $currentNumber = 3;
    $increment = 2;

    for ($i = 0; $i < $limit; $i++) {
        $detailperpage[] = $currentNumber;
        $currentNumber += $increment;
    }

    if (empty($functionalities)) {
        if (empty($customContents)) {
            $totalItems = 10;
            ?>
            <p><b>Point of Test: </b></p>
            <table class="table table-sm text-center table-bordered">
                <thead>
                    <tr>
                        <td class="vmiddle" rowspan="2">No.</td>
                        <td class="vmiddle" rowspan="2">Feeder Tag No.</td>
                        <td rowspan="1" colspan="4">Output</td>
                    </tr>
                    <tr>
                        <td rowspan="1">Voltage At Power<br/>Terminal</td>
                        <td rowspan="1">Pass / Fail<br/> / Not Available</td>
                        <td rowspan="1">Wiring<br>Termination<br/>Connection</td>
                        <td rowspan="1">Pass / Fail<br/> / Not Available</td>
                    </tr>
                </thead>
                <tbody class="sortableTable">
                    <?php
                    for ($i = 0; $i < 10; $i++) {
                        ?>
                        <tr>
                            <td height="15.5"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
        } else {
            $totalItems = 0;
        }
    } else {
        foreach ($functionalities as $key => $data) {
            $detail = $data['detail'];
            $functionality = $data['items'];
            if (in_array($num, $detailperpage)) {
                echo '<div class="nextPage"></div>';
            }
            ?>
            <p><b>Point of Test:<span><?= $detail->pot0->name ?>-<?= $detail->pot_val ?></span></b></p>
            <table class="table table-sm text-center table-bordered">
                <thead>
                    <tr>
                        <td class="vmiddle" rowspan="2">No.</td>
                        <td class="vmiddle" rowspan="2">Feeder Tag No.</td>
                        <td rowspan="1" colspan="4">Output</td>
                    </tr>
                    <tr>
                        <td rowspan="1">Voltage At Power<br/>Terminal</td>
                        <td rowspan="1">Pass / Fail<br/> / Not Available</td>
                        <td rowspan="1">Wiring<br>Termination<br/>Connection</td>
                        <td rowspan="1">Pass / Fail<br/> / Not Available</td>
                    </tr>
                </thead>
                <tbody  class="sortableTable">
                    <?php
                    if (empty($functionality)) {
                        for ($i = 0; $i < 10; $i++) {
                            ?>
                            <tr>
                                <td height="15.5"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <?php
                            $totalItems++;
                        }
                    } else {
                        foreach ($functionality as $val => $function) {
                            ?>
                            <tr data-functionality-id="<?= $function->id ?>" data-detail-id="<?= $key ?>">
                                <td height="15.5"><?= $function->no ?></td>
                                <td><?= $function->feeder_tag ?></td>
                                <td><?= $function->voltage_apt ?></td>
                                <td><?= $function->voltage_apt_sts == 1 ? '<span style="font-family:zapfdingbats;">3</span>' : ($function->voltage_apt_sts === null ? '<span>N/A</span>' : '<span style="font-family:zapfdingbats;">5</span>') ?></td>
                                <td><?= $function->wiring_tc ?></td>
                                <td><?= $function->wiring_tc_sts == 1 ? '<span style="font-family:zapfdingbats;">3</span>' : ($function->wiring_tc_sts === null ? '<span>N/A</span>' : '<span style="font-family:zapfdingbats;">5</span>') ?></td>
                            </tr>
                            <?php
                            $totalItems++;
                        }
                    }
                    ?>
                </tbody>
            </table>
            <?php
            $num++;
        }
    }
    ?>

    <?php
    if (empty($functionality)) {
        if (!empty($customContents)) {
            foreach ($customContents as $index => $content) {
                if ($index === 0) {
                    // First content - no nextPage class
                    ?>
                    <div class="content-wrapper">
                    <?= $content->content ?>
                    </div>
                    <?php
                } else {
                    // Second and subsequent content - with nextPage class
                    ?>
                    <div class="content-wrapper nextPage">
                    <?= $content->content ?>
                    </div>
                    <?php
                }
            }
        }
    } else {
        // When functionality is not empty
        if (!empty($customContents)) {
            foreach ($customContents as $index => $content) {
                ?>
                <div class="content-wrapper nextPage">
                <?= $content->content ?>
                </div>
                <?php
            }
        }
    }
    ?>

    <?php
//    if (in_array($num, $detailperpage) || in_array($totalItems, $allNumbers)):
    ?>
    <!--        <div class="nextPage">
    <?php //= $this->render('sign', ['master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_FUNCTIONALITY)])   ?>
            </div>-->
    <?php // else:   ?>
    <!--<br/>< br/>-->
    <?php //= $this->render('sign', ['master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_FUNCTIONALITY)]) ?>
    <?php // endif;   ?>

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
        $needsNextPageDiv = (in_array($num, $detailperpage) || in_array($totalItems, $allNumbers)) || $isFatFailOrComplete;
    }

    // Render signature
    if ($needsNextPageDiv):
        ?>
        <div class="nextPage">
        <?= $this->render('sign', ['formStatus' => $funcForm, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_FUNCTIONALITY)]) ?>
        </div>
    <?php else: ?>
        <?= $this->render('sign', ['formStatus' => $funcForm, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_FUNCTIONALITY)]) ?>
<?php endif; ?>
</body>
