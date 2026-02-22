<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\models\ProjectProduction\fabrication\RefProjProdTaskFab;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use frontend\models\ProjectProduction\fabrication\ProductionFabTasks;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;
?>
<div class="task-weight-form">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myFinalizeForm',
    ]);
    ?>
    <div class="row"> 
        <?php
        $refFabTask = RefProjProdTaskFab::getAllActiveSorted();
        $refElecTask = RefProjProdTaskElec::getAllActiveSorted();
        $fabTaskWeight = ProductionFabTasks::getFabTaskValue($panel->id);
        $elecTaskWeight = ProductionElecTasks::getELecTaskValue($panel->id);
        ?>
        <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            <h6>Panel's Code: <?= $panel->project_production_panel_code ?></h6>
            <?=
            $this->render('/workassignment/fab/_fabTaskWeight', [
                'refFabTask' => $refFabTask,
                'fabTaskWeight' => $fabTaskWeight,
                'panel' => $panel
            ]);
            ?>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            <?=
            $this->render('/workassignment/elec/_elecTaskWeight', [
                'refElecTask' => $refElecTask,
                'elecTaskWeight' => $elecTaskWeight,
                'panel' => $panel
            ]);
            ?>
            <?= Html::a("Save", "javascript:void(0);", ["class" => "btn btn-success mb-2 mt-0 float-right px-3", "onclick" => "validateAndFinalize();"]); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script>
    function validateAndFinalize() {
        var totalFabTaskWeightErrors = $('.fab-error-message:contains("Total weight cannot exceed 100%")');
        var totalElecTaskWeightErrors = $('.elec-error-message:contains("Total weight cannot exceed 100%")');

        if (totalFabTaskWeightErrors.length > 0 || totalElecTaskWeightErrors.length > 0) {
            return false;
        }
        
        $("#myFinalizeForm").submit();
    }
</script>
