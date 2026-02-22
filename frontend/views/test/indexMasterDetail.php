<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use frontend\models\test\TestMain;
use frontend\models\test\TestMaster;
use yii\bootstrap4\ActiveForm;
use common\models\myTools\MyCommonFunction;

$this->title = $master->tc_ref;
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $project->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $panel->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .centered-text{
        height: 100%;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-link {
        text-decoration: none;
    }
    .card-link:active {
        transform: scale(0.95);
        text-decoration: none !important;
    }


</style>

<div class="index-master-detail">

    <div class="row">
        <div class="col-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Test Detail:</legend>
                <div class="row">
                    <div class="col-md-6">
                        <table class="m-0 p-0">
                            <tr>
                                <td>Project</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $project->name ?></td>
                            </tr>
                            <tr>
                                <td>TC Ref</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $master->tc_ref ?></td>
                            </tr>
                            <tr>
                                <td>Switchboard</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $panel->panel_description ?></td>
                            </tr>
                            <tr>
                                <td>Client</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $main->client ?></td>
                            </tr>
                            <tr>
                                <td>Elec Consul.</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $main->elec_consultant ?></td>
                            </tr>
                            <tr>
                                <td>Elec Contra.</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $main->elec_contractor ?></td>
                            </tr>
                            <tr>
                                <td>Testing Date</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= MyFormatter::asDate_Read($master->date) ?></td>
                            </tr>
                            <tr>
                                <td>Tester</td>
                                <td>&nbsp;:&nbsp;</td>
                                <td><?= $master->tested_by ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="w-100">
                            <?= $master->detail ?>
                        </div>
                    </div>
                </div>
                <div class='col-12'>
                    <div class="row">
                        <div class='col text-right p-0 pt-3'>
                            <?= Html::a('MS WORD <i class="far fa-file-pdf"></i>', yii\helpers\Url::to(['/test/testing/generate-ats-docx', 'masterId' => $master->id, 'mainId' => $main->id]), ['class' => 'btn btn-sm btn-primary', 'target' => '_blank']) ?>
                            <?= Html::a('PDF <i class="far fa-file-pdf"></i>', yii\helpers\Url::to(['/test/testing/generate-report', 'masterId' => $master->id, 'mainId' => $main->id]), ['class' => 'btn btn-sm btn-primary', 'target' => '_blank']) ?>
                            <?=
                            Html::a("Update Test Detail", "javascript:", [
                                'title' => "$main->test_type",
                                "value" => yii\helpers\Url::to(['/test/testing/update-master', 'id' => $master->id]),
                                "class" => "modalButton btn btn-sm btn-success",
                                'data-modaltitle' => "Update " . $main->panel->panel_description
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Test Form:</legend>
                <div class="row">
                    <?php
                    $success = '<span class="badge badge-success">Completed</span>';
                    $fails = '<span class="badge badge-danger">Not Complete</span>';
                    ?>
                    <?php if ($attendance): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/attendanceform/index', 'id' => $attendance->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Attendance&nbsp;&nbsp; 
                                                <?= $attendance->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($insuhipot): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/insuhipot/index', 'id' => $insuhipot->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Insulation and Hipot&nbsp;&nbsp; 
                                                <?= $insuhipot->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($dimension): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/dimension/index', 'id' => $dimension->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Dimension Check&nbsp;&nbsp; 
                                                <?= $dimension->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($visualpaint): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/visualpaint/index', 'id' => $visualpaint->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Visual Inspection&nbsp;&nbsp; 
                                                <?= $visualpaint->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($component): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/component/index', 'id' => $component->id, 'addComponentForm' => false]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Component Check&nbsp;&nbsp; 
                                                <?= $component->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($ats): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/ats/index', 'id' => $ats->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                ATS Functionality&nbsp;&nbsp; 
                                                <?= $ats->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($functionality): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/functionality/index', 'id' => $functionality->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Functionality&nbsp;&nbsp; 
                                                <?= $functionality->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($punchlist): ?>
                        <div class="col-md-6 col-xl-4 pb-3">
                            <a href="<?= \yii\helpers\Url::to(['/test/punchlist/index', 'id' => $punchlist->id]) ?>">
                                <div class="card card-link">
                                    <div class="card-body">
                                        <div class="vmiddle">
                                            <p class="card-title bold centered-text text-dark">
                                                Punchlist&nbsp;&nbsp; 
                                                <?= $punchlist->status0->badge ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="row float-right pr-3">
                    <?php
                    if ($master->status == \frontend\models\test\RefTestStatus::STS_READY_FOR_TESTING && $punchlist->status == \frontend\models\test\RefTestStatus::STS_READY_FOR_TESTING) {
                        echo Html::a('Start Test', yii\helpers\Url::to(['/test/testing/start-test', 'id' => $master->id]), ['class' => 'btn btn-sm btn-success mr-1']);
                    }

                    echo Html::a("Add Relevant Form", "javascript:", [
                        'title' => "Add form relevant to this test",
                        "value" => yii\helpers\Url::to(['/test/testing/add-form-to-test', 'id' => $master->id]),
                        "class" => "modalButtonMedium btn btn-sm btn-success",
                        'data-modaltitle' => 'Add Relevant Form'
                    ]);
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
</div>