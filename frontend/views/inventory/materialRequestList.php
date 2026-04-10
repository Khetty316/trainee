<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\inventory\InventoryMaterialRequestSeach */
/* @var $dataProvider yii\data\ActiveDataProvider */
$key = '6';
if ($moduleIndex === 'execStock') {
    $pageName = 'Inventory Master - Executive';
} else if ($moduleIndex === 'assistStock') {
    $pageName = 'Inventory Master - Assistant';
} else if ($moduleIndex === 'projcoorStock') {
    $pageName = 'Inventory Master - Project Coordinator';
} else if ($moduleIndex === 'maintenanceHeadStock') {
    $pageName = 'Inventory Master - Head of Maintenance';
} else if ($moduleIndex === 'personalStock') {
    $pageName = 'Inventory Master - Personal';
    $key = '2';
}

$this->title = 'Inventory Control';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = $pageName;
?>
<div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
    <div style="text-align:center; color:white;">
        <div style="font-size: 48px; margin-bottom: 20px;">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
        <h3 style="margin:0; font-weight:bold;">Processing Approvals...</h3>
        <p style="margin-top:10px; font-size:16px;">Please wait, do not close or refresh this page.</p>
        <div id="progress-info" style="margin-top:15px; font-size:14px; background:rgba(255,255,255,0.1); padding:10px; border-radius:5px;">
            <span id="progress-text">Initializing...</span>
        </div>
    </div>
</div>
<div class="inventory-material-request-index">
    <?= $this->render('__inventoryNavBar', ['module' => $moduleIndex, 'pageKey' => $key]) ?>

    <p>
        <?= Html::a('Create Requisition <i class="fas fa-plus"></i>', ['add-material-request', 'type' => $moduleIndex], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?type=' . $moduleIndex, ['class' => 'btn btn-primary']) ?> 
        <?=
        Html::a(
                'User Manual <i class="fas fa-book"></i>',
                ['user-manual-inventory'],
                ['class' => 'btn btn-warning float-right ml-1', 'title' => 'View User Manual', 'target' => '_blank']
        )
        ?>
            <?php if ($moduleIndex === 'execStock' || $moduleIndex === 'assistStock' || $moduleIndex === 'maintenanceHeadStock') { ?>
            <?=
            Html::button('Verify Selected <i class="fas fa-check"></i>', [
                'class' => 'btn btn-success float-right',
                'id' => 'approve-selected'
            ])
            ?>
            <?=
            Html::button('Reject Selected <i class="fas fa-times"></i>', [
                'class' => 'btn btn-danger float-right mr-1',
                'id' => 'reject-selected'
            ])
            ?>
        <?php } ?>
        
    </p>
    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-request-grid']); ?>
    <div class="table-responsive">
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => ['class' => yii\bootstrap4\LinkPager::class],
            'headerRowOptions' => ['class' => 'my-thead'],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
//            'id',
                [
                    'attribute' => 'user_id',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => function ($model) {
                        return ($model->user->fullname);
                    }
                ],
                [
                    'attribute' => 'inventory_detail_id',
                    'label' => 'Supplier',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return ($model->inventoryDetail->supplier->name ?? '-');
                    }
                ],
                [
                    'attribute' => 'inventory_model_id',
                    'label' => 'Model Type',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->inventoryDetail->model->type ?? '-';
                    }
                ],
                [
                    'attribute' => 'inventory_brand_id',
                    'label' => 'Brand',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->inventoryDetail->brand->name ?? '-';
                    }
                ],
                [
                    'attribute' => 'reference_type',
                    'format' => 'raw',
                    'filter' => [
                        '1' => 'Project',
                        '2' => 'Corrective Maintenance',
                        '3' => 'Preventive Maintenance',
                        '4' => 'Others',
                    ],
                    'value' => function ($model) {
                        if ($model->reference_type === 1) {
                            $referenceType = 'Project';
                        } else if ($model->reference_type === 2) {
                            $referenceType = 'Corrective Maintenance';
                        } else if ($model->reference_type === 3) {
                            $referenceType = 'Preventive Maintenance';
                        } else if ($model->reference_type === 4) {
                            $referenceType = 'Others';
                        }
                        return $referenceType ?? '-';
                    },
                ],
                [
                    'attribute' => 'reference_id',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->reference_type === 1) {
                            $id = \frontend\models\ProjectProduction\ProjectProductionPanels::findOne($model->reference_id);
                            $referenceId = $id->project_production_panel_code;
                        } else if ($model->reference_type === 2) {
                            $referenceId = 'Work Order - ' . $model->reference_id;
                        } else if ($model->reference_type === 3) {
                            $referenceId = 'Work Order - ' . $model->reference_id;
                        }
                        return $referenceId ?? $model->reference_id;
                    },
                ],
                [
                    'attribute' => 'desc',
                    'value' => function ($model) {
                        return $model->desc;
                    }
                ],
                [
                    'attribute' => 'request_qty',
                    'headerOptions' => ['style' => 'width: 100px; text-align: center;'],
                    'contentOptions' => ['style' => 'text-align: center;'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return $model->request_qty ?? 0;
                    }
                ],
                'approved_qty',
                [
                    'attribute' => 'created_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $responder = common\models\User::findOne($model->created_by);
                        if ($responder) {
                            return "By " . ($responder->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                        }
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'language' => 'en',
                        'dateFormat' => 'php:d/m/Y',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'onchange' => '$("#w0").yiiGridView("applyFilter")',
                        ],
                    ]),
                ],
                [
                    'attribute' => 'updated_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $responder = common\models\User::findOne($model->updated_by);
                        if ($responder) {
                            return "By " . ($responder->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                        }
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'language' => 'en',
                        'dateFormat' => 'php:d/m/Y',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'onchange' => '$("#w0").yiiGridView("applyFilter")',
                        ],
                    ]),
                ],
                [
                    'attribute' => 'approved_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        $responder = common\models\User::findOne($model->approved_by);
                        if ($responder) {
                            return "By " . ($responder->fullname) . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->approved_at);
                        }
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'approved_at',
                        'language' => 'en',
                        'dateFormat' => 'php:d/m/Y',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'onchange' => '$("#w0").yiiGridView("applyFilter")',
                        ],
                    ]),
                ],
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'filter' => [
                        '0' => "Awaiting Verification",
                        '1' => "Verified",
                        '2' => "Rejected"
                    ],
                    'value' => function ($model) {
                        if ($model->status === 0) {
                            $status = '<span class="text-warning">Awaiting Verification</span>';
                        } else if ($model->status === 1) {
                            $status = '<span class="text-success">Verified</span>';
                        } else if ($model->status === 2) {
                            $status = '<span class="text-danger">Rejected</span>';
                        }
                        return $status;
                    }
                ],
                [
                    'format' => 'raw',
                    'header' => Html::tag('div', 'Action'),
                    'value' => function ($model) use ($moduleIndex) {
                        $buttons = '';

                        if ($model->status == 0 && $model->created_by === Yii::$app->user->identity->id) {
                            $buttons .= Html::a(
                                            '<i class="fas fa-edit"></i>',
                                            "javascript:void(0)",
                                            [
                                                'title' => 'Edit Requested Qty',
                                                'value' => yii\helpers\Url::to(['edit-material-request', 'id' => $model->id, 'type' => $moduleIndex]),
                                                'class' => 'modalButton m-1',
                                                'data-modaltitle' => 'Edit Requested Qty',
                                                'data-pjax' => '0',
                                            ]
                                    ) . ' ';

                            $buttons .= Html::a(
                                    '<i class="fas fa-trash"></i>',
                                    ['cancel-material-request', 'id' => $model->id, 'type' => $moduleIndex], // Direct URL, not through data-value
                                    [
                                        'title' => 'Cancel requisition',
                                        'class' => 'text-danger',
                                        'data-confirm' => 'Are you sure you want to cancel this request?',
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]
                            );
                        }
                        return $buttons;
                    }
                ],
                ($moduleIndex === 'personalStock' ? [] : [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
                    Html::checkbox('select_all', false, ['id' => 'select-all', 'style' => 'margin:0;']),
                    'headerOptions' => ['class' => 'col-sm-1 text-center'],
                    'contentOptions' => ['class' => 'col-sm-1 text-center'],
                    'checkboxOptions' => function ($model) {
                        if ($model->status == 0) {
                            return ['value' => $model->id, 'class' => 'my-checkbox'];
                        }
                        return ['style' => 'display:none'];
                    },
                        ]),
            ],
        ]);
        ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>

</div>
<style>
    /* Loading Overlay Styles */
    #loading-overlay {
        display: none;
    }

    #loading-overlay.active {
        display: flex !important;
    }

    /* Prevent scrolling when loading */
    body.loading-active {
        overflow: hidden;
    }

    /* Pulse animation for loading spinner */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    #loading-overlay .fa-spinner {
        animation: pulse 1.5s ease-in-out infinite;
    }
</style>

<script>
    (function () {
        const pjaxContainer = '#pjax-request-grid';
        let selectedIds = new Set();
        let selectAllActive = false;
        let excludedIds = new Set();

        // Loading overlay functions
        function showLoading(message = 'Processing...') {
            $('#loading-overlay').addClass('active');
            $('body').addClass('loading-active');
            $('#progress-text').text(message);
        }

        function hideLoading() {
            $('#loading-overlay').removeClass('active');
            $('body').removeClass('loading-active');
        }

        function updateLoadingMessage(message) {
            $('#progress-text').text(message);
        }

        // Prevent page navigation during processing
        let isProcessing = false;

        window.addEventListener('beforeunload', function (e) {
            if (isProcessing) {
                e.preventDefault();
                e.returnValue = 'Processing is in progress. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        function reapplyChecks() {
            const $checkboxes = $(pjaxContainer).find('.my-checkbox');

            $checkboxes.each(function () {
                const id = String($(this).val());
                if (selectAllActive) {
                    $(this).prop('checked', !excludedIds.has(id));
                } else {
                    $(this).prop('checked', selectedIds.has(id));
                }
            });

            $(pjaxContainer).find('#select-all').prop('checked', selectAllActive);
        }

        $(document).on('change', pjaxContainer + ' .my-checkbox', function () {
            const id = String($(this).val());
            if (selectAllActive) {
                if (this.checked)
                    excludedIds.delete(id);
                else
                    excludedIds.add(id);
            } else {
                if (this.checked)
                    selectedIds.add(id);
                else
                    selectedIds.delete(id);
                const visible = $(pjaxContainer).find('.my-checkbox:visible');
                const allChecked = visible.length > 0 && visible.length === visible.filter(':checked').length;
                $(pjaxContainer).find('#select-all').prop('checked', allChecked);
            }
        });

        $(document).on('change', '#select-all', function () {
            const checked = this.checked;
            selectAllActive = checked;

            if (checked) {
                excludedIds.clear();
                selectedIds.clear();
                $(pjaxContainer).find('.my-checkbox').prop('checked', true);
            } else {
                excludedIds.clear();
                selectedIds.clear();
                $(pjaxContainer).find('.my-checkbox').prop('checked', false);
            }
        });

        $(document).on('pjax:end', function () {
            setTimeout(reapplyChecks, 50);
        });

        // Generic function to handle approve/reject actions
        function processSelectedItems(action, url) {
            let dataToSend = {};
            let confirmMessage = '';
            let itemCount = 0;
            let actionText = action === 'approve' ? 'approve' : 'reject';
            let actionTextCaps = action === 'approve' ? 'Approval' : 'Rejection';

            if (selectAllActive) {
                dataToSend.selectAll = true;
                dataToSend.excludedIds = Array.from(excludedIds);

                const excludeCount = excludedIds.size;
                confirmMessage = excludeCount > 0
                        ? `Are you sure you want to ${actionText} ALL requests except ${excludeCount} unchecked item(s)?`
                        : `Are you sure you want to ${actionText} ALL requests?`;

                itemCount = 'all';
            } else {
                const ids = Array.from(selectedIds);
                if (ids.length === 0) {
                    alert(`Please select at least one request to ${actionText}.`);
                    return;
                }
                dataToSend.ids = ids;
                itemCount = ids.length;
                confirmMessage = `Are you sure you want to ${actionText} ${ids.length} selected request(s)?`;
            }

            if (!confirm(confirmMessage))
                return;

            const $button = action === 'approve' ? $('#approve-selected') : $('#reject-selected');
            const originalText = $button.html();

            // Disable both buttons during processing
            $('#approve-selected, #reject-selected').prop('disabled', true);
            $button.html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            isProcessing = true;

            // Show loading overlay
            if (itemCount === 'all') {
                showLoading(`Processing all requests... This may take a while.`);
            } else {
                showLoading(`Processing ${itemCount} request(s)...`);
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                timeout: 300000, // 5 minutes timeout for large batches
                success: function (response) {
                    updateLoadingMessage(`${actionTextCaps} successful! Reloading page...`);

                    // Clear selections
                    selectedIds.clear();
                    selectAllActive = false;
                    excludedIds.clear();
                    isProcessing = false;

                    // Short delay before reload to show success message
                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                },
                error: function (xhr, status, error) {
                    isProcessing = false;
                    hideLoading();

                    let errorMessage = `Server error while ${actionText}ing: `;

                    // Better error handling
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += xhr.responseJSON.message;
                    } else if (status === 'timeout') {
                        errorMessage += 'Request timeout. The operation may still be processing. Please refresh the page.';
                    } else if (xhr.status === 0) {
                        errorMessage += 'Network error. Please check your connection.';
                    } else {
                        errorMessage += error || 'Unknown error';
                    }

                    alert(errorMessage);

                    // Re-enable both buttons
                    $('#approve-selected, #reject-selected').prop('disabled', false);
                    $button.html(originalText);
                }
            });
        }

        // Approve selected button handler
        $(document).on('click', '#approve-selected', function (e) {
            e.preventDefault();
            processSelectedItems('approve', '<?= \yii\helpers\Url::to(['verify-material-request']) ?>');
        });

        // Reject selected button handler
        $(document).on('click', '#reject-selected', function (e) {
            e.preventDefault();
            processSelectedItems('reject', '<?= \yii\helpers\Url::to(['reject-material-request']) ?>');
        });

    })();
</script>