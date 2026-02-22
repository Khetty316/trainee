<?php

use yii\helpers\Html;
use frontend\models\projectproduction\fabrication\RefProjProdTaskFab;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use common\models\myTools\MyFormatter;

$this->registerCssFile('@web/css/topPerformanceFabElecStaffStyle.css');
?> 

<div class="row">
    <!-- FABRICATION PERFORMANCE -->
    <div class="col-lg-6 col-md-12 col-sm-12 mt-1">
        <div class="performance-staff-card fabrication-card">
            <div class="performance-staff-header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h4 class="header-title">
                        Fabrication Performance 
                        <small class="p-0 m-0">from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?></small>
                    </h4>
                </div>
            </div>
            <div class="performance-staff-body">    
                <?php if (!empty($topFabStaffOverall)): ?>
                    <?php
                    // Check if any of top 3 have performance > 0
                    $hasValidTop3 = false;
                    foreach ($topFabStaffOverall as $index => $staff) {
                        if ($index < 3 && floatval($staff['totalPerformance']) > 0) {
                            $hasValidTop3 = true;
                            break;
                        }
                    }
                    ?>

                    <?php if ($hasValidTop3): ?>
                        <div class="top-3-performer-overall-list">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="title-top-performer">Top Performer</div>  
                                </div>

                                <?php foreach ($topFabStaffOverall as $index => $staff): ?>
                                    <?php if ($index < 3 && floatval($staff['totalPerformance']) > 0): ?>
                                        <div class="col-lg-4 col-md-4 col-sm-6 mt-2">
                                            <div class="performer-overall-card rank-<?= $index + 1 ?>">
                                                <div class="rank-badge-lg">
                                                    <?php if ($index === 0): ?>
                                                        <i class="fas fa-crown"></i>
                                                    <?php elseif ($index === 1): ?>
                                                        <i class="fas fa-medal"></i>
                                                    <?php elseif ($index === 2): ?>
                                                        <i class="fas fa-award"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="staff-info">
                                                    <div class="staff-name-lg"><?= Html::encode($staff['fullname']) ?></div>
                                                    <div class="staff-metrics">
                                                        <span class="performance-percentage-lg"><?= number_format($staff['percentage'], 2) ?>%</span>
                                                        <span class="performance-amount-lg">RM <?= number_format($staff['totalPerformance'], 2) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($topFabricationStaffByTask)): ?>
                    <?php
                    $taskCodes = array_keys($topFabricationStaffByTask);
                    $tasks = RefProjProdTaskFab::find()
                            ->where(['code' => $taskCodes, 'active_sts' => 1])
                            ->indexBy('code')
                            ->all();

                    uksort($topFabricationStaffByTask, function ($a, $b) use ($tasks) {
                        $sortA = isset($tasks[$a]) ? $tasks[$a]->sort : 999;
                        $sortB = isset($tasks[$b]) ? $tasks[$b]->sort : 999;
                        return $sortA <=> $sortB;
                    });
                    ?>

                    <?php foreach ($topFabricationStaffByTask as $taskCode => $staffList): ?>
                        <?php
                        $taskModel = RefProjProdTaskFab::find()->where(['code' => $taskCode, 'active_sts' => 1])->one();
                        $taskName = $taskModel ? $taskModel->name : null;
                        if ($taskName !== null) {
                            ?>
                            <details class="task-group">
                                <summary class="task-summary">
                                    <span class="task-badge">
                                        <?= Html::encode($taskName) ?>
                                    </span>
                                </summary>
                                <div class="table-responsive">
                                    <table class="performers-table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="text-center">Rank</th>
                                                <th rowspan="2" class="text-left">Staff Member</th>
                                                <th colspan="2" class="text-center">Contribution</th>
                                            </tr>
                                            <tr>
                                                <th class="text-right">Percentage (%)</th>
                                                <th class="text-right">Value (RM)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staffList as $index => $staff): ?>
                                                <?php
                                                $amount = floatval($staff['amount']);
                                                $rankClass = ($amount == 0.00) ? 'rank-0' : 'rank-' . ($index + 1);
                                                ?>
                                                <tr class="<?= $rankClass ?>">
                                                    <td class="rank-cell">
                                                        <div class="rank-badge">
                                                            <?php
                                                            if ($index === 0 && $amount > 0) {
                                                                echo '<i class="fas fa-crown"></i>';
                                                            } elseif ($index === 1 && $amount > 0) {
                                                                echo '<i class="fas fa-medal"></i>';
                                                            } elseif ($index === 2 && $amount > 0) {
                                                                echo '<i class="fas fa-award"></i>';
                                                            } else {
                                                                echo $index + 1;
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td><div class="staff-name"><?= Html::encode($staff['fullname']) ?></div></td>
                                                    <td class="text-right"><span class="performance-value performance-percentage"><?= number_format($staff['percentage'], 2) ?></span></td>
                                                    <td class="text-right"><span class="performance-value performance-amount"><?= number_format($staff['amount'], 2) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        <?php } ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: #9ca3af;">
                        <i class="fas fa-tools" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <h5 style="color: #6b7280;">No Fabrication Data Available</h5>
                        <p>Adjust your filters to view performance metrics</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ELECTRICAL PERFORMANCE -->
    <div class="col-lg-6 col-md-12 col-sm-12 mt-1">
        <div class="performance-staff-card electrical-card">
            <div class="performance-staff-header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="header-title">
                        Electrical Performance 
                        <small class="p-0 m-0">from <?= MyFormatter::asDate_Read($model->dateFrom) ?> to <?= MyFormatter::asDate_Read($model->dateTo) ?></small>
                    </h4>
                </div>
            </div>
            <div class="performance-staff-body">    
                <?php if (!empty($topElecStaffOverall)): ?>
                    <?php
                    $hasValidTop3 = false;
                    foreach ($topElecStaffOverall as $index => $staff) {
                        if ($index < 3 && floatval($staff['totalPerformance']) > 0) {
                            $hasValidTop3 = true;
                            break;
                        }
                    }
                    ?>

                    <?php if ($hasValidTop3): ?>
                        <div class="top-3-performer-overall-list">
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="title-top-performer">Top Performer</div>  
                                </div>

                                <?php foreach ($topElecStaffOverall as $index => $staff): ?>
                                    <?php if ($index < 3 && floatval($staff['totalPerformance']) > 0): ?>
                                        <div class="col-lg-4 col-md-4 col-sm-6 mt-2">
                                            <div class="performer-overall-card rank-<?= $index + 1 ?>">
                                                <div class="rank-badge-lg">
                                                    <?php if ($index === 0): ?>
                                                        <i class="fas fa-crown"></i>
                                                    <?php elseif ($index === 1): ?>
                                                        <i class="fas fa-medal"></i>
                                                    <?php elseif ($index === 2): ?>
                                                        <i class="fas fa-award"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="staff-info">
                                                    <div class="staff-name-lg"><?= Html::encode($staff['fullname']) ?></div>
                                                    <div class="staff-metrics">
                                                        <span class="performance-percentage-lg"><?= number_format($staff['percentage'], 2) ?>%</span>
                                                        <span class="performance-amount-lg">RM <?= number_format($staff['totalPerformance'], 2) ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (!empty($topElectricalStaffByTask)): ?>
                    <?php
                    $taskCodes = array_keys($topElectricalStaffByTask);
                    $tasks = RefProjProdTaskElec::find()
                            ->where(['code' => $taskCodes, 'active_sts' => 1])
                            ->indexBy('code')
                            ->all();

                    uksort($topElectricalStaffByTask, function ($a, $b) use ($tasks) {
                        $sortA = isset($tasks[$a]) ? $tasks[$a]->sort : 999;
                        $sortB = isset($tasks[$b]) ? $tasks[$b]->sort : 999;
                        return $sortA <=> $sortB;
                    });
                    ?>

                    <?php foreach ($topElectricalStaffByTask as $taskCode => $staffList): ?>
                        <?php
                        $taskModel = RefProjProdTaskElec::find()->where(['code' => $taskCode, 'active_sts' => 1])->one();
                        $taskName = $taskModel ? $taskModel->name : null;
                        if ($taskName !== null) {
                            ?>
                            <details class="task-group">
                                <summary class="task-summary">
                                    <span class="task-badge">
                                        <?= Html::encode($taskName) ?>
                                    </span>
                                </summary>
                                <div class="table-responsive">
                                    <table class="performers-table">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="text-center">Rank</th>
                                                <th rowspan="2" class="text-left">Staff Member</th>
                                                <th colspan="2" class="text-center">Contribution</th>
                                            </tr>
                                            <tr>
                                                <th class="text-right">Percentage (%)</th>
                                                <th class="text-right">Value (RM)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staffList as $index => $staff): ?>
                                                <?php
                                                $amount = floatval($staff['amount']);
                                                $rankClass = ($amount == 0.00) ? 'rank-0' : 'rank-' . ($index + 1);
                                                ?>
                                                <tr class="<?= $rankClass ?>">
                                                    <td class="rank-cell">
                                                        <div class="rank-badge">
                                                            <?php
                                                            if ($index === 0 && $amount > 0) {
                                                                echo '<i class="fas fa-crown"></i>';
                                                            } elseif ($index === 1 && $amount > 0) {
                                                                echo '<i class="fas fa-medal"></i>';
                                                            } elseif ($index === 2 && $amount > 0) {
                                                                echo '<i class="fas fa-award"></i>';
                                                            } else {
                                                                echo $index + 1;
                                                            }
                                                            ?>
                                                        </div>
                                                    </td>
                                                    <td><div class="staff-name"><?= Html::encode($staff['fullname']) ?></div></td>
                                                    <td class="text-right"><span class="performance-value performance-percentage"><?= number_format($staff['percentage'], 2) ?></span></td>
                                                    <td class="text-right"><span class="performance-value performance-amount"><?= number_format($staff['amount'], 2) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        <?php } ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: #9ca3af;">
                        <i class="fas fa-bolt" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.2;"></i>
                        <h5 style="color: #6b7280;">No Electrical Data Available</h5>
                        <p>Adjust your filters to view performance metrics</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.performance-staff-card').forEach(card => {
            const firstTask = card.querySelector('.task-group');
            if (firstTask) {
                firstTask.open = true;
            }
        });
    });
</script>