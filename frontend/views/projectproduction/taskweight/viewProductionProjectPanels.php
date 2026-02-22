<?php

use yii\helpers\Html;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Panel Task Weight'];
$this->params['breadcrumbs'][] = ['label' => 'Project List', 'url' => ['index-production-project-list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-view">
    <div class="row">
        <h4 class="col-12">
            <?= Html::a($model->project_production_code . ' <i class="fas fa-external-link-square-alt fa-sm"></i>', "javascript:void(0)", ['class' => 'modalButtonMedium', 'value' => '/production/production/ajax-view-project-detail?id=' . $model->id]) ?>
        </h4>
        <h6 class="col-12"><?= Html::encode($model->name) ?></h6>
    </div>
    <div class="row">
        <div class="col-12 order-md-1">
            
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Panel List:</legend>
                <div class="table-responsive">
                <?php
                $panels = $model->projectProductionPanels;
                $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
                $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
                if ($panels) {
                    array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                    ?> 
                    <table class="table table-sm table-striped table-bordered">
                        <thead class="thead-light">
                            <tr class=" ">                                
                                <th class="text-center" rowspan="2"></th>
                                <th class="tdnowrap text-center align-top" rowspan="2">#</th>
                                <th class="align-top" rowspan="2">Panel's Code</th>
                                <th class="align-top" rowspan="2">Panel's Name</th>
                                <th class="text-center" colspan="<?= count($refFabTask) ?>">Fabrication</th>
                                <th class="text-center" colspan="<?= count($refElecTask) ?>">Electrical</th>                              
                            </tr>
                            <tr>
                                <?php
                                foreach ((array) $refFabTask as $key => $fabTask) {
                                    echo "<th class='text-center'> $fabTask->name (%)</th>";
                                }
                                foreach ((array) $refElecTask as $key => $elecTask) {
                                    echo "<th class='text-center'> $elecTask->name (%)</th>";
                                }
                                ?>  
                            </tr>
                        </thead>
                        <tbody id="itemDisplayTable">
                            <?php
                            foreach ($panels as $key => $panel) {
                                $fabProgress = $panel->getFabTaskProgressStatus(); 
                                $elecProgress = $panel->getElecTaskProgressStatus();
                                $isFinalized = !empty($panel->finalized_at . $panel->design_completed_at);
                                $fabTaskWeights = ProductionFabTasks::getFabTaskValue($panel->id);
                                $elecTaskWeights = ProductionElecTasks::getELecTaskValue($panel->id);
                                ?>

                                <tr>
                                    <td class="text-center">
                                        <?php
                                        if (!empty($fabProgress) || !empty($elecProgress)) {
                                            echo Html::a("<i class='far fa-edit'></i>", "javascript:", [
                                                'title' => "Edit Task Weight",
                                                "value" => yii\helpers\Url::to(['update-task-weight', 'id' => $panel->id]),
                                                "class" => "modalButton",
                                                'data-modaltitle' => "Edit Task Weight"
                                            ]);
                                        }
                                        ?>
                                    </td>     
                                    <td class="text-right px-2"><?= $key + 1 ?></td>
                                    <td class="" style="width:12%;">
                                        <?= $panel->project_production_panel_code ?>
                                    </td>
                                    <td class="" style="width:15%;">
                                        <?= Html::encode($panel->panel_description) ?>
                                    </td>

                                    <!-- Fabrication tasks -->
                                    <?php
                                    if (!empty($fabProgress)) {
                                        foreach ((array) $refFabTask as $key => $fabTask) {
                                            $code = $fabTask->code;
                                            echo "<td class='text-right col-1'>";

                                            if (!empty($fabProgress[$code])) {
                                                foreach ($fabTaskWeights as $taskCode => $weight) {
                                                    if ($taskCode === $fabTask->code && !empty($fabProgress[$code])) {
                                                        echo $weight;
                                                    }
                                                }
                                            } else {
                                                echo "-";
                                            }
                                            echo "</td>";
                                        }
                                    } else if ($isFinalized) {
                                        echo "<td colspan='" . sizeof($refFabTask) . "' class='text-center'> -- No Task Yet -- </td>";
                                    } else {
                                        echo "<td colspan='" . sizeof($refFabTask) . "' class='text-center'> -- Not Confirm Yet -- </td>";
                                    }
                                    ?>

                                    <!-- Electrical tasks -->
                                    <?php
                                    if (!empty($elecProgress)) {
                                        foreach ((array) $refElecTask as $key => $elecTask) {
                                            $code = $elecTask->code;
                                            echo "<td class='text-right col-1'>";
                                            if (!empty($elecProgress[$code])) {
                                                foreach ($elecTaskWeights as $taskCode => $weight) {
                                                    if ($taskCode === $elecTask->code && !empty($elecProgress[$code])) {
                                                        echo $weight;
                                                    }
                                                }
                                            } else {
                                                echo "-";
                                            }
                                            echo "</td>";
                                        }
                                    } else if ($isFinalized) {
                                        echo "<td colspan='" . sizeof($refElecTask) . "' class='text-center'> -- No Task Yet -- </td>";
                                    } else {
                                        echo "<td colspan='" . sizeof($refElecTask) . "' class='text-center'> -- Not Confirm Yet -- </td>";
                                    }
                                    ?> 
                                </tr>
                                <?php
                            }
                            ?>        
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                }
                ?>
                </div>
            </fieldset>
            
        </div>
    </div>
</div>