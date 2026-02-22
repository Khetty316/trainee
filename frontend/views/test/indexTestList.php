<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\test\TestMain;
use frontend\models\test\TestMaster;

$this->title = $main->test_type;
$this->params['breadcrumbs'][] = ['label' => "Panel's Test List", 'url' => ['/test/testing/index']];
$this->params['breadcrumbs'][] = ['label' => $panel->panel_description];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class='index-type'>
    <div class="row">
        <div class="col-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Test Related Detail:</legend>
                <div class="row">
                    <div class="col-md-12">
                        <table class="m-0 p-0">
                            <tr>
                                <td>Panel Description</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $panel->panel_description ?></td>
                            </tr>
                            <tr>
                                <td>Client</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $main->client ?></td>
                            </tr>
                            <tr>
                                <td>Elec Consultant</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $main->elec_consultant ?></td>
                            </tr>
                            <tr>
                                <td>Elec Contractor</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $main->elec_contractor ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="text-right col-12 p-0 pt-3">
                    <?=
                    Html::a("Update These Details", "javascript:", [
                        'title' => "Update details on $main->test_type",
                        "value" => yii\helpers\Url::to(['/test/testing/update-main', 'mainId' => $main->id]),
                        "class" => "modalButton btn btn-sm btn-success",
                        'data-modaltitle' => "$panel->panel_description"
                    ]);
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

    <div class='row'>
        <div class='col-12'>
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0"><?= $main->test_type ?> List:</legend>
                <div id='app'>
                    <?php
                    if (!$masters) {
                        echo Html::a("Start A Test", "javascript:", [
                            'title' => "Start a single $main->test_type",
                            "value" => yii\helpers\Url::to(['/test/testing/ajax-form-master', 'mainId' => $main->id]),
                            "class" => "modalButton btn btn-sm btn-success",
                            'data-modaltitle' => "$main->test_type"
                        ]);
                    } else {
                        ?>
                        <table class="table table-sm table-bordered table-striped table-hover m-0 mt-2 col-12 rounded">
                            <thead>
                                <tr>
                                    <th @click="sortTable('tc_ref')" class="search-hover text-primary" colspan="2">Test Certificate</th>
                                </tr>
                                <tr>
                                    <th class="p-1" colspan="2"><input class="form-control" v-model="searchCriteria.tc_ref"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="model in filteredModels" :key="model.id">
                                    <td class="p-1">{{ model.tc_ref }}</td>
                                    <td class="p-1 text-center">
                                        <a :href="'/test/testing/index-master-detail?id=' + model.id" class="btn btn-sm btn-primary ml-1 px-2" title="To Factory Acceptance Test">View</a>
                                        <a v-if="model.status == '<?= TestMaster::STS_FAIL['value'] ?>' || model.status == '<?= TestMaster::STS_COMPLETE['value'] ?>'" 
                                           :value="'/test/testing/refer-master?id=' + model.id" 
                                           class="btn modalButton btn-sm btn-success ml-1 px-2" 
                                           data-modaltitle="<?= $main->test_type ?>"
                                           >Refer
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <?php }
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

</div>

<script>
    window.models = <?= $jsonMasters ?>;
</script>
<script src="\js\vueTable.js"></script>