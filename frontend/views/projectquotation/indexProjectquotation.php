<?php

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
            <?= Html::a('Create Quotation', ['create-projectquotation'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
        </div>
        <div class="col-md-9">
            <div class="float-right">
                <div class="d-inline-block mr-2">
                    <label for="export-start-date" class="d-block mb-1" style="font-size: 12px;">Quotation Date From</label>
                    <?=
                    DatePicker::widget([
                        'name' => 'export_start_date',
                        'id' => 'export-start-date',
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'style' => 'width: 140px;'],
                        'dateFormat' => 'dd/MM/yyyy',
                        'clientOptions' => [
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                        ],
                    ]);
                    ?>
                </div>
                <div class="d-inline-block mr-2">
                    <label for="export-end-date" class="d-block mb-1" style="font-size: 12px;">Quotation Date To</label>
                    <?=
                    DatePicker::widget([
                        'name' => 'export_end_date',
                        'id' => 'export-end-date',
                        'options' => ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'style' => 'width: 140px;'],
                        'dateFormat' => 'dd/MM/yyyy',
                        'clientOptions' => [
                            'showButtonPanel' => true,
                            'closeText' => 'Close',
                        ],
                    ]);
                    ?>
                </div>
                <div class="d-inline-block" style="vertical-align: bottom;">
                    <?=
                    Html::button('Export Selected to Excel <i class="fas fa-file-excel"></i>', [
                        'class' => 'btn btn-info',
                        'id' => 'export-selected',
                        'style' => 'margin-bottom: 0;'
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-md-12 text-right">
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Select quotations and optionally choose date range
            </small>
        </div>
    </div>

    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-quotation-grid']); ?>

    <?=
    GridView::widget([
        'id' => 'quotation-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
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
                'filter' => ['QUOTATION' => 'QUOTATION', 'CONFIRMED' => 'CONFIRMED', "PUSHED" => "PUSHED"]
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
                'class' => 'yii\grid\CheckboxColumn',
                'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
                Html::checkbox('select_all', false, ['id' => 'select-all', 'style' => 'margin:0;']),
                'headerOptions' => ['class' => 'text-center'],
                'contentOptions' => ['class' => 'text-center'],
                'checkboxOptions' => function ($model) {
                    return ['value' => $model->id, 'class' => 'my-checkbox'];
                },
            ],
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

        // Function to get ALL filtered IDs from rr
        function getAllFilteredIds(callback) {
            showLoading('Loading all filtered items...');

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
                    hideLoading();
                    if (response.success && response.ids) {
                        callback(response.ids);
                    } else {
                        alert('Failed to load all items: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function () {
                    hideLoading();
                    alert('Failed to load all items. Please try again.');
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