<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;
?>
<style>
    .strikeout {
        text-decoration: line-through;
    }

</style>
<?php
$panel = $bomMaster->productionPanel;
?>
<div class="bomdetails-index">

    <?php // if (($canReverse ?? false) && (MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super]))) { ?>
        <!--<h4><?php //= Html::encode($this->title) . ($bomMaster->finalized_status ? Html::a(" (Finalized)", 'javascript:void(0)', ['class' => 'text-primary', 'id' => 'revertBtn']) : "")             ?></h4>-->
    <?php // } else { ?>
        <!--<h4><?php //= Html::encode($this->title) . ($bomMaster->finalized_status ? " <span class='text-warning'>(Finalized)</span>" : "")             ?></h4>-->
    <?php // } ?>
    <!--<h5><?php //= $panel->project_production_panel_code . ": " . $panel->panel_description             ?></h5>-->

    <h4><?= Html::encode($this->title) ?></h4>
    <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal])) { ?>
        <?php // if (!$bomMaster->finalized_status) { ?>
        <p>
            <?=
            Html::a("Add Material", "javascript:void(0)", [
                'title' => "Add Material",
                "value" => yii\helpers\Url::to(['..\bom\create', 'bomMasterId' => $bomMaster->id]),
                "class" => "modalButton btn btn-success ml-1",
                'data-modaltitle' => 'Add Material',
                'id' => 'AddMaterialBtn'
            ]);
            ?>
            <?=
            Html::a('Pre-Requisition', ['..\inventory\inventory\create-prerequisition', 'sourceModule' => 'inventory', 'moduleIndex' => "projcoorPendingApproval", 'referenceType' => "bom", 'referenceId' => $bomMaster->id], [
                'class' => 'btn btn-success',
                'target' => '_blank',
                'rel' => 'noopener noreferrer'
            ])
            ?>            
            <?php
//                =
//                Html::button("Finalize B.O.M",
//                        [
//                            'class' => 'btn btn-primary ml-2',
//                            'id' => 'finalizeBomBtn',
//                ]);
            ?>
            <?=
            Html::button("Finalize Selected Material",
                    [
                        'class' => 'btn btn-primary',
                        'id' => 'finalizeSelectedBtn',
            ]);
            ?>
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super])) { ?>
                <?=
                Html::button("Ready for Outbound",
                        [
                            'class' => 'btn btn-primary',
                            'id' => 'readyOutboundSelectedBtn',
                ]);
                ?>
            <?php } ?>
            <?php if ($bomMaster->finalized_status != 1) { ?>
                <?=
                Html::button("Delete Selected", [
                    'class' => 'btn btn-danger ml-2 float-right',
                    'id' => 'deleteSelectedBtn',
                ]);
                ?>
            <?php } ?>
        </p>
        <?php
//        }
    }
    $dataProvider->sort = false;
    $dataProvider->pagination = false;
    ?>

    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => null,
        'rowOptions' => function ($model) {
            return $model->active_status == 0 ? ['class' => 'strikeout text-danger'] : [];
        },
        'columns' => array_filter([
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            ($bomMaster->finalized_status == 1) ? ['attribute' => 'model_type'] : [
                'attribute' => 'model_type',
                'format' => 'raw',
                'value' => function ($model) use ($bomMaster) {
                    if ($model->active_status == 0) {
                        return Html::encode($model->model_type);
                    } else {
                        if (MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal]) &&
                                $model->is_finalized == 1 && ($model->inventory_sts == 2 || $model->inventory_sts == 0)) {
                            return Html::a($model->model_type, "javascript:void(0)", [
                                        'title' => "Edit",
                                        "value" => Url::to(['update', 'id' => $model->id]),
                                        "class" => "modalButton",
                                        'data-modaltitle' => 'Edit Material'
                            ]);
                        } else {
                            return Html::encode($model->model_type);
                        }
                    }
                }
                    ],
            'brand',
            'description',
            'qty',
            'remark',
            [
                'attribute' => 'inventory_sts',
                'label' => '<div style="margin-bottom: 5px;">Inventory' .
                '<input type="checkbox" id="select-all-prereq" class="select-all-checkbox ml-2" title="Select all pre-requisition items"></div>',
                'encodeLabel' => false, // IMPORTANT: Allow HTML in label
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    $canPreReq = MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal]);

                    if ($model->active_status == 1) {
                        switch ($model->inventory_sts) {
                            case 1:
                                return '<span class="badge badge-info">Pre-Req Submitted</span>';
                            case 2:
                                return '<span class="badge badge-success">This item exist in Inventory</span>';
                            case 3:
                                return '<span class="badge badge-danger">Pre-Requisite Rejected</span>';
                            case 4:
                                return '<span class="badge badge-warning">Awaiting Confirmation</span>';
                            case 5:
                                return '<span class="badge badge-info">Purchasing in Progress</span>';
                            case 0:
                                if ($canPreReq && $model->is_finalized == 1 && $model->active_status == 1) {
                                    return '<div>' .
                                            '<span class="badge badge-danger">This item does not exist in Inventory. Please issue Pre-Requisite</span>' .
                                            Html::checkbox('prereq_items[]', false, [
                                                'value' => $model->id,
                                                'class' => 'prereq-checkbox ml-2',
                                                'title' => 'Select for pre-requisition'
                                            ]) .
                                            '</div>';
                                } else {
                                    return '<span class="badge badge-danger">This item does not exist in Inventory.</span>';
                                }
                            default:
                                return '-';
                        }
                    } else {
                        return '->';
                    }
                }
            ],
            [
                'attribute' => 'is_finalized',
                'label' => '<div style="margin-bottom: 5px;">Finalize' .
                '<input type="checkbox" id="select-all-finalize" class="select-all-checkbox ml-2" title="Select all finalize items"></div> ',
                'encodeLabel' => false, // IMPORTANT: Allow HTML in label
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    $canFinalize = MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal]);

                    if ($model->is_finalized == 2 && $model->active_status == 1) {
                        return '<span class="badge badge-success">Finalized</span>';
                    } else if ($model->is_finalized == 1 && $model->active_status == 1) {
                        if ($canFinalize && $model->inventory_sts == 2 && $model->active_status == 1) {
                            return '<div>' .
                                    '<span class="badge badge-warning">Pending</span>' .
                                    Html::checkbox('finalize_items[]', false, [
                                        'value' => $model->id,
                                        'class' => 'finalize-checkbox ml-2',
                                        'title' => 'Select to finalize'
                                    ]) .
                                    '</div>';
                        } 
//                        else {
//                            return '<span class="badge badge-warning">Pending</span>';
//                        }
                    } else if ($model->is_finalized == 3 && $model->active_status == 1) {
                        return '<span class="badge badge-secondary">Outbound</span>';
                    } else {
                        return '<span class="text-muted">-</span>';
                    }
                }
            ],
            MyCommonFunction::checkRoles([AuthItem::ROLE_Stock_Ob_Super]) ? [
                'label' => '<div style="margin-bottom: 5px;">Outbound' .
                '<input type="checkbox" id="select-all-outbound" class="select-all-checkbox ml-2" title="Select all outbound items"></div>',
                'encodeLabel' => false, // IMPORTANT: Allow HTML in label
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    $outbound = \frontend\models\bom\StockOutboundDetails::find()
                            ->where(['active_sts' => 1, 'bom_detail_id' => $model->id])
                            ->exists();

                    if ($outbound) {
                        return '<span class="badge badge-success">In Outbound</span>';
                    } else if ($model->is_finalized == 2 && $model->active_status == 1) {
                        return '<div>' .
                                '<span class="badge badge-secondary">Ready</span>' .
                                Html::checkbox('outbound_items[]', false, [
                                    'value' => $model->id,
                                    'class' => 'outbound-checkbox ml-2',
                                    'title' => 'Select for outbound'
                                ]) .
                                '</div>';
                    } else {
                        return '<span class="text-muted">-</span>';
                    }
                }
                    ] : null,
            ($bomMaster->finalized_status != 1 && MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal])) ? [
                'label' => '<div style="margin-bottom: 5px;">Delete' .
                '<input type="checkbox" id="select-all-delete" class="select-all-checkbox ml-2" title="Select all items to delete"></div>',
                'encodeLabel' => false, // IMPORTANT: Allow HTML in label
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'value' => function ($model) {
                    if ($model->active_status == 1 && ($model->is_finalized != 2 || $model->is_finalized != 3) && ($model->inventory_sts == 0 || $model->inventory_sts == 3)) {
                        return Html::checkbox('delete_items[]', false, [
                                    'value' => $model->id,
                                    'class' => 'delete-checkbox',
                                    'title' => 'Select to delete'
                        ]);
                    }
                    return '<span class="text-muted">-</span>';
                }
                    ] : null,
        ]),
    ]));
    ?>
</div>
<?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Bom_Super, AuthItem::ROLE_Bom_Normal])) { ?>
    <?php // if (!$bomMaster->finalized_status) {  ?>
    <fieldset class="form-group border p-3">
        <legend class="w-auto px-2  m-0 ">Upload By Template:</legend>
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <?php
                    $form = ActiveForm::begin([
                        'action' => ['..\bom\upload-excel', 'bomMasterId' => $bomMaster->id],
                        'options' => ['enctype' => 'multipart/form-data'],
                    ]);

                    echo Html::fileInput('excelTemplate', null, [
                        'accept' => '.xls', 'required' => true,
                    ]);
                    echo Html::submitButton(
                            'Upload Excel <i class="fas fa-upload"></i>',
                            ['class' => 'btn btn-success mb-2 mt-2']);
                    ActiveForm::end();
                    ?>
                    <?php
                    echo Html::a(
                            'Download Template <i class="fas fa-download"></i>',
                            yii\helpers\Url::to('@web/template/template-bill_of_material.xls'),
                            [
                                'class' => 'btn btn-primary mb-5 mt-0',
                                'download' => 'template-bill_of_material.xls',
                                'title' => 'Download Excel Template'
                            ]
                    );
                    ?>
                </div>
            </div>
        </div>
    </fieldset>
    <?php
//    }
}
?>
<style>
    .strikeout {
        text-decoration: line-through;
    }

    .badge {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }

    .finalize-checkbox,
    .prereq-checkbox,
    .outbound-checkbox,
    .delete-checkbox,
    .select-all-checkbox {
        cursor: pointer;
        width: 18px;
        height: 18px;
    }

    .finalize-checkbox:hover,
    .prereq-checkbox:hover,
    .outbound-checkbox:hover,
    .delete-checkbox:hover,
    .select-all-checkbox:hover {
        transform: scale(1.2);
    }

    /* Style for header checkboxes */
    .select-all-checkbox {
        margin: 0 3px;
        vertical-align: middle;
    }
</style>
<script>
    $(document).ready(function () {
        var $preReqBtn = $('a[href*="create-prerequisition"]');
        var $finalizeBtn = $('#finalizeSelectedBtn');
        var $outboundBtn = $('#readyOutboundSelectedBtn');
        var $deleteBtn = $('#deleteSelectedBtn');

        // ===== SELECT ALL FUNCTIONALITY =====

        // Select All - Finalize
        $('#select-all-finalize').on('change', function () {
            var isChecked = $(this).prop('checked');
            $('.finalize-checkbox').prop('checked', isChecked);
            updateFinalizeButton();
        });

        // Select All - Pre-Requisition
        $('#select-all-prereq').on('change', function () {
            var isChecked = $(this).prop('checked');
            $('.prereq-checkbox').prop('checked', isChecked);
            updatePreReqButton();
        });

        // Select All - Outbound
        $('#select-all-outbound').on('change', function () {
            var isChecked = $(this).prop('checked');
            $('.outbound-checkbox').prop('checked', isChecked);
            updateOutboundButton();
        });

        // Select All - Delete
        $('#select-all-delete').on('change', function () {
            var isChecked = $(this).prop('checked');
            $('.delete-checkbox').prop('checked', isChecked);
            updateDeleteButton();
        });

        // ===== UPDATE SELECT ALL STATE WHEN INDIVIDUAL CHECKBOX CHANGES =====

        $(document).on('change', '.finalize-checkbox', function () {
            updateSelectAllState('#select-all-finalize', '.finalize-checkbox');
            updateFinalizeButton();
        });

        $(document).on('change', '.prereq-checkbox', function () {
            updateSelectAllState('#select-all-prereq', '.prereq-checkbox');
            updatePreReqButton();
        });

        $(document).on('change', '.outbound-checkbox', function () {
            updateSelectAllState('#select-all-outbound', '.outbound-checkbox');
            updateOutboundButton();
        });

        $(document).on('change', '.delete-checkbox', function () {
            updateSelectAllState('#select-all-delete', '.delete-checkbox');
            updateDeleteButton();
        });

        // ===== HELPER FUNCTIONS =====

        function updateSelectAllState(selectAllId, checkboxClass) {
            var total = $(checkboxClass).length;
            var checked = $(checkboxClass + ':checked').length;

            if (total > 0) {
                $(selectAllId).prop('checked', checked === total);
                $(selectAllId).prop('indeterminate', checked > 0 && checked < total);
            }
        }

        function updatePreReqButton() {
            var selectedCount = $('.prereq-checkbox:checked').length;
            $preReqBtn.text(selectedCount > 0 ?
                    'Pre-Requisition (' + selectedCount + ')' :
                    'Pre-Requisition');
        }

        function updateFinalizeButton() {
            var selectedCount = $('.finalize-checkbox:checked').length;
            $finalizeBtn.text(selectedCount > 0 ?
                    'Finalize Selected Material (' + selectedCount + ')' :
                    'Finalize Selected Material');
        }

        function updateOutboundButton() {
            var selectedCount = $('.outbound-checkbox:checked').length;
            $outboundBtn.text(selectedCount > 0 ?
                    'Ready for Outbound (' + selectedCount + ')' :
                    'Ready for Outbound');
        }

        function updateDeleteButton() {
            var selectedCount = $('.delete-checkbox:checked').length;
            $deleteBtn.text(selectedCount > 0 ?
                    'Delete Selected (' + selectedCount + ')' :
                    'Delete Selected');
        }

        // ===== ACTION HANDLERS =====

        // Pre-Requisition action
        $preReqBtn.on('click', function (e) {
            e.preventDefault();
            var selectedIds = [];
            $('.prereq-checkbox:checked').each(function () {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Please select at least one item for pre-requisition.');
                return;
            }

            var url = $(this).attr('href') + '&selectedIds=' + selectedIds.join(',');
            window.open(url, '_blank');
        });

        // Finalize action
        $finalizeBtn.on('click', function () {
            var selectedIds = [];
            $('.finalize-checkbox:checked').each(function () {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Please select at least one item to finalize.');
                return;
            }

            if (confirm("Finalize " + selectedIds.length + " selected material(s)?")) {
                $.post('<?= Url::to(['bom/finalize-selected-material']) ?>', {
                    bomMasterId: <?= $bomMaster->id ?>,
                    ids: selectedIds
                }, function (response) {
                    location.reload();
                });
            }
        });

        // Outbound action
        $outboundBtn.on('click', function () {
            var selectedIds = [];
            $('.outbound-checkbox:checked').each(function () {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Please select at least one item for outbound.');
                return;
            }

            if (confirm("Ready " + selectedIds.length + " material(s) for outbound?")) {
                $.post('<?= Url::to(['stockoutbound/outbound-finalized-item']) ?>', {
                    productionPanelId: <?= $panel->id ?>,
                    ids: selectedIds
                }, 
                function (response) {
                    if (response.success) {
                        alert(response.message || 'Outbound initiated successfully!');
                        location.reload();
                    } 
//                    else {
//                        alert('Error: ' + (response.message || 'Failed to initiate outbound'));
//                    }
                }).fail(function (xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Server error occurred'));
                });
            }
        });

        // Delete action
        $deleteBtn.on('click', function () {
            var selectedIds = [];
            $('.delete-checkbox:checked').each(function () {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                alert('Please select at least one item to delete.');
                return;
            }

            if (confirm("Are you sure you want to delete " + selectedIds.length + " selected item(s)?")) {
                $.post('<?= Url::to(['bom/delete-multiple']) ?>', {ids: selectedIds}, function (response) {
                    location.reload();
                });
            }
        });

        // Trigger add material modal if just created
        if ('<?= $justCreated ?? 0 ?>' == true) {
            $('#AddMaterialBtn').trigger('click');
        }
    });
</script>
