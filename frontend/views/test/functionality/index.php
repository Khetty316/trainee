<?php

use yii\helpers\Html;
use frontend\models\test\RefTestStatus;
use frontend\models\test\TestMaster;

$this->title = 'Functionality List';
$this->params['breadcrumbs'][] = ['label' => "Test Project List", 'url' => ['/test/testing/index-project-lists']];
$this->params['breadcrumbs'][] = ['label' => 'Test Project Details', 'url' => ['/test/testing/index-project', 'id' => $master->testMain->panel->projProdMaster->id]];
$this->params['breadcrumbs'][] = ['label' => 'Test Panel Details', 'url' => ['/test/testing/index-panel', 'id' => $master->testMain->panel->id]];
$this->params['breadcrumbs'][] = ['label' => $master->tc_ref, 'url' => ["/test/testing/index-master-detail", 'id' => $master->id]];
$this->params['breadcrumbs'][] = $this->title;

$currSts = ($model->status != RefTestStatus::STS_READY_FOR_TESTING && $model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) ? 1 : 0;
?>

<div class="test-detail-functionality-index px-3">
    <div class="col-12">
        <div class="row justify-content-between">
            <div>
                <h3><?= Html::encode($this->title) ?></h3>
            </div>
            <div>
                <?php
                if ($model->status == RefTestStatus::STS_SETUP) {
                    echo Html::a('Add Table <i class="fa fa-plus"></i>', "javascript:", [
                        'title' => "Add a new table",
                        "value" => yii\helpers\Url::to(['add-detail', 'id' => $model->id]),
                        "class" => "modalButton btn btn-success",
                        'data-modaltitle' => "Add Table"
                    ]);
                } else if ($model->status != RefTestStatus::STS_SETUP && $model->status != RefTestStatus::STS_READY_FOR_TESTING) {
                    echo $this->render('..\_formModalAddPunchlist', [
                        'id' => $master->id,
                        'formType' => TestMaster::CODE_FUNCTIONALITY
                    ]);
                }
                if (!$functionalities) {
                    echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
                }
                ?>
            </div>
        </div>
    </div>

    <div class="col-12">

        <div class="row">
            <?php
            if ($model->status != RefTestStatus::STS_FAIL && $model->status != RefTestStatus::STS_COMPLETE) {

                foreach ($functionalities as $key => $data) {
                    $detail = $data['detail'];
                    $functionality = $data['items'];
                    ?>
                    <div class="mt-5 mb-3">
                        <h5 class="mb-0">Point of Test: 
                            <span>
                                <span><?= $detail->pot0->name ?> - <?= $detail->pot_val ?></span>
                                <?= Html::a('Edit <i class="far fa-edit"></i>', ["edit-functionality-list", 'id' => $key], ['class' => 'btn btn-sm btn-success']) ?>
                                <?= Html::a('Duplicate <i class="far fa-copy"></i>', 'javascript:void(0)', ['class' => 'btn btn-sm btn-success', 'onclick' => 'duplicateTable(' . $detail->id . ',' . $model->id . ')']) ?>
                                <?=
                                Html::a('Delete <i class="fas fa-trash"></i>', ['delete-table', 'detailId' => $detail->id, 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-danger',
                                    'data' => [
                                        'confirm' => 'Delete this table?',
                                        'method' => 'post',
                                    ],
                                ])
                                ?>
                            </span>
                        </h5>
                    </div>
                    <table class="table table-sm text-center table-bordered">
                        <thead>
                            <tr>
                                <th class="vmiddle" rowspan="2">No.</th>
                                <th class="vmiddle" rowspan="2">Feeder Tag No.</th>
                                <th rowspan="1" colspan="4">Output</th>
                                <th rowspan="2"></th>
                            </tr>
                            <tr>
                                <th rowspan="1">Voltage At Power</br>Terminal (V)</th>
                                <th rowspan="1">Pass / Fail</br> / Not Available</th>
                                <th rowspan="1">Wiring</br>Termination</br>Connection</th>
                                <th rowspan="1">Pass / Fail</br> / Not Available</th>
                            </tr>
                        </thead>
                        <tbody  class="sortableTable">
                            <?php
                            foreach ($functionality as $val => $function) {
                                ?>
                                <tr data-functionality-id="<?= $function->id ?>" data-detail-id="<?= $key ?>">
                                    <td><?= $function->no ?></td>
                                    <td><?= $function->feeder_tag ?></td>
                                    <td><?= $function->voltage_apt ?></td>
                                    <td><?= $function->voltage_apt_sts == 1 ? '<i class="fas fa-check text-success"></i>' : ($function->voltage_apt_sts === null ? '<span class="text-warning">N/A</span>' : '<i class="fas fa-times text-danger"></i>') ?></td>
                                    <td><?= $function->wiring_tc ?></td>
                                    <td><?= $function->wiring_tc_sts == 1 ? '<i class="fas fa-check text-success"></i>' : ($function->wiring_tc_sts === null ? '<span class="text-warning">N/A</span>' : '<i class="fas fa-times text-danger"></i>') ?></td>
                                    <td><span class="handle disform"><i class="fas fa-grip-lines"></i></span></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
            } else {
                echo $this->render('_viewFunctionality', [
                    'functionalities' => $functionalities
                ]);
            }
            ?>
        </div>
    </div>
    <div class="row">
        <?php
        if ($model->got_custom_content == 1) {
            ?>    
            <div class="col-sm-12 col-md-12 mt-5">
                <div class="mb-3">
                    <h6 style="border-bottom: 2px solid #28a745; padding-bottom: 5px; margin-bottom: 15px;">
                        Custom Content Section <?= Html::a('Edit Custom Content <i class="far fa-edit"></i>', ["add-custom-content", 'id' => $model->id], ['class' => 'btn btn-sm btn-success']) ?>
                    </h6>
                </div>
                <?php
                if (!empty($customContents)) {
                    ?>
                    <?php
                    foreach ($customContents as $index => $content) {
                        ?>
                        <div class="custom-content-display mb-4" style="border: 1px solid #28a745; border-radius: 5px; padding: 15px; background-color: #f8fff9;">
                            <div class="content-wrapper">
                                <?= $content->content ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        } else {
            ?>

            <div class="col-sm-12 col-md-12 mt-5">
                <div class="mb-3">
                    <h6 style="border-bottom: 2px solid #28a745; padding-bottom: 5px; margin-bottom: 15px;">
                        Custom Content Section 
                        <?= Html::a('Add Custom Content <i class="fas fa-plus"></i>', ["add-custom-content", 'id' => $model->id], ['class' => 'btn btn-sm btn-success']) ?>
                    </h6>
                </div>
                <div class="custom-content-display mb-4" style="border: 1px solid #28a745; border-radius: 5px; padding: 15px; background-color: #f8fff9; height: 100px;">
                    <div class="content-wrapper">
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <?php
//            if ($model->status == RefTestStatus::STS_SETUP && $functionalities) {
                if ($model->status == RefTestStatus::STS_SETUP) {
                echo Html::a('Delete Form &nbsp;<i class="fa fa-trash"></i>', ["delete-form", 'id' => $model->id], ['class' => 'float-right btn btn-danger ml-2', 'data-confirm' => 'Delete this form?']);
                echo Html::a('Save and Ready to Test &nbsp;<i class="far fa-clipboard"></i>', ["functionality-status", 'id' => $model->id, 'sts' => RefTestStatus::STS_READY_FOR_TESTING], ['class' => 'float-right btn btn-success ml-2 save-and-status']);
            } else if ($model->status == RefTestStatus::STS_IN_TESTING) {
                echo Html::a('Save and Fail &nbsp;<i class="fas fa-times"></i>', ["functionality-status", 'id' => $model->id, 'sts' => RefTestStatus::STS_FAIL], ['class' => 'float-right btn btn-danger ml-2 save-and-status']);
                echo Html::a('Save and Pass &nbsp;<i class="fas fa-clipboard-check"></i>', ["functionality-status", 'id' => $model->id, 'sts' => RefTestStatus::STS_COMPLETE], ['class' => 'float-right btn btn-success ml-2 save-and-status']);
            }
            if ($model->status == RefTestStatus::STS_READY_FOR_TESTING || $model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                echo Html::a('Revert Form &nbsp;<i class="fas fa-undo"></i>', ["revert-form", 'id' => $model->id], ['class' => 'float-right btn revert btn-danger ml-2', 'data-confirm' => 'Revert this form?']);
            }
            ?>
        </div>
        <div class="col-12">
            <?php
//            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
//                if ($witnessList) {
//                    
            ?>
            <!--<h5>Witnesses</h5>-->
            <?php
//                }
//                if ($witnessList) {
//                    echo $this->render('../__signatureForm', [
//                        'model' => $model,
//                        'witnessList' => $witnessList
//                    ]);
//                }
//            }
            ?>
            <?php if ($witnessList) { ?>
                <h5>Witnesses</h5>
                <?php
            }
            if ($model->status == RefTestStatus::STS_FAIL || $model->status == RefTestStatus::STS_COMPLETE) {
                if ($witnessList) {
                    echo $this->render('../__signatureForm', [
                        'model' => $model,
                        'witnessList' => $witnessList
                    ]);
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.sortableTable').forEach(function (table) {
        new Sortable(table, {
            animation: 90,
            handle: '.handle',
            ghostClass: 'blue-background-class',
            onUpdate: function (evt) {
                var draggedFunctionalityId = evt.from.children[evt.newIndex].getAttribute('data-functionality-id');
                var detailId = evt.from.children[evt.newIndex].getAttribute('data-detail-id');

                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['ajax-reorder-item']) ?>',
                    method: 'POST',
                    dataType: 'html',
                    data: {
                        detailId: detailId,
                        id: draggedFunctionalityId,
                        order: evt.newIndex
                    }
                }).done(function (response) {
                    $("#listTBody").append(response);
                });
            }
        });
    });

    window.onload = function () {
        var status = <?= $currSts ?>;
        if (!status) {
            hideLinks();
        }
    };

    function hideLinks() {
        var linksToHide = document.querySelectorAll('.btn');
        var displayRevertBtn = document.querySelector('.revert');
        var displayAbleBtn = document.querySelectorAll('.able');
        linksToHide.forEach(function (link) {
            if (link.id !== 'newPunchlistBtn') {
                link.style.display = 'none';
            }
        });
        displayRevertBtn.style.display = 'block';
        displayAbleBtn.forEach(function (able) {
            able.style.display = 'block';
        });
    }

    function duplicateTable(detailId, id) {
        if (confirm("Duplicate this table?")) {
            $.ajax({
                url: "<?= \yii\helpers\Url::to(['duplicate-table-ajax']) ?>",
                type: 'post',
                dataType: 'json',
                data: {
                    detailId: detailId,
                    id: id
                },
                success: function (response) {
                    // Handle success response
                },
                error: function (xhr, status, error) {
                    // Handle error response
                }
            });
        }
    }

    $('.save-and-status, .revert').click(function () {
        window.scrollTo(0, 0);
        eraseCookie('jumpToScrollPosition');
    });

    window.addEventListener('beforeunload', function () {
        var currentYOffset = window.pageYOffset;
        setCookie('jumpToScrollPosition', currentYOffset, 1);
    });

    var jumpTo = getCookie('jumpToScrollPosition');
    if (jumpTo !== null && jumpTo !== "undefined") {
        window.scrollTo(0, jumpTo);
        eraseCookie('jumpToScrollPosition');

    }

</script>