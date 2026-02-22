<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\test\TestMain;
use frontend\models\test\TestMaster;
use frontend\models\test\RefTestStatus;

$this->title = "$panel->project_production_panel_code";
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $project->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class='index-type'>
    <div class="row">
        <div class="col-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Panel Detail:</legend>
                <div class="row">
                    <div class="col-md-12">
                        <table class="m-0 p-0">
                            <tr>
                                <td>Panel Description</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $panel->panel_description ?></td>
                            </tr>
                            <tr>
                                <td>Panel Finalized At</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= MyFormatter::asDate_Read($panel->finalized_at) ?></td>
                            </tr>
                            <tr>
                                <td>Fabrication Complete Percentage</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $panel->fab_complete_percent ?></td>
                            </tr>
                            <tr>
                                <td>Electrical Complete Percentage</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $panel->elec_complete_percent ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="text-right col-12 p-0 pt-3">
                    <?php
//                    echo Html::a("Update These Details", "javascript:", [
//                        'title' => "Update details on $main->test_type",
//                        "value" => yii\helpers\Url::to(['/test/testing/update-main', 'mainId' => $main->id]),
//                        "class" => "modalButton btn btn-sm btn-success",
//                        'data-modaltitle' => "$panel->panel_description"
//                    ]);
                    ?>
                </div>
            </fieldset>
        </div>
    </div>

    <div class='row'>
        <div class='col-12'>
            <?php
            foreach ($mains as $main) {
                ?>
                <fieldset class="form-group border p-3">
                    <legend class="w-auto px-2 m-0"><?= $main->test_type ?> List:</legend>
                    <div>
                        <?php
                        if (!$main->testMasters) {
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
                                        <th colspan="2">Test Certificate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $masters = $main->testMasters;
                                    $count = count($masters);
                                    foreach ($masters as $number => $master) {
                                        ?>
                                        <tr>
                                            <td class="p-1"><?= $master->tc_ref ?></td>
                                            <td class="p-1 text-center">
                                                <?= Html::a('View ', '/test/testing/index-master-detail?id=' . $master->id, ['class' => 'btn btn-primary btn-sm']) ?>
                                                <?php
                                                if (($number + 1 == $count) && ($master->status == RefTestStatus::STS_COMPLETE || $master->status == RefTestStatus::STS_FAIL)) {
                                                    echo Html::a("Refer", "javascript:", [
                                                        'title' => "Refer to this test",
                                                        "value" => yii\helpers\Url::to(['/test/testing/ajax-refer-master', 'id' => $master->id]),
                                                        "class" => "modalButtonMedium btn btn-sm btn-success",
                                                        'data-modaltitle' => "Select type for new test"
                                                    ]);
//                                                    echo Html::a("Start A Test", "javascript:", [
//                                                        'title' => "Start a single $main->test_type",
//                                                        "value" => yii\helpers\Url::to(['/test/testing/ajax-form-master', 'mainId' => $main->id]),
//                                                        "class" => "modalButton btn btn-sm btn-success",
//                                                        'data-modaltitle' => "$main->test_type"
//                                                    ]);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </fieldset>
            <?php } ?>
        </div>
    </div>

</div>