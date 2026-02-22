<?php

use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\RefProjectItemUnit;
use common\models\myTools\MyFormatter;

$this->title = $model->project_production_panel_code;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = ['label' => $model->projProdMaster->project_production_code, 'url' => ['view-production-main', 'id' => $model->proj_prod_master]];
$this->params['breadcrumbs'][] = $this->title;

//$model = new frontend\models\ProjectProduction\ProjectProductionPanels();
$panelItems = $model->projectProductionPanelItems;
array_multisort(array_column($panelItems, "sort"), SORT_ASC, $panelItems);
echo yii\jui\AutoComplete::widget(['options' => ['class' => 'hidden']]);
?>

<div class="project-qpanels-view">
    <div class="row">
        <h3 class="col-xs-12 col-xl-9"><?= $model->panel_description ?></h3>
    </div>
    <div class="row">
        <div class="col-md-5 order-md-2">
            <?= $this->render("../materialbq/_detailViewProjProdDetail", ['projProdMaster' => $model->projProdMaster, 'panel' => $model]) ?>
        </div>
        <div class="col-md-7 order-md-1"> 
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Items List:</legend>
                <?php
                $form = ActiveForm::begin([
                            'id' => 'myForm',
                            'options' => ['autocomplete' => 'off']
                ]);
                ?>
                <table class="table table-sm table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Description</th>
                            <th class="col-2 text-right">Qty</th>
                            <th class="col-2">Unit</th>
                        </tr>
                    </thead>
                    <tbody id='divItems'>
                        <?php
                        $unitList = RefProjectItemUnit::getDropDownList();
                        $items = $model->projectProductionPanelItems;
                        foreach ($items as $item) {
                            ?>
                            <tr>
                                <td><?= $item->item_description ?></td>
                                <td class="text-right"><?= MyFormatter::asDecimal2($item->quantity) ?></td>
                                <td><?= $item->unitCode->unit_name_single ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                ActiveForm::end();
                ?>
            </fieldset>
        </div>
    </div>
</div>
