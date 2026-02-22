<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Stock Outbound', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$stockMaster = new frontend\models\bom\StockOutboundMaster();
?>
<div class="project-production-master-view">
    <div class="row">
        <h3 class="col-12"><?= Html::encode($model->name) ?></h3>
    </div>
    <div class="row">
        <div class="col-xl-6 order-md-1">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Panels:</legend>
                <div class="table-responsive" id="panelList">
                    <?php
                    $formFinalize = ActiveForm::begin([
                        'id' => 'myFinalizeForm',
                        'options' => ['autocomplete' => 'off'],
                        'method' => 'post',
                        'action' => 'finalize-panel?id=' . $model->id
                    ]);

                    if ($panelLists) {
                        array_multisort(array_column($panelLists, "project_production_panel_code"), SORT_ASC, $panelLists);
                        ?>
                        <table class="table table-sm table-striped table-bordered">
                            <thead class="thead-light">
                                <tr class="">
                                    <th class="tdnowrap text-center align-top">#</th>
                                    <th class="align-top">Panel's Code</th>
                                    <th class="align-top">Panel's Name</th>
                                    <th class="tdnowrap text-center align-top">Type</th>
                                    <th class="tdnowrap text-center align-top">QTY</th>
                                    <th class="tdnowrap text-center align-top">B.O.M</th>
                                    <th class="tdnowrap text-center align-top">Stock Out</th>
                                </tr>
                            </thead>
                            <tbody id="itemDisplayTable">
                                <?php
                                foreach ($panelLists as $key => $panel) {
                                    $isNotFullyDispatched = $stockMaster->getStockDispatchStatus($panel->id);
                                    $panelCheckboxOptions = [
                                        'value' => $panel->id,
                                        'class' => 'itemToFinalize',
                                    ];
                                    $isFinalized = !empty($panel->finalized_at . $panel->design_completed_at);

                                    if ($panel->activeStatus == 0) {
                                        echo '<tr>';
                                        echo '<td class="text-right px-2">' . ($key + 1) . '</td>';
                                        echo '<td class="tdnowrap">' . $panel->project_production_panel_code . '</td>';
                                        echo '<td style="">' . $panel->panel_description . '</td>';
                                        echo '<td class="tdnowrap">' . $panel->project_type_name . '</td>';
                                        echo '<td class="text-right px-3 tdnowrap">' . $panel->quantity . " " . $panel->unit_code . ($panel->quantity > 1 ? "S" : "") . '</td>';
                                        echo '<td class="text-center tdnowrap">';
                                        $bomFinalized = (isset($panel->bomMasters[0]) && $panel->bomMasters[0]->finalized_status == 1) ? true : false;
                                        $gotBomDetail = false;
                                        if (isset($panel->bomMasters[0])) {
                                            $bomDetail = frontend\models\bom\BomDetails::find()->where(['bom_master' => $panel->bomMasters[0]->id])->all();
                                            $gotBomDetail = (empty($bomDetail) ? false : true);
                                        }

//                                        if ($bomFinalized) {
                                        $hasFinalizedItem = frontend\models\bom\BomDetails::find()->where(['active_status' => 1, 'is_finalized' => 2, 'inventory_sts' => 2])->all();
                                        if ($hasFinalizedItem) { //atleast one finalized item exists
                                            $bomIconColor = 'ml-2 text-success';
                                        } else {
                                            if ($gotBomDetail) {
                                                $bomIconColor = 'ml-2 text-primary';
                                            } else {
                                                $bomIconColor = 'ml-2 text-warning';
                                            }
                                        }

                                        echo Html::a(
                                                '<i class="fas fa-list"></i>',
                                                ['/bom/index', 'productionPanelId' => $panel->id],
                                                ['title' => 'Bill Of Materials', 'class' => ($bomIconColor)]
                                        );
//                                        echo $bomFinalized ? Html::a(
//                                                        '<i class="fas fa-list"></i>',
////                                                        ['/bom/index', 'productionPanelId' => $panel->id],
//                                                        ['view-bom', 'productionPanelId' => $panel->id],
//                                                        ['title' => 'Bill Of Materials']
//                                                ) : '';
//                                        if($isFinalized){
//                                            echo Html::a(
//                                                    '<i class="fas fa-list"></i>',
//                                                    ['view-bom', 'productionPanelId' => $panel->id],
//                                                    ['title' => 'Bill Of Materials', 'class' => ($bomFinalized?'ml-2 text-primary':'ml-2 text-warning')]
//                                            );
//                                        }

                                        if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal])) {
//                                            if (!empty($panel->stockOutboundMasters) && $bomFinalized) {
                                                if (!empty($panel->stockOutboundMasters)) {
                                                echo Html::a(
                                                        '<i class="fas fa-edit"></i>',
                                                        ['view-material-detail', 'productionPanelId' => $panel->id],
                                                        ['title' => 'Update Material Detail', 'class' => 'ml-2 text-primary']
                                                );
                                            }
                                        }
                                        echo '</td>';
                                        echo '<td class="text-center tdnowrap">';
//                                        if (!empty($panel->stockOutboundMasters) && $bomFinalized) {
                                                                                        if (!empty($panel->stockOutboundMasters)) {

                                            echo Html::a(
                                                    '<i class="fas fa-list"></i>',
                                                    ['update-stock-dispatch', 'productionPanelId' => $panel->id],
                                                    ['title' => 'Stock Dispatch', 'class' => ($isNotFullyDispatched ? 'ml-2 text-warning' : 'ml-2 text-primary')]
                                            );
                                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super])) {
                                                echo Html::a(
                                                        '<i class="fas fa-sliders-h"></i>',
                                                        ['stock-adjustment', 'productionPanelId' => $panel->id],
                                                        ['title' => 'Adjustment', 'class' => 'ml-2 text-danger']
                                                );
                                            }
                                            if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super, AuthItem::ROLE_Stock_Ob_Normal])) {
                                                echo Html::a(
                                                        '<i class="fas fa-redo"></i>',
                                                        ['stock-return', 'productionPanelId' => $panel->id],
                                                        ['title' => 'Return', 'class' => 'ml-2 text-primary']
                                                );
                                            }
                                        } else if ($bomFinalized) {
                                            echo Html::a(
                                                    '<i class="fas fa-plus"></i>',
                                                    ['inventory-validation', 'productionPanelId' => $panel->id],
                                                    ['title' => 'Initiate Stock Outbound', 'data-confirm' => 'Ready for Outbound?', 'data-method' => 'POST']
                                            );
                                        }
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>                       
                        </table>

                        <?php
                    } else {
                        echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                    }
                    ?>
                    <?php
                    ActiveForm::end();
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>
<div class="hidden">
    <?php
    $form = ActiveForm::begin(['id' => 'myHiddenForm']);
    ActiveForm::end();
    ?>
</div>