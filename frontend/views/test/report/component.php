<meta charset="UTF-8">
<?php

use frontend\models\test\RefTestCompType;
use frontend\models\test\TestDetailComponent;

$css = file_get_contents(Yii::getAlias('@app/web/css/testing-report-bootstrap4.css'));
$details = $model1;
$conformities = $model2;
$funcList = $model3;
$accesList = $model4;
$component = $model5;
?>
<style>
<?php echo $css; ?>
</style>
<body>
    <div class="content">
        <table>
            <?php
            $num = 0;
            $itemPerPage = 10;
            if ($master->status == frontend\models\test\RefTestStatus::STS_FAIL || $master->status == frontend\models\test\RefTestStatus::STS_COMPLETE) {
                $itemPerPage = 10;
            }
            foreach ($details as $key => $detail) :
                ?>
                <?php if ($num % $itemPerPage === 0 && $num !== 0) : ?>
                    <div class="nextPage"></div>
                <?php endif; ?>
                <tr>
                    <td><?= $num + 1 ?>.&nbsp;Details of <?= $detail['form']->comp_type == RefTestCompType::TYPE_OTHER ? $detail['form']->comp_name . " " . $detail['form']->pou0->name . '-' . $detail['form']->pou_val : $detail['form']->compType->name . " " . $detail['form']->pou0->name . '-' . $detail['form']->pou_val ?> : <br/>
                        <?php
                        $numAttribute = 0;
                        foreach ($detail['attributetorender'] as $attribute) :
                            ?>
                            <?php if ($numAttribute % 6 === 0 && $numAttribute !== 0) : ?>
                                <br/>
                            <?php endif; ?>
                            <?php if ($attribute == TestDetailComponent::ATTRIBUTE_COMPNAME) : ?>
                                <?php continue; ?>
                            <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_ACCESSORY) : ?>
                                <?= $detail['form']->attributeLabels()["$attribute"] ?> : 
                                <span>
                                    <?php
                                    foreach ($detail['form'][$attribute] as $key => $acsrycode) {
                                        echo $accesList[$acsrycode] ?? null;
                                        if (!empty($detail['form'][$attribute][$key + 1])) {
                                            echo ", ";
                                        }
                                    }
                                    ?>
                                </span>
                            <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_FUNCTIONTYPE) : ?>
                                <?= $detail['form']->attributeLabels()["$attribute"] ?> : <span><?= $funcList[$detail['form'][$attribute]] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                            <?php elseif ($attribute != TestDetailComponent::ATTRIBUTE_POU && $attribute != TestDetailComponent::ATTRIBUTE_POUVAL) : ?>
                                <?php if ($attribute == TestDetailComponent::ATTRIBUTE_RATIOA) : ?>
                                    <?php
                                    $ratioA = $detail['form'][TestDetailComponent::ATTRIBUTE_RATIOA];
                                    $ratioB = $detail['form'][TestDetailComponent::ATTRIBUTE_RATIOB];
                                    ?>
                                    Ratio : <span><?= $ratioA ?>:<?= $ratioB ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_RATIOB):
                                    ?>
                                <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_DIMENSIONA) : ?>
                                    <?php
                                    $dimensionA = $detail['form'][TestDetailComponent::ATTRIBUTE_DIMENSIONA];
                                    $dimensionB = $detail['form'][TestDetailComponent::ATTRIBUTE_DIMENSIONB];
                                    ?>
                                    Dimension : <span><?= $dimensionA ?>mm&nbsp;x&nbsp;<?= $dimensionB ?>mm</span>
                                <?php elseif ($attribute == TestDetailComponent::ATTRIBUTE_DIMENSIONB) : ?>
                                    &nbsp;
                                <?php else : ?>
                                    <?= $detail['form']->attributeLabels()["$attribute"] ?> : <span><?= $detail['form'][$attribute] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php
                            $numAttribute++;
                        endforeach;
                        ?>
                        <?php if (!empty($detail['otheritem'])) : ?>
                            <?php foreach ($detail['otheritem'] as $item) : ?>
                                <?php if ($numAttribute % 6 === 0 && $numAttribute !== 0) : ?>
                                    <br/>
                                <?php endif; ?>
                                <?= $item->attribute ?> : <span><?= $item->value ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                <?php
                                $numAttribute++;
                            endforeach;
                            ?>
                        <?php endif; ?>
                        <br/>
                    </td>
                </tr>
                <?php $num++; ?>
            <?php endforeach; ?>
        </table>
        <?php
        $limit = 1000;
        $allNumbersconf = [];
        $totalItems = count($details);

        for ($number = 5, $increment = 0;
                $number <= $limit;
                $number++) {
            $allNumbersconf[] = $number;
            $increment++;

            if ($increment == 3) {
                $number += 4;
                $increment = 0;
            }
        }
        ?>
        <?php
        $numComp = 0;
        $itemPerPageComp = 45; // Always 45 items per page for table
        $rangeEnd = 45;        // Always 45 rows per page

        if (!empty($details)) {
            ?>
            <div class="nextPage">
            <?php } else { ?>   
                <div>
                <?php } ?>

                <p><b>Components Conformity</b></p>
                <table class="table table-sm table-bordered text-center">
                    <thead>
                        <tr>
                            <td width="5%" class="vmiddle">No.</td>
                            <td width="47%" class="vmiddle">Non-conform Component</td>
                            <td width="47%" class="vmiddle">Remark</td>
                        </tr>
                    </thead>
                    <?php
                    if (empty($conformities)) {
                        // Show 5 empty rows if no data (your original logic)
                        for ($i = 0; $i < 5; $i++) {
                            ?>
                            <tr height="30">
                                <td width="5%"></td>
                                <td width="47%"></td>
                                <td width="47%"></td>
                            </tr>
                            <?php
                        }
                    } else {
                        $totalItems = count($conformities);

                        foreach ($conformities as $key => $conformity) {
                            $currentItemNumber = $key + 1;

                            // Create page break every 45 items
                            if ($currentItemNumber > 45 && ($currentItemNumber - 1) % 45 === 0) {
                                echo '</table></div>';
                                echo '<div class="nextPage">';
                                echo '<table class="table table-sm table-bordered text-center">';
                                echo '<thead>
                        <tr>
                            <td width="5%" class="vmiddle">No.</td>
                            <td width="47%" class="vmiddle">Non-conform Component</td>
                            <td width="47%" class="vmiddle">Remark</td>
                        </tr>
                      </thead>';
                            }
                            ?>

                            <tr>
                                <td width="5%" class="vmiddle text-center"><?= $currentItemNumber ?></td>
                                <td width="47%" class="text-left"><?= $conformity->non_conform ?></td>
                                <td width="47%" class="text-left"><?= $conformity->remark ?></td>
                            </tr>
                            <?php
                        }

                        // Only fill empty rows if we have more than 45 items and need to complete the current page
                        if ($totalItems > 45) {
                            $currentPageItems = $totalItems % 45;
                            if ($currentPageItems > 0) {
                                $emptyRows = 45 - $currentPageItems;
                                for ($i = 0; $i < $emptyRows; $i++) {
                                    ?>
                                    <tr height="30">
                                        <td width="5%"></td>
                                        <td width="47%"></td>
                                        <td width="47%"></td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        // If items <= 45, show only the actual items (no empty rows)
                    }
                    ?>
                </table>
            </div>

            <?php
            // Signature logic - separate from table pagination
            $totalconf = count($conformities);
            if ($totalconf === 0) {
                $totalconf = 5;
            }

            $needsNextPageDiv = false;

            if ($main->test_type === \frontend\models\test\TestMain::TEST_FAT_TITLE) {
                // FAT: signature on next page if 20-45 items
                if ($master->status == frontend\models\test\RefTestStatus::STS_FAIL || $master->status == frontend\models\test\RefTestStatus::STS_COMPLETE) {
                    $needsNextPageDiv = ($totalconf >= 20 && $totalconf <= 45);
                }else{
                    $needsNextPageDiv = ($totalconf >= 35 && $totalconf <= 45);
                }
            } else {
                // Non-FAT: signature on next page if 35-45 items  
                $needsNextPageDiv = ($totalconf >= 35 && $totalconf <= 45);
            }
            if ($needsNextPageDiv):
                ?>
                <div class="nextPage">
                    <?= $this->render('sign', ['formStatus' => $component, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_COMPONENT)]) ?>
                </div>
            <?php else: ?>              
                <?= $this->render('sign', ['formStatus' => $component, 'master' => $master, 'main' => $main, 'witnesses' => frontend\models\test\TestItemWitness::getTestItemWitness($master->id, frontend\models\test\TestMaster::CODE_COMPONENT)]) ?>
            <?php endif; ?>
            </body>

