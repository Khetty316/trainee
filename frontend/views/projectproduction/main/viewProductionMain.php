<?php

use yii\helpers\Html;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

$this->title = $model->project_production_code;
$this->params['breadcrumbs'][] = ['label' => 'Master Project List', 'url' => ['index-production-main']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-view">
    <div class="row">
        <h3 class="col-12"><?= Html::encode($model->name) ?></h3>
        <p class="col-12">
            <?php
            if (Yii::$app->user->can(common\modules\auth\models\AuthItem::ROLE_Director) && $state) {
                echo Html::a('Retract Production <i class="fas fa-backspace"></i>', ['production/production/delete-production-main', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this production?',
                        'method' => 'post',
                    ],
                ]);
            } else if (!$state) {
                echo Html::a('Retract Production <i class="fas fa-backspace"></i>', '#', [
                    'class' => 'btn btn-secondary',
                    'disabled' => true,
                    'style' => 'pointer-events: none;',
                ]);
            }
            ?>
        </p>
    </div>
    <div class="row">
        <div class="col-xl-5 order-md-2">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Project Detail:</legend>
                <div class="table-responsive">
                    <?php
                    echo $this->render("_detailviewProjectProduction", [
                        'model' => $model
                    ]);
                    echo $this->render("_detailviewProjectDocuments", [
                        'model' => $model
                    ]);

// to attach project related attachments
                    $form = ActiveForm::begin([
                        'options' => ['enctype' => 'multipart/form-data'],
                        'action' => 'upload-attachments?id=' . $model->id
                    ]);
                    echo $form->field($model, 'scannedFile[]', ['options' => ['class' => ['my-3']]])->fileInput(['multiple' => true])->label("Attachments:");
                    echo Html::submitButton('Upload', ['class' => 'btn btn-success']);
                    ActiveForm::end();
                    ?>
                </div>
            </fieldset>
        </div>
        <div class="col-xl-7 order-md-1">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Panels:</legend>
                <div class="table-responsive">
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
                                    <th class="tdnowrap text-center align-top">Action</th>
                                    <th class="tdnowrap text-center align-top">Reference</th>
                                    <th class="tdnowrap text-center align-top">Select<br/>
                                        <?= Html::checkbox("", false, ['id' => 'checkAllControl', 'onclick' => 'checkAllItems(this)']) ?>
                                    </th>
                                    <th class="tdnowrap text-center align-top">FAT/<br/>ITP</th>
                                </tr>
                            </thead>
                            <tbody id="itemDisplayTable">
                                <?php
                                $hasPendingPanel = false;
                                foreach ($panelLists as $key => $panel) {
                                    $panelCheckboxOptions = [
                                        'value' => $panel->id,
                                        'class' => 'itemToFinalize',
                                    ];
                                    $isFinalized = empty($panel->finalized_at . $panel->design_completed_at);

                                    if ($panel->activeStatus == 0) {
                                        echo '<tr>';
                                        echo '<td class="text-right px-2">' . ($key + 1) . '</td>';
                                        echo '<td class="tdnowrap">' . $panel->project_production_panel_code . '</td>';
//                                        echo '<td style="">' . $panel->panel_description .'<br/>(RM '.MyFormatter::asDecimal2_emptyZero($panel->amount) .')</td>';
                                        echo '<td style="">' . $panel->panel_description . '<br/>(RM ' . MyFormatter::asDecimal2_emptyZero($panel->amount) . ')' . ($panel->remark ? ('<br/>' . nl2br(Html::encode($panel->remark))) : "") . '</td>';
                                        echo '<td class="tdnowrap">' . $panel->project_type_name . '</td>';
                                        echo '<td class="text-right px-3 tdnowrap">' . $panel->quantity . " " . $panel->unit_code . ($panel->quantity > 1 ? "S" : "") . '</td>';
                                        echo '<td class="text-center tdnowrap">';
                                        if (empty($panel->finalized_at . $panel->design_completed_at)) {
                                            echo Html::a("<i class='far fa-edit fa-lg'></i>",
                                                    "javascript:",
                                                    [
                                                        "onclick" => "event.preventDefault();",
                                                        "value" => \yii\helpers\Url::to(['ajax-edit-panels', 'panelId' => $panel->id]),
                                                        "class" => "modalButton text-success mx-1",
                                                        "title" => "Edit Panel Detail"
                                                    ]
                                            );
                                        } else {
                                            echo '<i class="far fa-edit fa-lg mx-1 text-muted" title="Edit Panel Detail (Disabled as already finalized)"></i>';
                                            echo Html::hiddenInput('finalizeBox[]', null, ['id' => 'panelIdInput']);
                                        }
                                        echo '</td>';
                                        echo '<td class="text-center">';
                                        if ($panel['filename']) {
                                            echo Html::a('<i class="fas fa-file-alt fa-lg"></i>',
                                                    ['get-panel-file-by-panel-id', 'panelId' => $panel->id],
                                                    ['class' => 'text-warning m-2', 'target' => '_blank', 'title' => 'View Attachment']);
                                            echo Html::a('<i class="fas fa-trash-alt fa-lg"></i>',
                                                    ['/production/production/delete-panel-file', 'panelId' => $panel->id],
                                                    [
                                                        'class' => 'text-danger mx-2',
                                                        'data' => ['confirm' => "Remove panel file?", 'method' => 'post'],
                                                        'title' => 'Delete Attachment'
                                            ]);
                                        } else {
                                            echo Html::a('<i class="fas fa-upload fa-lg"></i', "#",
                                                    [
                                                        'title' => "Upload",
                                                        "value" => ("/production/production/panel-upload-attachment?panelId=" . $panel->id),
                                                        "class" => "modalButtonMedium m-2",
                                                        'data' => ['modaltitle' => "Attach panel file (1 only)"]
                                            ]);
                                        }
                                        echo '</td>';
                                        echo '<td class="text-center tdnowrap">';
                                        if (empty($panel->finalized_at)) {
                                            echo Html::checkbox("finalizeBox[]", false, $panelCheckboxOptions);
                                            $hasPendingPanel = true;
                                        } else {
                                            echo Html::a(
                                                    "<i class='fas fa-cogs'></i>",
                                                    ['view-tasks', 'id' => $model->id, 'panelId' => $panel->id],
                                                    ['title' => 'Configure Panel Task']
                                            );
                                            $bomFinalized = (isset($panel->bomMasters[0]) && $panel->bomMasters[0]->finalized_status == 1) ? true : false;
                                            $gotBomDetail = false;
                                            if (isset($panel->bomMasters[0])) {
                                                $bomDetail = frontend\models\bom\BomDetails::find()->where(['bom_master' => $panel->bomMasters[0]->id])->all();
                                                $gotBomDetail = (empty($bomDetail) ? false : true);
                                            }

                                            if ($bomFinalized) {
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
                                        }
                                        echo '</td>';
                                        echo '<td class="text-center">';
                                        echo Html::a('<i class="fas fa-arrow-circle-right fa-lg"></i>',
                                                ['/test/testing/initiate-from-panel', 'panelid' => $panel->id],
                                                [
                                                    'class' => 'text-primary',
                                                    'title' => 'Start test']);

                                        echo '</td>';
                                    } else { //deleted panels
                                        echo '<tr>';
                                        echo '<td class="text-right px-2">' . ($key + 1) . '</td>';
                                        echo '<td class="tdnowrap">';
                                        echo '<span class="crossed-out-text">' . $panel->project_production_panel_code . '</span>';
                                        echo '</td>';
                                        echo '<td style="">';
                                        echo '<span class="crossed-out-text">' . $panel->panel_description . '</span>';
                                        echo '</td>';
                                        echo '<td class="tdnowrap">';
                                        echo '<span class="crossed-out-text">' . $panel->project_type_name . '</span>';
                                        echo '</td>';
                                        echo '<td class="text-right px-3 tdnowrap">';
                                        echo '<span class="crossed-out-text">' . $panel->quantity . " " . $panel->unit_code . ($panel->quantity > 1 ? "S" : "") . '</span>';
                                        echo '</td>';
                                        echo '<td class="text-center tdnowrap">'; // action column
                                        if (empty($panel->finalized_at . $panel->design_completed_at)) {
                                            echo Html::a("<i class='far fa-edit fa-lg' style='display: none;'></i>",
                                                    "javascript:",
                                                    [
                                                        "onclick" => "event.preventDefault();",
                                                        "value" => \yii\helpers\Url::to(['ajax-edit-panels', 'panelId' => $panel->id]),
                                                        "class" => "modalButton text-success mx-1",
                                                    ]
                                            );
                                        } else {
                                            echo '<i class="far fa-edit fa-lg mx-1 text-muted" style="display: none;"></i>';
                                        }
                                        echo '</td>';
                                        echo '<td class="text-center"'; // reference column     
                                        if ($panel['filename']) {
                                            echo Html::a('<i class="fas fa-file-alt fa-lg" style="display: none;"></i>', ['get-panel-file-by-panel-id', 'panelId' => $panel->id], ['class' => 'text-warning m-2', 'target' => '_blank']);
                                            echo Html::a('<i class="fas fa-trash-alt fa-lg" style="display: none;"></i>',
                                                    ['/production/production/delete-panel-file', 'panelId' => $panel->id],
                                                    ['class' => 'text-danger mx-2',
                                                        'data' => ['confirm' => "Remove panel file?", 'method' => 'post']]);
                                        } else {
                                            echo Html::a('<i class="fas fa-upload fa-lg" style="display: none;"></i', "#",
                                                    [
                                                        'title' => "Upload",
                                                        "value" => ("/production/production/panel-upload-attachment?panelId=" . $panel->id),
                                                        "class" => "modalButtonMedium m-2",
                                                        'data' => ['modaltitle' => "Attach panel file (1 only)"]
                                            ]);
                                        }
                                        echo '</td>';
                                        echo '<td class="text-center">'; // checkbox   column
                                        echo Html::checkbox("deletedPanel[]", true, ["style" => "display: none;"]);
                                        echo '</td>';
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>                       
                        </table>
                        <div class="container-fluid p-0">
                            <?php
                            if ($hasPendingPanel) {
                                echo Html::a('Delete <i class="fas fa-trash"></i>', 'javascript:validateAndDelete()',
                                        ["class" => "btn btn-danger mb-2 ml-2 mt-0 float-right"]);
                                echo Html::a('To Production <i class="fas fa-check"></i>', 'javascript:validateAndFinalize()',
                                        ["class" => "btn btn-success mb-2 mt-0 float-right"]);
                            }
                            ?>
                        </div>
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
<script>
    $(function () {
        $(document).on('beforeSubmit', 'form', function (event) {
            setPositionCookie();
        });

        // check if we should jump to postion.
        getPositionCookie();

        $(".itemToFinalize").click(function () {
            $("#checkAllControl").prop('checked', false);
        });
    });

    function deletePanel(panelId) {
        $("#myHiddenForm").attr('action', 'remove-project-panel?panelId=' + panelId);
        $("#myHiddenForm").submit();
    }

    function checkAllItems(me) {
        let toCheck = $(me).is(':checked');
        $("#myFinalizeForm").find(".itemToFinalize").each(function (idx, elem) {
            $(elem).prop('checked', toCheck);
        });
    }

    function validateAndFinalize() {
        let checked = 0;
        $("#myFinalizeForm").find(".itemToFinalize").each(function (idx, elem) {
            checked += $(elem).is(':checked');
        });

        if (checked <= 0) {
            myAlert("No panel is selected");
        } else {
            $("#myFinalizeForm").submit();
        }
    }

    function validateAndDelete() {
        let checkedPanels = [];
        $("#myFinalizeForm").find(".itemToFinalize:checked").each(function (idx, elem) {
            checkedPanels.push($(elem).val());
        });

        if (checkedPanels.length <= 0) {
            myAlert("No panel is selected");
        } else if (confirm("Delete selected panels?")) {
            $.ajax({
                url: '<?= \yii\helpers\Url::to(['delete-panels-ajax', 'id' => $model->id]) ?>',
                type: 'POST',
                data: {
                    panelIds: checkedPanels
                }
            });
        }
    }
</script>
<style>
    .file-upload-wrapper {
        position: relative;
    }

    .custom-file-upload {
        display: none;
    }

    .file-upload-button {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .crossed-out-text {
        text-decoration: line-through;
        color: red;
    }

</style>