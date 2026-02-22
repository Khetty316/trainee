<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\projectquotation\ProjectQRevisionsTemplate */

$this->title = $model->revision_description;
$this->params['breadcrumbs'][] = ['label' => 'Quotation Template', 'url' => ['indexpqrevision']];
$this->params['breadcrumbs'][] = $this->title;

$sst = \frontend\models\common\RefGeneralReferences::getValue("sst_value")->value;
$amoutBeforeSST = 0;
$SSTAmount = 0;
$totalAmountBeforeSST = 0;
$totalSSTAmount = 0;
\yii\web\YiiAsset::register($this);
echo yii\jui\Sortable::widget([
]);
?>
<div class="project-qrevisions-template-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?php
        if ($model->is_active == 1) {
            echo Html::a("Update Template <i class='far fa-edit'></i>", ['update-p-q-template-revision-panel', 'id' => $model->id], ['class' => 'btn btn-success']);
        }
        ?>
        <?=
        Html::a('Clone Template <i class="far fa-object-ungroup"></i>',
                '#',
                ['class' => 'btn btn-primary', 'data' => ['toggle' => 'modal', 'target' => '#modalSetTemplate']])
        ?>

        <?php
//        =
//        Html::a("Delete <i class='far fa-trash-alt'></i>", ['deletepqrevision', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ])
        ?>
        <?php
        if ($model->is_active == 1) {
            echo Html::a("Deactivate", ['deactivatepqrevision', 'id' => $model->id], [
                'class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Are you sure you want to deactivate this template?',
                    'method' => 'post',
                ],
            ]);
        } else {
            echo Html::a("Reactivate", ['reactivatepqrevision', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Are you sure you want to activate this template?',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'template' => "<tr><th style='width: 25%;'>{label}</th><td>{value}</td></tr>",
        'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
        'attributes' => [
            'revision_description',
            'remark:ntext',
            [
                'attribute' => 'currency_id',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->currency->currency_sign;
                }
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'value' => function ($model) {
                    return MyFormatter::asDecimal2_emptyZero($model->amount);
                }
            ],
            'q_material_offered:ntext',
            'q_switchboard_standard:ntext',
            'q_quotation',
            'q_delivery_ship_mode',
            'q_delivery_destination',
            'q_delivery',
            'q_validity',
            'q_payment',
            'q_remark:ntext',
            [
                'attribute' => 'with_sst',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->with_sst ? "Yes" : "No";
                }
            ],
            [
                'attribute' => 'show_breakdown',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->show_breakdown ? "Yes" : "No";
                }
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = \common\models\User::findOne($model['created_by']);
                    return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = \common\models\User::findOne($model['updated_by']);
                    if ($user) {
                        return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    } else {
                        return null;
                    }
                }
            ],
            [
                'attribute' => 'deactivated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = \common\models\User::findOne($model['deactivated_by']);
                    if ($user) {
                        return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    } else {
                        return null;
                    }
                }
            ],
        ],
    ])
    ?>
    <div class="row">
        <div class="col-xs-12 col-xl-9">
            <fieldset class="form-group border p-3">
                <legend class="w-auto px-2  m-0 ">Panels:</legend>

                <!--                <div class="form-check form-check-inline">
                <?php
                echo Html::a(
                        'New Panel <i class="fas fa-plus"></i>',
                        '#',
                        [
                            'title' => 'Create new panel',
                            'class' => 'btn btn-success mb-2 mt-0',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalNewPanel',
                        ]
                );
                ?>
                
                                </div>-->
                <!--                <div class="form-check form-check-inline float-right">
                                    <div class=" custom-control custom-checkbox float-right vmiddle">
                                        <input type="checkbox" class="custom-control-input" id="allowSort"/>
                                        <label class="custom-control-label" for="allowSort" >Allow Sort</label>
                                    </div>  
                                </div>-->

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
                                    <!--<div class="custom-control custom-checkbox">-->
                                        <!--<input type="checkbox" class="custom-control-input" id="withSST" <?= $model->with_sst ? "Checked" : "" ?>/>-->
                                    <!--<label class="custom-control-label" for="withSST" >Tax</label>-->
                                    <!--</div>-->
                                    Tax
                                </th>
                                <th class="text-right" >
                                    Amount with Tax
                                </th>
                                <!--<th class="text-center tdnowrap">Action</th>-->
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
                                                ['/projectqtemplate/viewpqpanel', 'id' => $panel->id],
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
            </fieldset>
        </div>
    </div>
</div>

<div class="modal fade" id="modalSetTemplate" tabindex="-1" role="dialog" aria-labelledby="modalSetTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <?php
            $form4 = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'template' => "{label}<div class=\"col-sm-12\">{input}{error}{hint}</div>\n",
                    'horizontalCssClasses' => [
                        'label' => 'col-sm-12',
                        'offset' => 'col-sm-offset-4',
                        'wrapper' => 'col-sm-6',
                    ],
                ],
                'options' => ['autocomplete' => 'off'],
                'action' => 'clone-template'
            ]);
            ?>
            <div class="modal-header">
                <h5 class="modal-title" id="modalSetTemplateLabel">Clone template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= $form4->field($model, 'id', ['options' => ['class' => 'hidden']])->hiddenInput()->label(false) ?>
                <?= $form4->field($model, 'revision_description')->textInput(['value' => ''])->label("New Template Name") ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Create Template <i class="fas fa-check"></i></button>
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
                    reloadRevisionAmount();
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
                    reloadRevisionAmount();
                } else {
                    myAlert("Fail to remove. Contact IT Support");
                }
            }
        });
    }

    function reloadRevisionAmount() {
        $.ajax({
            type: "GET",
            url: "load-revision-amount",
            dataType: "json",
            data: {
                revisionId: <?= $model->id ?>
            },
            success: function (data) {
                $("#revisionAmountDisplay").html(data.amount);
            }
        });
    }
</script>