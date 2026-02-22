<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

//$this->title = 'Quotation Approval';
//$this->params['breadcrumbs'][] = $this->title;

$approveUrl = \yii\helpers\Url::to(['director-approve-quotation']);
$gridId = 'quotation-grid';
?>

<!-- ✅ Loading Overlay -->
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

<div class="quotation-pdf-masters-index">

    <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary mb-2']) ?>
    <?= Html::button('Approve Selected', [
        'class' => 'btn btn-success float-right mb-2',
        'id' => 'approve-selected'
    ]) ?>

    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-quotation-grid']); ?>

    <?= GridView::widget([
        'id' => $gridId,
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
                'attribute' => 'quotation_no',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->quotation_no . ' ' . Html::a('<i class="far fa-file-pdf fa-lg"></i>', 
                        ['/projectqrevision/read-pdf', 'id' => $model->id],
                        ['class' => 'text-red float-right', 'title' => 'View Quotation', 'target' => '_blank', 'data-pjax' => '0']
                    );
                }
            ],
            [
                'attribute' => 'project_q_client_id',
                'format' => 'raw',
                'value' => fn($model) => $model->projectQClient->client->company_name ?? '-'
            ],
            [
                'attribute' => 'proj_title',
                'format' => 'raw',
                'contentOptions' => ['style' => 'white-space:normal!important'],
                'value' => fn($model) => $model->proj_title
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => fn($model) => 'by ' . $model->createdBy->fullname . ' @ ' . MyFormatter::asDateTime_ReaddmYHi($model->created_at),
                'filter' => yii\jui\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created_at',
                    'language' => 'en',
                    'dateFormat' => 'php:d/m/Y',
                    'options' => ['class' => 'form-control', 'autocomplete' => 'off', 'onchange' => '$("#w0").yiiGridView("applyFilter")'],
                ]),
            ],
            [
                'format' => 'raw',
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'value' => function ($model) {
                    if ($model->md_approval_status == \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_DIRECTOR_APPROVED) {
                        return '<span class="text-success">Approved by ' . $model->mdUser->fullname . 
                            '<br>@ ' . MyFormatter::asDateTime_ReaddmYHi($model->md_approval_date) . '</span>';
                    } elseif ($model->md_approval_status == \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL) {
                        return Html::a('Approve', ['director-approve-one-quotation', 'id' => $model->id], [
                            'class' => 'btn btn-sm btn-success',
                            'title' => 'Approve this quotation',
                            'data-confirm' => 'Are you sure you want to approve this quotation?',
                            'data-method' => 'post'
                        ]);
                    }
                    return null;
                }
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
                    Html::checkbox('select_all', false, ['id' => 'select-all', 'style' => 'margin:0;']),
                'headerOptions' => ['class' => 'col-sm-1 text-center'],
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'checkboxOptions' => function ($model) {
                    if ($model->md_approval_status == \frontend\models\projectquotation\QuotationPdfMasters::QUOTATION_GET_DIRECTOR_APPROVAL) {
                        return ['value' => $model->id, 'class' => 'my-checkbox'];
                    }
                    return ['style' => 'display:none'];
                },
            ],
        ],
    ]); ?>

    <?php \yii\widgets\Pjax::end(); ?>
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
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

#loading-overlay .fa-spinner {
    animation: pulse 1.5s ease-in-out infinite;
}
</style>

<script>
(function () {
    const pjaxContainer = '#pjax-quotation-grid';
    let selectedIds = new Set();
    let selectAllActive = false;
    let excludedIds = new Set();

    // ✅ Loading overlay functions
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
            if (this.checked) excludedIds.delete(id);
            else excludedIds.add(id);
        } else {
            if (this.checked) selectedIds.add(id);
            else selectedIds.delete(id);
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

    // Approve selected with enhanced loading feedback
    $(document).on('click', '#approve-selected', function (e) {
        e.preventDefault();

        let dataToSend = {};
        let confirmMessage = '';
        let itemCount = 0;

        if (selectAllActive) {
            dataToSend.selectAll = true;
            dataToSend.excludedIds = Array.from(excludedIds);

            const excludeCount = excludedIds.size;
            confirmMessage = excludeCount > 0
                ? `Are you sure you want to approve ALL quotations except ${excludeCount} unchecked item(s)?`
                : 'Are you sure you want to approve ALL active quotations?';
            
            itemCount = 'all';
        } else {
            const ids = Array.from(selectedIds);
            if (ids.length === 0) {
                alert('Please select at least one quotation to approve.');
                return;
            }
            dataToSend.ids = ids;
            itemCount = ids.length;
            confirmMessage = `Are you sure you want to approve ${ids.length} selected quotation(s)?`;
        }

        if (!confirm(confirmMessage))
            return;

        const $button = $(this);
        const originalText = $button.html();
        
        // Disable button and show loading
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        isProcessing = true;
        
        // Show loading overlay
        if (itemCount === 'all') {
            showLoading('Processing all quotations... This may take a while.');
        } else {
            showLoading(`Processing ${itemCount} quotation(s)...`);
        }

        $.ajax({
            url: '<?= \yii\helpers\Url::to(['director-approve-quotation']) ?>',
            type: 'POST',
            data: dataToSend,
            dataType: 'json',
            timeout: 300000, // 5 minutes timeout for large batches
            success: function (response) {
                updateLoadingMessage('Approval successful! Reloading page...');
                
                // ✅ Clear selections
                selectedIds.clear();
                selectAllActive = false;
                excludedIds.clear();
                isProcessing = false;
                
                // ✅ Short delay before reload to show success message
                setTimeout(function() {
                    location.reload(); // triggers FlashHandler::success()
                }, 1000);
            },
            error: function (xhr, status, error) {
                isProcessing = false;
                hideLoading();
                
                let errorMessage = 'Server error while approving: ';
                
                // ✅ Better error handling
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
                $button.prop('disabled', false).html(originalText);
            },
            complete: function () {
                // Note: don't hide loading here because we're reloading the page on success
            }
        });
    });

})();
</script>