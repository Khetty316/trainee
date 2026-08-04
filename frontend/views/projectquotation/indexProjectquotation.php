<?php

//indexProjectquotation.php
use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\projectquotation\ProjectQTypes;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\projectquotation\ProjectQMastersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Project Quotation List';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Loading Overlay -->
<div id="loading-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center;">
    <div style="text-align:center; color:white;">
        <div style="font-size: 48px; margin-bottom: 20px;">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
        <h3 style="margin:0; font-weight:bold;">Exporting Quotations...</h3>
        <p style="margin-top:10px; font-size:16px;">Please wait, do not close or refresh this page.</p>
        <div id="progress-info" style="margin-top:15px; font-size:14px; background:rgba(255,255,255,0.1); padding:10px; border-radius:5px;">
            <span id="progress-text">Initializing...</span>
        </div>
    </div>
</div>

<div class="project-qmasters-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row mb-3 align-items-end">
        <div class="col-md-3">
            <?= Html::a('Create Quotation <i class="fas fa-plus"></i>', ['create-projectquotation'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        </div>
        <div class="col-md-9">
            <div class="float-right">
                <!--                <div class="d-inline-block mr-2">
                                    <label for="export-start-date" class="d-block mb-1" style="font-size: 12px;">Quotation Date From</label>
                <?php
//                    =
//                    DatePicker::widget([
//                        'name' => 'export_start_date',
//                        'id' => 'export-start-date',
//                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'style' => 'width: 140px;'],
//                        'dateFormat' => 'dd/MM/yyyy',
//                        'clientOptions' => [
//                            'showButtonPanel' => true,
//                            'closeText' => 'Close',
//                        ],
//                    ]);
                ?>
                                </div>
                                <div class="d-inline-block mr-2">
                                    <label for="export-end-date" class="d-block mb-1" style="font-size: 12px;">Quotation Date To</label>
                <?php
//                    =
//                    DatePicker::widget([
//                        'name' => 'export_end_date',
//                        'id' => 'export-end-date',
//                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'style' => 'width: 140px;'],
//                        'dateFormat' => 'dd/MM/yyyy',
//                        'clientOptions' => [
//                            'showButtonPanel' => true,
//                            'closeText' => 'Close',
//                        ],
//                    ]);
                ?>
                                </div>-->
                <!--<div class="d-inline-block" style="vertical-align: bottom;">-->
                <?php
//                    =
//                    Html::button('Export Selected to Excel <i class="fas fa-file-excel"></i>', [
//                        'class' => 'btn btn-info',
//                        'id' => 'export-selected',
//                        'style' => 'margin-bottom: 0;'
//                    ])
                ?>
                <!--</div>-->
            </div>
        </div>
    </div>
    <!--    <div class="row mb-2">
            <div class="col-md-12 text-right">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Select quotations and optionally choose date range
                </small>
            </div>
        </div>-->

    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-quotation-grid']); ?>

    <!--    <div class="card mb-3" id="summary-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h5 class="mb-1">Total Records</h5>
                        <h3 class="text-primary" id="total-records"><?= $dataProvider->getTotalCount() ?></h3>
                    </div>
                    <div class="col-md-3">
                        <h5 class="mb-1">Selected Records</h5>
                        <h3 class="text-info" id="selected-count">0</h3>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-1">Total Amount (Selected)</h5>
                        <h3 class="text-success" id="total-amount">-</h3>
                    </div>
                </div>
            </div>
        </div>-->

    <?=
    GridView::widget([
        'id' => 'quotation-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => yii\bootstrap4\LinkPager::class,
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i> First Page',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i> Prev',
            'nextPageLabel' => 'Next <i class="fa fa-angle-right"></i>',
            'lastPageLabel' => 'Last Page <i class="fa fa-angle-double-right"></i>',
            'maxButtonCount' => 5,
        ],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "
{summary}
{pager}
<div class='table-scroll'>
    {items}
</div>
{pager}
",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'quotation_display_no',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    $str = Html::a($model->quotation_display_no, ['view-projectquotation', 'id' => $model->id]);
                    return $str;
                }
            ],
            [
                'attribute' => 'project_name',
                'contentOptions' => ['style' => 'white-space:normal!important'],
            ],
            [
                'attribute' => 'total_amount',
                'label' => 'Amount',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    return $model->total_amount > 0 ? $model->currency_sign . ' ' . MyFormatter::asDecimal2($model->total_amount) : '0.00';
                },
            ],
            [
                'contentOptions' => ['class' => 'col-sm-1'],
                'attribute' => 'status',
                'filter' => ['QUOTATION' => 'QUOTATION', 'CONFIRMED' => 'CONFIRMED', "PUSHED" => "PUSHED", 'COMPLETED' => 'COMPLETED', "DELIVERED" => "DELIVERED"]
            ],
            [
                'attribute' => 'clients',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    $returnStr = null;
                    $clients = explode("|||", trim($model->clients));
                    foreach ($clients as $client) {
                        if (!empty($client)) {
                            $returnStr .= "- " . $client . "<br/>";
                        }
                    }
                    return ($returnStr);
                }
            ],
            [
                'contentOptions' => ['class' => 'col-sm-1'],
                'attribute' => 'project_coordinator_fullname',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Created Date',
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1'],
                'value' => function ($model) {
                    return $model->created_at ? date('d/m/Y', strtotime($model->created_at)) : '-';
                },
                'filter' => '<div style="display:flex; flex-direction:column; gap:5px;">' .
                DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at_from',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'From',
                        'style' => 'width:100%; font-size:12px; padding:4px;'
                    ],
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd',
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at_from'),
                        'showButtonPanel' => true,
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                ]) .
                DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at_to',
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => 'To',
                        'style' => 'width:100%; font-size:12px; padding:4px;'
                    ],
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'clientOptions' => [
                        'altFormat' => 'yy-mm-dd',
                        'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'created_at_to'),
                        'showButtonPanel' => true,
                        'changeMonth' => true,
                        'changeYear' => true,
                    ],
                ]) .
                '</div>',
            ],
//            [
//                'class' => 'yii\grid\CheckboxColumn',
//                'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
//                Html::checkbox('select_all', false, ['id' => 'select-all', 'style' => 'margin:0;']),
//                'headerOptions' => ['class' => 'text-center'],
//                'contentOptions' => ['class' => 'text-center'],
//                'checkboxOptions' => function ($model) {
//                    return ['value' => $model->id, 'class' => 'my-checkbox'];
//                },
//            ],
        ],
    ]);
    ?>
    <input type="hidden" id="global-select-all" value="0">
    <?php \yii\widgets\Pjax::end(); ?>

</div>

<style>
    #loading-overlay {
        display: none;
    }

    #loading-overlay.active {
        display: flex !important;
    }

    body.loading-active {
        overflow: hidden;
    }

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

    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }

    .card-body {
        padding: 1rem;
    }
</style>
<script>
    function getAllFilteredIds(callback) {

        const filterData = {};

        $('input[name*="Search"], select[name*="Search"]').each(function () {
            const name = $(this).attr('name');
            const value = $(this).val();

            if (name && value !== '') {
                filterData[name] = value;
            }
        });

        $.ajax({
            url: '<?= \yii\helpers\Url::to(["get-all-filtered-ids"]) ?>',
            type: 'POST',
            data: {filters: filterData},

            success: function (response) {

                if (response.success && response.ids) {
                    callback(response.ids);
                } else {
                    alert('Failed to load all items');
                }
            },

            error: function () {
                alert('Failed to load all items.');
            }
        });
    }
</script>
<script>
    (function () {
        const pjaxContainer = '#pjax-quotation-grid';
        let isProcessing = false;
        let isGlobalSelectAll = false;
        let selectedIds = new Set();

        // Loading overlay
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

        // Prevent leaving during export
        window.addEventListener('beforeunload', function (e) {
            if (isProcessing) {
                e.preventDefault();
                e.returnValue = 'Export is in progress. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        // Update selected count display
        function updateSelectedCount() {
            const count = selectedIds.size;
            let buttonText = 'Export Selected to Excel <i class="fas fa-file-excel"></i>';

            if (count > 0) {
                buttonText += ` (${count} selected)`;
            }

            $('#export-selected').html(buttonText);
        }

        // Enhanced Select All functionality
        $(document).on('change', '#select-all', function () {
            const isChecked = $(this).prop('checked');

            if (isChecked) {
                isGlobalSelectAll = true;

                getAllFilteredIds(function (allIds) {
                    selectedIds.clear();
                    allIds.forEach(id => {
                        selectedIds.add(String(id));
                    });
                    $(pjaxContainer).find('.my-checkbox').prop('checked', true);
                    updateSelectedCount();
                });
            } else {
                isGlobalSelectAll = false;
                selectedIds.clear();
                $(pjaxContainer).find('.my-checkbox').prop('checked', false);
                updateSelectedCount();
            }
        });

        // Individual checkbox change
        $(document).on('change', '.my-checkbox', function () {
            const id = String($(this).val());

            if ($(this).prop('checked')) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);

                if (isGlobalSelectAll) {
                    isGlobalSelectAll = false;
                    $('#select-all').prop('checked', false);
                }
            }

            const visibleCheckboxes = $(pjaxContainer).find('.my-checkbox:visible');
            const allCheckedOnPage = visibleCheckboxes.length > 0 &&
                    visibleCheckboxes.length === visibleCheckboxes.filter(':checked').length;
            $('#select-all').prop('checked', allCheckedOnPage);

            updateSelectedCount();
        });

        // Reapply checkbox states after Pjax reload
        $(document).on('pjax:end', function () {
            $(pjaxContainer).find('.my-checkbox').each(function () {
                const id = String($(this).val());
                $(this).prop('checked', selectedIds.has(id));
            });

            const visibleCheckboxes = $(pjaxContainer).find('.my-checkbox:visible');
            const allCheckedOnPage = visibleCheckboxes.length > 0 &&
                    visibleCheckboxes.length === visibleCheckboxes.filter(':checked').length;
            $('#select-all').prop('checked', allCheckedOnPage || isGlobalSelectAll);

            updateSelectedCount();
        });

        // Export selected with date range
        $(document).on('click', '#export-selected', function (e) {
            e.preventDefault();

            // Get date values
            const startDate = $('#export-start-date').val();
            const endDate = $('#export-end-date').val();

            // If global select all is active but IDs aren't loaded yet
            if (isGlobalSelectAll && selectedIds.size === 0) {
                getAllFilteredIds(function (allIds) {
                    selectedIds = new Set(allIds.map(String));
                    performExport(startDate, endDate);
                });
                return;
            }

            performExport(startDate, endDate);
        });

        // Function to perform export
        function performExport(startDate, endDate) {
            const idsToExport = Array.from(selectedIds);

            if (idsToExport.length === 0) {
                alert('Please select at least one quotation to export.');
                return;
            }

            // Validate dates if provided
            if ((startDate && !endDate) || (!startDate && endDate)) {
                alert('Please select both start date and end date, or leave both empty.');
                return;
            }

            const totalCount = idsToExport.length;
            let confirmMessage = `Are you sure you want to export ${totalCount} selected quotation(s)?`;

            if (startDate && endDate) {
                confirmMessage = `Are you sure you want to export ${totalCount} selected quotation(s) from ${startDate} to ${endDate}?`;
            }

            if (!confirm(confirmMessage))
                return;

            const $button = $('#export-selected');
            const originalButtonText = $button.html();

            $button.prop('disabled', true);
            isProcessing = true;

            let loadingMsg = `Exporting ${totalCount} quotation(s)...`;
            if (startDate && endDate) {
                loadingMsg = `Exporting ${totalCount} quotation(s) from ${startDate} to ${endDate}...`;
            }
            showLoading(loadingMsg);

            $.ajax({
                url: '<?= \yii\helpers\Url::to(["export-quotations-excel"]) ?>',
                type: 'POST',
                data: {
                    ids: JSON.stringify(idsToExport),
                    selectAll: isGlobalSelectAll ? '1' : '0',
                    startDate: startDate || '',
                    endDate: endDate || ''
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function (response, status, xhr) {
                    var contentType = xhr.getResponseHeader("content-type") || "";

                    if (contentType.indexOf('application/json') > -1) {
                        var reader = new FileReader();
                        reader.onload = function () {
                            try {
                                var errorData = JSON.parse(reader.result);
                                alert('Error: ' + (errorData.message || 'Export failed'));
                            } catch (e) {
                                alert('Export failed with an unknown error');
                            }
                            isProcessing = false;
                            hideLoading();
                            $button.prop('disabled', false).html(originalButtonText);
                        };
                        reader.readAsText(response);
                        return;
                    }

                    updateLoadingMessage('Export successful! Downloading file...');

                    var blob = new Blob([response], {type: 'application/vnd.ms-excel'});
                    var link = document.createElement('a');

                    var filename = 'Project_Quotations_';
                    if (startDate && endDate) {
                        filename += startDate.replace(/\//g, '') + '_to_' + endDate.replace(/\//g, '');
                    } else {
                        var d = new Date();
                        filename += d.getFullYear() + '' +
                                String(d.getMonth() + 1).padStart(2, '0') + '' +
                                String(d.getDate()).padStart(2, '0') + '_' +
                                String(d.getHours()).padStart(2, '0') + '' +
                                String(d.getMinutes()).padStart(2, '0') + '' +
                                String(d.getSeconds()).padStart(2, '0');
                    }
                    filename += '.xls';

                    link.href = URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(link.href);

                    // Clear selections after export
                    selectedIds.clear();
                    isGlobalSelectAll = false;
                    $('.my-checkbox').prop('checked', false);
                    $('#select-all').prop('checked', false);

                    setTimeout(function () {
                        isProcessing = false;
                        hideLoading();
                        $button.prop('disabled', false).html('Export Selected to Excel <i class="fas fa-file-excel"></i>');
                        $.pjax.reload({container: pjaxContainer});
                    }, 1500);
                },
                error: function (xhr, status, error) {
                    isProcessing = false;
                    hideLoading();

                    let errorMessage = 'No result found!';
//
//                    if (xhr.responseText) {
//                        try {
//                            var errorData = JSON.parse(xhr.responseText);
//                            errorMessage += errorData.message || error;
//                        } catch (e) {
//                            errorMessage += error || 'Unknown error';
//                        }
//                    } else if (status === 'timeout') {
//                        errorMessage += 'Request timeout. Please try with fewer items.';
//                    } else if (xhr.status === 0) {
//                        errorMessage += 'Network error. Please check your connection.';
//                    } else {
//                        errorMessage += error || 'Unknown error';
//                    }

                    alert(errorMessage);
                    $button.prop('disabled', false).html(originalButtonText);
                }
            });
        }

        // Initialize selected count on page load
        $(document).ready(function () {
            updateSelectedCount();
        });

    })();
</script>
<script>
    (function () {
        const pjaxContainer = '#pjax-quotation-grid';
        let isProcessing = false;
        let isGlobalSelectAll = false;
        let selectedIds = new Set();

        // ... existing loading functions ...

        // NEW: Function to calculate and update total amount
        function updateTotalAmount() {
            const idsToCalculate = Array.from(selectedIds);
            const startDate = $('#export-start-date').val();
            const endDate = $('#export-end-date').val();

            if (idsToCalculate.length === 0) {
                $('#total-amount').text('-');
                $('#selected-count').text('0');
                return;
            }

            $('#selected-count').text(idsToCalculate.length);
            $('#total-amount').html('<i class="fas fa-spinner fa-spin"></i> Calculating...');

            $.ajax({
                url: '<?= \yii\helpers\Url::to(["calculate-total-amount"]) ?>',
                type: 'POST',
                data: {
                    ids: JSON.stringify(idsToCalculate),
                    startDate: startDate || '',
                    endDate: endDate || ''
                },
                success: function (response) {
                    if (response.success) {
                        $('#total-amount').text(response.formatted);
                    } else {
                        $('#total-amount').text('Error calculating');
                    }
                },
                error: function () {
                    $('#total-amount').text('Error');
                }
            });
        }

        // Update selected count display
        function updateSelectedCount() {
            const count = selectedIds.size;
            let buttonText = 'Export Selected to Excel <i class="fas fa-file-excel"></i>';

            if (count > 0) {
                buttonText += ` (${count} selected)`;
            }

            $('#export-selected').html(buttonText);

            // NEW: Update total amount
            updateTotalAmount();
        }

        // Enhanced Select All functionality
        $(document).on('change', '#select-all', function () {
            const isChecked = $(this).prop('checked');

            if (isChecked) {
                isGlobalSelectAll = true;

                getAllFilteredIds(function (allIds) {
                    selectedIds.clear();
                    allIds.forEach(id => {
                        selectedIds.add(String(id));
                    });
                    $(pjaxContainer).find('.my-checkbox').prop('checked', true);
                    updateSelectedCount();
                });
            } else {
                isGlobalSelectAll = false;
                selectedIds.clear();
                $(pjaxContainer).find('.my-checkbox').prop('checked', false);
                updateSelectedCount();
            }
        });

        // Individual checkbox change
        $(document).on('change', '.my-checkbox', function () {
            const id = String($(this).val());

            if ($(this).prop('checked')) {
                selectedIds.add(id);
            } else {
                selectedIds.delete(id);

                if (isGlobalSelectAll) {
                    isGlobalSelectAll = false;
                    $('#select-all').prop('checked', false);
                }
            }

            const visibleCheckboxes = $(pjaxContainer).find('.my-checkbox:visible');
            const allCheckedOnPage = visibleCheckboxes.length > 0 &&
                    visibleCheckboxes.length === visibleCheckboxes.filter(':checked').length;
            $('#select-all').prop('checked', allCheckedOnPage);

            updateSelectedCount();
        });

        // NEW: Update total amount when date changes
        $(document).on('change', '#export-start-date, #export-end-date', function () {
            if (selectedIds.size > 0) {
                updateTotalAmount();
            }
        });

        // Reapply checkbox states after Pjax reload
        $(document).on('pjax:end', function () {
            $(pjaxContainer).find('.my-checkbox').each(function () {
                const id = String($(this).val());
                $(this).prop('checked', selectedIds.has(id));
            });

            const visibleCheckboxes = $(pjaxContainer).find('.my-checkbox:visible');
            const allCheckedOnPage = visibleCheckboxes.length > 0 &&
                    visibleCheckboxes.length === visibleCheckboxes.filter(':checked').length;
            $('#select-all').prop('checked', allCheckedOnPage || isGlobalSelectAll);

            updateSelectedCount();
        });

        // Initialize selected count on page load
        $(document).ready(function () {
            updateSelectedCount();
        });

    })();
</script>

<style>
    .table-scroll {
        overflow-x: auto;
    }
    .table-scroll {
        max-height: calc(100vh - 320px);
        overflow-y: auto;
    }


    .table-scroll thead tr.filters th {
        position: sticky;
        top: 48px;   /* Adjust if needed */
        background: #fff;
        z-index: 19;
    }

    .table-scroll thead tr.filters th {
        position: sticky;
        top: 45px;
        background: #fff;
        z-index: 9;
    }

    .grid-view .summary {
        margin-bottom: 10px;
    }

    .grid-view .pagination {
        margin: 10px 0;
    }

    .table-scroll {
        margin-bottom: 20px;
        margin-top: 20px;
    }

    .table-scroll thead tr.my-thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
    }

    .table-scroll thead tr.filters th {
        position: static;
    }
</style>