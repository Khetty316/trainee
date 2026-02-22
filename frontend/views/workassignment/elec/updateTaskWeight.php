<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
?>
<div class="task-weight-form">
    <?php
    $form = ActiveForm::begin([
                'id' => 'myFinalizeForm',
    ]);
    ?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 mt-2">
            <h6>Panel's Code: <?= $panel->project_production_panel_code ?></h6>
            <?=
            $this->render('_elecTaskWeight', [
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
        var totalTaskWeightErrors = $('.elec-error-message:contains("Total weight cannot exceed 100%")');
        if (totalTaskWeightErrors.length > 0) {
            return false;
        }
        $("#myFinalizeForm").submit();
    }
</script>
