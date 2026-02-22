<?php

use yii\helpers\Html;

$this->params['breadcrumbs'][] = "Reconfigure Task Weight";
?>
<style>
    .borderTopBottom {
        border-top: 1px solid black !important;
        border-bottom: 1px solid black !important;
    }
    .spacer-row {
        height: 15px;
    }
</style>
<div class="task-view">
    <div class="row">
        <div class="col-12 order-md-1 mt-2">
            <?=
            Html::a('Update Section Weight (%) <i class="far fa-edit"></i>',
                    ['/reporting/update-project-q-detail'],
                    ['class' => 'btn btn-success align-right']
            );
            ?>
            <?php
            $refProjectTypes = $refProjectTypes;
            if ($refProjectTypes) {
                array_multisort(array_column($refProjectTypes, "code"), SORT_ASC, $refProjectTypes);
                ?> 
                <table class="table table-sm table-striped table-bordered col-lg-8 col-md-12 col-sm-12 mt-3">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center col-1 br" rowspan="2">Task</th>
                            <th class="text-center col-1" colspan="<?= count($refProjectTypes) ?>">Project Type</th>
                        </tr>
                        <tr>
                            <!-- Project Type Headers -->
                            <?php foreach ($refProjectTypes as $refProjectType) { ?>
                                <th class="text-center col-1">
                                    <div class="task-container">
                                        <div class="task-name">
                                            <?=
                                            Html::a("$refProjectType->project_type_name(%)", "javascript:", [
                                                'title' => "Edit Task Weight",
                                                "value" => yii\helpers\Url::to(['update-task-weight', 'paneltype' => $refProjectType->code]),
                                                "class" => "modalButton",
                                                'data-modaltitle' => "Edit Task Weight"
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                </th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Fabrication Tasks -->
                        <?php foreach ($refFabTask as $refFabItems) { ?>
                            <tr class="text-center col-1 ">
                                <td class="br"><?= Html::encode($refFabItems->name) ?></td>
                                <?php foreach ($refProjectTypes as $refProjectType) { ?> 
                                    <td class="text-right">
                                        <?php foreach ($refTaskWeightFab as $attribute) { ?>                                   
                                            <?php
                                            if ($attribute->task_code == $refFabItems->code && $attribute->panel_type == $refProjectType->code) {
                                                echo $attribute->task_weight;
                                            }
                                            ?>                                    
                                        <?php } ?>                                        
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <tr class="spacer-row" style="height: 1px"></tr>
                        <tr class="text-center col-1 borderTopBottom">
                            <th class="borderTopBottom br">Total Assigned Weight (Fabrication)</th>
                            <?php foreach ($refProjectTypes as $refProjectType) { ?>
                                <th class="text-right borderTopBottom">
                                    <?= Html::encode($refProjectType->fab_dept_percentage) ?> %
                                </th>
                            <?php } ?>
                        </tr>
                        <tr class="spacer-row" ></tr>
                        <!-- Electrical Tasks -->
                        <?php foreach ($refElecTask as $refElecItems) { ?>
                            <tr class="text-center col-1 ">
                                <td class="br"><?= Html::encode($refElecItems->name) ?></td>
                                <?php foreach ($refProjectTypes as $refProjectType) { ?> 
                                    <td class="text-right">
                                        <?php foreach ($refTaskWeightElec as $attribute) { ?>                                   
                                            <?php
                                            if ($attribute->task_code == $refElecItems->code && $attribute->panel_type == $refProjectType->code) {
                                                echo $attribute->task_weight;
                                            }
                                            ?>                                    
                                        <?php } ?>                                        
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                        <tr class="spacer-row" style="height: 1px"></tr>
                        <tr class="text-center col-1 borderTopBottom">
                            <th class="borderTopBottom br">Total Assigned Weight (Electrical)</th>
                            <?php foreach ($refProjectTypes as $refProjectType) { ?>
                                <th class="text-right borderTopBottom">
                                    <?= Html::encode($refProjectType->elec_dept_percentage) ?> %                                    
                                </th>
                            <?php } ?>
                        </tr>

                        <!-- Overall Totals -->
                        <tr class="text-center col-1">
                            <th class="borderTopBottom br">Overall Total</th>
                            <?php foreach ($refProjectTypes as $refProjectType) { ?>
                                <th class="text-right borderTopBottom">
                                    <?= Html::encode($refProjectType->fab_dept_percentage + $refProjectType->elec_dept_percentage) ?> %
                                </th>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
                <?php
            } else {
                echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
            }
            ?>
        </div>
    </div>
</div>
