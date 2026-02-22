<?php
//$projProdMaster = frontend\models\ProjectProduction\ProjectProductionMaster::findOne(1);
//$panel = frontend\models\ProjectProduction\ProjectProductionPanels::findOne(1);
?>
<fieldset class="form-group border p-3">
    <legend class="w-auto px-2 m-0">Project Detail:</legend>
    <table class="table table-sm table-striped table-bordered">
        <?php
        if ($projProdMaster) {
            ?>
            <tr>
                <td class="col-3">Project Code</td>
                <td class="col-9"><?= $projProdMaster->project_production_code ?></td>
            </tr>
            <tr>
                <td>Project Name</td>
                <td><?= $projProdMaster->name ?></td>
            </tr>
            <?php
        }
        if (!empty($panel)) {
            ?>
            <tr>
                <td class="col-3">Panel Code</td>
                <td class="col-9"><?= $panel->project_production_panel_code ?></td>
            </tr>
            <tr>
                <td>Panel Description</td>
                <td><?= $panel->panel_description ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
</fieldset>