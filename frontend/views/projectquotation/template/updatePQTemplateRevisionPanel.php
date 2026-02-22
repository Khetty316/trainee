<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;
use frontend\models\common\RefProjectQPanelUnit;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisions */

$this->title = $model->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Template', 'url' => ['indexpqrevision']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['viewpqrevision', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => 'Edit'];
\yii\web\YiiAsset::register($this);

$sst = \frontend\models\common\RefGeneralReferences::getValue("sst_value")->value;
$amoutBeforeSST = 0;
$SSTAmount = 0;
$totalAmountBeforeSST = 0;
$totalSSTAmount = 0
?>
<div class="project-qrevisions-view">

    <h3>
        <?= Html::encode($this->title) ?>
    </h3>
    <p class="col-xs-12 col-xl-6 pl-0">
        <?= Html::a("Update Revision Template Detail <i class='far fa-edit'></i>", ['update-p-q-template-revision-detail', 'id' => $model->id], ['class' => 'btn btn-success']) ?>

    </p>
    <?php
    echo yii\jui\Sortable::widget([
    ]);
    ?>

    <div class="row">
        <div class="col-xs-12 col-xl-6">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2 m-0">Revision Template Detail:</legend>
                <?=
                DetailView::widget([
                    'model' => $model,
                    'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                    'template' => "<tr><th style='width: 20%;'>{label}</th><td>{value}</td></tr>",
                    'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
                    'attributes' => [
                        'q_material_offered:ntext',
                        'q_switchboard_standard:ntext',
                        'q_quotation',
                        [
                            'attribute' => 'q_delivery',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return $model->q_delivery_ship_mode . " - " . $model->q_delivery_destination . " - " . $model->q_delivery;
                            }
                        ],
                        'q_validity',
                        'q_payment',
                        'q_remark:ntext',
                    ],
                ])
                ?>
            </fieldset>
        </div>
        <div class="col-xs-12 col-xl-6">

        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-6">

        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xl-9">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Panels:</legend>
                <div class="form-check form-check-inline float-right">
                    <div class=" custom-control custom-checkbox float-right vmiddle">
                        <input type="checkbox" class="custom-control-input" id="allowSort"/>
                        <label class="custom-control-label" for="allowSort" >Allow Sort</label>
                    </div>  
                </div>

                <?php
                $panels = $model->projectQPanelsTemplates;

                if ($panels) {
                    array_multisort(array_column($panels, "sort"), SORT_ASC, $panels);
                    ?> 

                    <table class="table table-sm table-striped table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class="tdnowrap text-center">Item</th>
                                <th>Panel's Type</th>
                                <th>Panel's Name</th>
                                <th class="tdnowrap text-center">Quantity</th>
                                <th class="text-right" >Unit Price (<?= $model->currency->currency_sign ?>)</th>
                                <th class="text-right" >Amount w/o Tax</th>
                                <th class="text-right">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="withSST" <?= $model->with_sst ? "Checked" : "" ?>/>
                                        <label class="custom-control-label" for="withSST" >Tax</label>
                                    </div>
                                </th>
                                <th class="text-right" >
                                    Amount with Tax
                                </th>
                                <th class="text-center tdnowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody id="itemDisplayTable">
                            <?php
                            foreach ($panels as $key => $panel) {
                                $amoutBeforeSST = $panel->amount * $panel->quantity;
                                $SSTAmount = $sst / 100 * $amoutBeforeSST;
                                $totalAmountBeforeSST += $amoutBeforeSST;
                                $totalSSTAmount += $SSTAmount;
                                ?>
                                <tr id="tr_<?= $panel->id ?>">
                                    <td class="text-right px-2"><?= $key + 1 ?></td>
                                    <td class="px-2 col-1 tdnowrap"><?= $panel->panelType->project_type_name ?? ' - ' ?></td>
                                    <td style=""> 
                                        <?=
                                        Html::a($panel->panel_description,
                                                ['update-p-q-template-panel-item', 'id' => $panel->id],
                                                ['title' => 'View', 'class' => 'mx-1 text-primary no-text-deco'])
                                        ?>
                                    </td>
                                    <td class="text-right px-3 tdnowrap">
                                        <?= MyFormatter::asDecimal2($panel->quantity) . " " . $panel->unitCode->unit_name . ($panel->quantity > 1 ? "S" : "") ?>
                                    </td>
                                    <td class="text-right px-2 tdnowrap"><?= MyFormatter::asDecimal2($panel->amount ?? 0) ?></td>
                                    <td class="text-right px-2 tdnowrap"><?= MyFormatter::asDecimal2($amoutBeforeSST) ?></td>
                                    <td class="text-right px-2 <?= $model->with_sst ? "" : "bg-secondary" ?> isColSST tdnowrap" ><?= MyFormatter::asDecimal2($SSTAmount) ?></td>
                                    <td class="text-right px-2 <?= $model->with_sst ? "" : "bg-secondary" ?> isColSST tdnowrap"><?= MyFormatter::asDecimal2($amoutBeforeSST + $SSTAmount) ?></td>
                                    <td class="text-center tdnowrap">
                                        <?=
                                        Html::a(
                                                '<i class="far fa-clone fa-lg"></i>',
                                                '#',
                                                [
                                                    'title' => 'Clone', 'class' => 'mx-1 text-success',
                                                    'data-toggle' => 'modal',
                                                    'data-target' => '#modalClonePanel',
                                                    'data-panelname' => $panel->panel_description,
                                                    'data-motherpanelid' => $panel->id
                                                ]
                                        )
                                        ?>
                                        <?=
                                        Html::a('<i class="far fa-trash-alt fa-lg"></i>',
                                                "javascript:removePanel($panel->id)",
                                                ['title' => 'Remove', 'class' => 'mx-1 text-danger', 'data-confirm' => "Are you sure to remove?"])
                                        ?>
                                        <?php
//                                        echo Html::a("<i class='far fa-edit fa-lg'></i>",
//                                                "#",
//                                                ['class' => 'text-success mx-1',
//                                                    'title' => "Edit",
//                                                    'data' => [
//                                                        'toggle' => 'modal',
//                                                        'target' => '#modalUpdateProjectQPanel',
//                                                        'id' => $panel->id,
//                                                        'name' => $panel->panel_description,
//                                                        'quantity' => $panel->quantity,
//                                                        'amount' => $panel->amount,
//                                                        'unit' => $panel->unit_code
//                                                    ]
//                                        ]);
                                        ?>
                                        <?php
                                        echo Html::a("<i class='far fa-edit fa-lg'></i>",
                                                'javascript:void(0)',
                                                [
                                                    'value' => '/projectqtemplate/update-p-q-panel?panelId=' . $panel->id,
                                                    'title' => 'Edit',
                                                    'class' => 'text-success mx-1 modalButton']);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>        
                        </tbody>
                    </table>
                    <?php
                } else {
                    echo Html::tag('p', '-- No Record --', ['class' => 'text-center']);
                }
                ?>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <?php
                            echo Html::a('New Panel <i class="fas fa-plus"></i>',
                                    'javascript:void(0)',
                                    [
                                        'value' => '/projectqtemplate/add-p-q-panel?revisionId='.$model->id,
                                        'title' => 'Edit',
                                        'class' => 'btn btn-success mb-2 mt-0 modalButton']);
                            ?>
                            <?php
//                            echo Html::a(
//                                    'New Panel <i class="fas fa-plus"></i>',
//                                    '#',
//                                    [
//                                        'title' => 'Create new panel',
//                                        'class' => 'btn btn-success mb-2 mt-0',
//                                        'data-toggle' => 'modal',
//                                        'data-target' => '#modalNewPanel',
//                                    ]
//                            );
                            ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClonePanel" tabindex="-1" role="dialog" aria-labelledby="modalClonePanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form = ActiveForm::begin([
                        'options' => ['autocomplete' => 'off'],
                        'action' => 'clone-panel-same-revision'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalClonePanelLabel">Cloning Panel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-borderless"> 
                    <tbody>
                        <tr>
                            <td class="tdnowrap">Clone from panel</td>
                            <td> : </td>
                            <td id="modal-cloneFromPanelName"></td>
                        </tr>
                        <tr>
                            <td class="req tdnowrap">New panel name</td>
                            <td> : </td>
                            <td>
                                <?= Html::hiddenInput('motherPanelId', '', ['id' => 'modal-motherPanelId']) ?>
                                <?= Html::textInput('clonePanelNewName', '', ['id' => 'clonePanelNewName', 'class' => 'form-control', 'required' => true]) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Clone <i class="far fa-clone"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNewPanel" tabindex="-1" role="dialog" aria-labelledby="modalNewPanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $newPanel = new \frontend\models\projectquotation\ProjectQPanelsTemplate();
            $newPanel->unit_code = RefProjectQPanelUnit::DEFAULT_Code; // Default to "Unit" 3/3/2022
            $form2 = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-12',
                                'offset' => 'col-sm-offset-4',
                                'wrapper' => 'col-sm-6',
                                'error' => '',
                                'hint' => '',
                            ],
                        ],
                        'options' => ['autocomplete' => 'off'],
                        'action' => 'new-panel-template'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalNewPanelLabel">New Panel</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="hidden">
                    <?= $form2->field($newPanel, 'revision_template_id')->textInput(['value' => $model->id])->label(false) ?>
                </div>
                <?= $form2->field($newPanel, 'panel_description')->textInput()->label("New panel name") ?>
                <?= $form2->field($newPanel, 'quantity')->textInput(['type' => 'number', 'step' => '1']) ?>
                <?= $form2->field($newPanel, 'unit_code')->dropDownList(RefProjectQPanelUnit::getDropDownList()) ?>
                <?= $form2->field($newPanel, 'amount')->textInput(['type' => 'number', 'step' => '0.01']) ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Create <i class="fas fa-plus"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="modalUpdateProjectQPanel" tabindex="-1" role="dialog" aria-labelledby="modalUpdateProjectQPanelLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form3 = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                            'horizontalCssClasses' => [
                                'label' => 'col-sm-12',
                                'offset' => 'col-sm-offset-4',
                                'wrapper' => 'col-sm-6',
                                'error' => '',
                                'hint' => '',
                            ],
                        ],
                        'options' => ['autocomplete' => 'off'],
                        'action' => '/projectqpanel/update'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalUpdateProjectQPanelLabel">Update Panel Detail</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form3->field($newPanel, 'id', ['options' => ['class' => 'hidden']])->hiddenInput()->label(false) ?>
                <?= $form3->field($newPanel, 'panel_description')->textInput()->label("New panel name") ?>
                <?= $form3->field($newPanel, 'quantity')->textInput(['type' => 'number', 'step' => '1']) ?>
                <?= $form3->field($newPanel, 'unit_code')->dropDownList(RefProjectQPanelUnit::getDropDownList()) ?>
                <?= $form3->field($newPanel, 'amount')->textInput(['type' => 'number', 'step' => '0.01']) ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Update <i class="fas fa-check"></i></button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('#modalClonePanel').on('show.bs.modal', function (event) {
            $("#clonePanelNewName").val('');
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#modal-cloneFromPanelName').text(button.data('panelname'));
            modal.find('#modal-motherPanelId').val(button.data('motherpanelid'));
        });

        $('#modalUpdateProjectQPanel').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal        
            var modal = $(this);
            modal.find('#projectqpanels-id').val(button.data('id'));
            modal.find('#projectqpanels-panel_description').val(button.data('name'));
            modal.find('#projectqpanels-quantity').val(button.data('quantity'));
            modal.find('#projectqpanels-unit_code').val(button.data('unit'));
            modal.find('#projectqpanels-amount').val(button.data('amount'));
        });

        $("#allowSort").click(function () {
            if ($('#allowSort').is(":checked")) {
                sortableEnable();
            } else {
                sortableDisable();
            }
        });

        $("#withSST").click(function () {
            if ($('#withSST').is(":checked")) {
                $(".isColSST").removeClass('bg-secondary');
                controlSST(<?= $model->id ?>, 1);
            } else {
                $(".isColSST").addClass('bg-secondary');
                controlSST(<?= $model->id ?>, 0);

            }
        });


        $(document).on('beforeSubmit', 'form', function (event) {
            var currentYOffset = window.pageYOffset;  // save current page postion.
            setCookie('jumpToScrollPostion', currentYOffset, 2);
        });

        // check if we should jump to postion.
        var jumpTo = getCookie('jumpToScrollPostion');
        if (jumpTo !== "undefined" && jumpTo !== null) {
            window.scrollTo(0, jumpTo);
            eraseCookie('jumpToScrollPostion');  // and delete cookie so we don't jump again.
        }

    });

    function sortableEnable() {
        $("#itemDisplayTable").sortable({
            disabled: false,
            cursor: 'move',
            axis: 'y',
            dropOnEmpty: false,
            placeholder: "ui-state-highlight",
            stop: function (e, ui) {
                var id = ui.item[0].id;
                var id1 = $("#" + id).prev().attr('id');
                swapItem(id, id1);
            }
        });
        return false;
    }

    function sortableDisable() {
        $("#itemDisplayTable").sortable({
            disabled: true
        });

        return false;
    }

    function swapItem(moveId, previousId) {
        if (typeof (previousId) === "undefined") {
            previousId = "";
        }

        $.ajax({
            type: "POST",
            url: "sort-panels-ajax",
            dataType: "json",
            data: {
                revisionId: '<?= $model->id ?>',
                moveId: moveId,
                previousId: previousId
            }
        });
    }

    function removePanel(panelId) {
        $.ajax({
            type: "POST",
            url: "remove-panel-ajax",
            dataType: "json",
            data: {
                panelId: panelId
            },
            success: function (data) {
                if (data.success) {
//                    reloadRevisionAmount();
                    $("#tr_" + panelId).remove();
                } else {
                    myAlert("Fail to remove. Contact IT Support");
                }
            }
        });
    }

    function controlSST(revisionId, enableFlag) {
        $.ajax({
            type: "POST",
            url: "control-sst-ajax",
            dataType: "json",
            data: {
                revisionId: revisionId,
                enableFlag: enableFlag
            },
            success: function (data) {
                if (data.success) {
//                    reloadRevisionAmount();
                } else {
                    myAlert("Fail to remove. Contact IT Support");
                }
            }
        });
    }


</script>