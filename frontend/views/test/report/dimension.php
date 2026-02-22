<meta charset="UTF-8">
<?php

use frontend\models\test\RefTestStatus;
use frontend\models\test\TestFormDimension;

$dimensionForm = $model1;
$dimensionProcedures = $model2;
$dimensionList = $model3;
$customContents = $model4;
$showResult = ($dimensionForm->status != RefTestStatus::STS_SETUP && $dimensionForm->status != RefTestStatus::STS_READY_FOR_TESTING) ? 1 : 0;
$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <?php
    $modelProcedures = explode('|', $dimensionProcedures);
    echo '<p>' . (isset($modelProcedures[0]) ? $modelProcedures[0] : '') . '</p>';
    ?>
    <div class="nextPage">
        <p><b>Test Result</b></p>
    </div>

    <table class="table table-sm table-bordered text-center">
        <thead>
            <tr>
                <td width="60%">Panel</td>
                <td class="tableDimen">Subject</td>
                <td class="tableDimen">H (mm)</td>
                <td class="tableDimen">W (mm)</td>
                <td class="tableDimen">D (mm)</td>
            </tr>
        </thead>
        <?php
        $counter = 0;
        if (empty($dimensionList)) {
            for ($i = 0; $i < 2; $i++) {
                ?>
                <tr class="p-0 m-0">
                    <?php if ($showResult) { ?>
                        <td rowspan="4" width="60%"></td>
                    <?php } else { ?> 
                        <td rowspan="3" width="60%"></td>
                    <?php } ?>
                    <td class="tableDimen" height="20">Drawing</td>
                    <td class="tableDimen"></td>
                    <td class="tableDimen"></td>
                    <td class="tableDimen"></td>
                </tr>
                <tr class="p-0 m-0">
                    <td class="tableDimen" height="20">As-built</td>
                    <td class="tableDimen"></td>
                    <td class="tableDimen"></td>
                    <td class="tableDimen"></td>
                </tr>
                <tr class="p-0 m-0">
                    <td class="tableDimen" height="20">Error</td>
                    <td class="tableDimen"></td>
                    <td class="tableDimen"></td>
                    <td class="tableDimen"></td>
                </tr>                            
                <?php if ($showResult) { ?>
                    <tr class="p-0 m-0">
                        <td class="tableDimen" height="20">Result</td>
                        <td class="tableDimen"></td>
                        <td class="tableDimen"></td>
                        <td class="tableDimen"></td>
                    </tr>                            
                <?php } ?> 
                <?php
            }
        } else {
            // Set items per page based on condition
            $itemPerPage = 8; // Default: 8 items per page
            if ($main->test_type === \frontend\models\test\TestMain::TEST_FAT_TITLE) {
                if ($master->status == frontend\models\test\RefTestStatus::STS_FAIL || $master->status == frontend\models\test\RefTestStatus::STS_COMPLETE) {
                    $itemPerPage = 6; // FAT FAIL/COMPLETE: 6 items per page
                } else {
                    $itemPerPage = 7; // FAT Other status: 7 items per page
                }
            }

            foreach ($dimensionList as $key => $dimension) {
                if ($counter % $itemPerPage === 0 && $counter !== 0) {
                    echo '</table>';
                    echo '<div class="nextPage"></div>';
                    echo '<table class="table table-sm table-bordered text-center">';
                    echo '<thead>
                        <tr>
                            <td width="60%">Panel</td>
                            <td class="tableDimen">Subject</td>
                            <td class="tableDimen">H (mm)</td>
                            <td class="tableDimen">W (mm)</td>
                            <td class="tableDimen">D (mm)</td>
                        </tr>
                    </thead>';
                }
                ?>
                <tr class="p-0 m-0 tr_<?= $key ?>">
                    <?php if ($showResult) { ?>
                        <td rowspan="4" width="60%"><br><br><br><?= $dimension->panel_name ?: $panel->panel_description ?></td>
                    <?php } else { ?> 
                        <td rowspan="3" width="60%"><br><br><br><?= $dimension->panel_name ?: $panel->panel_description ?></td>
                    <?php } ?>
                    <td class="tableDimen" height="20">Drawing</td>
                    <td class="tableDimen"><?= $dimension->drawing_h ?></td>
                    <td class="tableDimen"><?= $dimension->drawing_w ?></td>
                    <td class="tableDimen"><?= $dimension->drawing_d ?></td>
                </tr>
                <tr class="p-0 m-0 tr_<?= $key ?>">
                    <td class="tableDimen" height="20">As-built</td>
                    <td class="tableDimen"><?= $dimension->built_h ?></td>
                    <td class="tableDimen"><?= $dimension->built_w ?></td>
                    <td class="tableDimen"><?= $dimension->built_d ?></td>
                </tr>
                <tr class="p-0 m-0 tr_<?= $key ?>">
                    <td class="tableDimen" height="20">Error</td>
                    <td  class="tableDimen"><?= $dimension->error_h ?></td>
                    <td class="tableDimen"><?= $dimension->error_w ?></td>
                    <td class="tableDimen"><?= $dimension->error_d ?></td>
                </tr>                            
                <?php if ($showResult) { ?>
                    <tr class="p-0 m-0 tr_<?= $key ?>">
                        <td class="tableDimen" height="20">Result</td>
                        <td class="tableDimen"><?= ($dimension->res_h === null) ? '' : ($dimension->res_h == TestFormDimension::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                        <td class="tableDimen"><?= ($dimension->res_w === null) ? '' : ($dimension->res_w == TestFormDimension::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                        <td class="tableDimen"><?= ($dimension->res_d === null) ? '' : ($dimension->res_d == TestFormDimension::RESULT_PASS['value'] ? 'Pass' : 'Fail') ?></td>
                    </tr>                            
                <?php } ?> 
                <?php
                $counter++;
            }
        }
        ?>
    </table>

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
// Signature logic
    $totalItems = count($dimensionList);

// Determine if signature needs nextPage div based on your requirements
    $isFatFailOrComplete = ($main->test_type === \frontend\models\test\TestMain::TEST_FAT_TITLE &&
            ($master->status == frontend\models\test\RefTestStatus::STS_FAIL ||
            $master->status == frontend\models\test\RefTestStatus::STS_COMPLETE));

    $needsNextPageDiv = false;

    if (!empty($customContents)) {
        // If customContents exists, only FAT FAIL/COMPLETE gets nextPage
        $needsNextPageDiv = $isFatFailOrComplete;
    } else {
        // Signature placement logic based on item count
        if ($isFatFailOrComplete) {
            // FAT FAIL/COMPLETE: signature on next page every 4 items (but still 6 items per page)
            $needsNextPageDiv = ($totalItems % 4 === 0 && $totalItems > 0);
        } else {
            // Other cases: signature on next page every 6 items (but still 8 items per page)
            $needsNextPageDiv = ($totalItems % 6 === 0 && $totalItems > 0);
        }
    }

// Render signature
    if ($needsNextPageDiv):
        ?>
        <div class="nextPage">
            <?= $this->render('sign', ['formStatus' => $dimensionForm, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_DIMENSION)]) ?>
        </div>
    <?php else: ?>
        <?= $this->render('sign', ['formStatus' => $dimensionForm, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_DIMENSION)]) ?>
    <?php endif; 
     ?>
</body>

