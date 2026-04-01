<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventoryOrderRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($moduleIndex === 'execPending') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
    $key = 3;
} else if ($moduleIndex === 'execAll') {
    $pageName = 'Purchasing - Executive';
    $module = 'execPurchasing';
    $key = 4;
} else if ($moduleIndex === 'assistPending') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
    $key = 3;
} else if ($moduleIndex === 'assistAll') {
    $pageName = 'Purchasing - Assistant';
    $module = 'assistPurchasing';
    $key = 4;
} else if ($moduleIndex === 'projcoor') {
    $pageName = 'Purchasing - Project Coordinator';
    $module = 'projcoor';
    $key = 4;
} else if ($moduleIndex === 'maintenanceHeadPending') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
    $key = 4;
} else if ($moduleIndex === 'maintenanceHeadAll') {
    $pageName = 'Purchasing - Head of Maintenance';
    $module = 'maintenanceHeadPurchasing';
    $key = 5;
}

$this->title = 'Order Requests';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => $pageName];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('__inventoryNavBar', ['module' => $module, 'pageKey' => $key]) ?>
<?=
Html::a(
        'User Manual <i class="fas fa-book"></i>',
        ['user-manual-inventory'],
        ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
)
?>
<?php if ($module === 'execPurchasing' || $module === 'assistPurchasing' || $module === 'maintenanceHeadPurchasing') { ?>
    <?=
    Html::button('Confirm Selected <i class="fa fa-check"></i>', [
        'class' => 'btn btn-success float-right mb-2 mr-1',
        'id' => 'order-selected'
    ])
    ?>
<?php } ?>

<?= Html::a('Add New Order Request', ['add-new-order-request', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>

<?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?type=' . $moduleIndex, ['class' => 'btn btn-primary']) ?> 

<div class="table-responsive mt-1">
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => array_merge(
                [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'inventory_detail_id',
                        'label' => 'Supplier',
                        'format' => 'raw',
                        'value' => function ($model) use ($moduleIndex, $module) {
                            $typeLink = '';

                            if (($model->status == 0) && ($module === 'execPurchasing' || $module === 'assistPurchasing')) {
                                $typeLink = Html::a(
                                        "<i class='far fa-edit m-1 float-right'></i>",
                                        "javascript:void(0)",
                                        [
                                            'title' => "Edit Supplier",
                                            'value' => yii\helpers\Url::to(['edit-supplier-order-request', 'id' => $model->id, 'type' => $moduleIndex]),
                                            'class' => 'modalButton',
                                            'data-modaltitle' => 'Edit Supplier',
                                        ]
                                );
                            }
                            return ($model->inventoryDetail->supplier->name ?? '-') . $typeLink;
                        }
                    ],
                    [
                        'attribute' => 'inventory_model_id',
                        'label' => 'Model Type',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->inventoryModel->type ?? '-';
                        }
                    ],
                    [
                        'attribute' => 'inventory_brand_id',
                        'label' => 'Brand',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->inventoryModel->inventoryBrand->name ?? '-';
                        }
                    ],
                    [
                        'attribute' => 'reference_type',
                        'format' => 'raw',
                        'filter' => [
                            'bom_detail' => 'Project - Bill of Material',
                            'reserve' => 'Reservation',
                            'cm' => 'Corrective Maintenance',
                            'pm' => 'Preventive Maintenance'
                        ],
                        'value' => function ($model) {
                            if ($model->reference_type === 'bom_detail') {
                                $referenceType = 'Project - Bill of Material';
                            } else if ($model->reference_type === 'bomstockoutbound') {
                                $referenceType = 'Project - Bill of Material';
                            } else if ($model->reference_type === 'reserve') {
                                $referenceType = 'Reservation';
                            } else if ($model->reference_type === 'cm') {
                                $referenceType = 'Corrective Maintenance';
                            } else if ($model->reference_type === 'pm') {
                                $referenceType = 'Preventive Maintenance';
                            }
                            return $referenceType ?? '-';
                        },
                    ],
                    [
                        'attribute' => 'reference_id',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->reference_type === 'bom_detail') {
                                $id = frontend\models\bom\BomDetails::findOne($model->reference_id);
                                $referenceId = $id->bomMaster->productionPanel->project_production_panel_code;
                            } else if ($model->reference_type === 'bomstockoutbound') {
                                $id = frontend\models\bom\StockOutboundDetails::findOne($model->reference_id);
                                $referenceId = $id->bomDetail->bomMaster->productionPanel->project_production_panel_code;
                            } else if ($model->reference_type === 'reserve') {
                                $id = common\models\User::findOne($model->reference_id);
                                $referenceId = $id->fullname;
                            }
                            return $referenceId ?? $model->reference_id;
                        },
                    ],
                    [
                        'attribute' => 'required_qty',
                        'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                        'contentOptions' => ['style' => 'text-align: center;'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->required_qty;
                        }
                    ],
                    [
                        'attribute' => 'order_qty',
                        'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                        'contentOptions' => ['style' => 'text-align: center;'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->order_qty;
                        }
                    ],
                    [
                        'attribute' => 'received_qty',
                        'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                        'contentOptions' => ['style' => 'text-align: center;'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->received_qty;
                        }
                    ],
                    [
                        'attribute' => 'pending_qty',
                        'headerOptions' => ['style' => 'width: 70px; text-align: center;'],
                        'contentOptions' => ['style' => 'text-align: center;'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->pending_qty;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) use ($moduleIndex) {
                            $typeLink = '';

                            if ($model->received_qty != 0) {
                                $typeLink = Html::a(
                                        "<i class='fas fa-external-link-alt'></i>",
                                        "javascript:void(0)",
                                        [
                                            'title' => "View Log",
                                            'value' => yii\helpers\Url::to([
                                                'view-order-request-allocation',
                                                'id' => $model->id,
                                                'type' => $moduleIndex
                                            ]),
                                            'class' => 'modalButton',
                                            'data-modaltitle' => 'View Log',
                                        ]
                                );
                            }

                            return "<div class='d-flex justify-content-between align-items-center'>
                    <span class='mr-1'>{$model->statusLabel}</span>
                    {$typeLink}
                </div>";
                        },
                        'filter' => Html::activeDropDownList(
                                $searchModel,
                                'status',
                                frontend\models\inventory\InventoryOrderRequest::getStatusList(),
                                ['class' => 'form-control', 'prompt' => 'All']
                        ),
                    ],
                    [
                        'attribute' => 'requested_by',
                        'contentOptions' => ['class' => 'col-sm-1'],
                        'value' => function ($model) {
                            return ($model->requestedBy->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->requested_at);
                        }
                    ],
                    [
                        'attribute' => 'updated_by',
                        'contentOptions' => ['class' => 'col-sm-1'],
                        'value' => function ($model) {
                            $name = $model->updatedBy->fullname ?? null;
                            $date = $model->updated_at ? MyFormatter::asDateTime_ReaddmYHi($model->updated_at) : '-';
                            return $name === null ? '-' : $name . ' @ ' . $date;
                        }
                    ],
                ],
                ($module === 'execPurchasing' || $module === 'assistPurchasing' || $module === 'maintenanceHeadPurchasing') ? [
                    [
                        'class' => 'yii\grid\CheckboxColumn',
                        'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
                        Html::checkbox('select_all', false, ['id' => 'select-all', 'style' => 'margin:0;']),
                        'headerOptions' => ['class' => 'col-sm-1 text-center'],
                        'contentOptions' => ['class' => 'col-sm-1 text-center'],
                        'checkboxOptions' => function ($model) {
                            if ($model->status == 0 && $model->inventory_detail_id !== null) {
                                return ['value' => $model->id, 'class' => 'my-checkbox'];
                            }
                            return ['style' => 'display:none'];
                        },
                    ]
                        ] : []
        ),
    ]);
    ?>
</div>
<script>
    var moduleIndex = <?= json_encode($moduleIndex) ?>;

    console.log(moduleIndex);
// Select All functionality
    $('#select-all').on('change', function () {
        var isChecked = $(this).is(':checked');
        $('.my-checkbox').prop('checked', isChecked);
    });

// Uncheck "Select All" if any checkbox is unchecked
    $('.my-checkbox').on('change', function () {
        if (!$(this).is(':checked')) {
            $('#select-all').prop('checked', false);
        } else {
            // Check if all checkboxes are checked
            var allChecked = $('.my-checkbox:visible').length === $('.my-checkbox:checked').length;
            $('#select-all').prop('checked', allChecked);
        }
    });

// Confirm Selected button click
    $('#order-selected').on('click', function () {
        var selectedIds = [];
        $('.my-checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one order request.');
            return;
        }

        // Submit via POST to confirm-order-request action
        $.ajax({
            url: 'confirm-order-request',
            type: 'POST',
            data: {
                ids: selectedIds,
                moduleIndex: moduleIndex,
                _csrf: yii.getCsrfToken()
            },
            success: function (response) {
                // Open the response in the same page or redirect
                $('body').html(response);
                // OR redirect to a new page:
                // window.location.href = 'confirm-order-request?ids=' + selectedIds.join(',');
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
</script>