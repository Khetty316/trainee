<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

$this->title = 'Quotation Templates';
$this->params['breadcrumbs'][] = $this->title;

$deactivateUrl = \yii\helpers\Url::to(['deactivate-selected']);
$gridId = 'template-grid';
?>

<div class="project-qrevisions-template-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary mb-2']) ?>
    <?=
    Html::button('Deactivate Selected', [
        'class' => 'btn btn-warning float-right mb-2',
        'id' => 'deactivate-selected'
    ])
    ?>
    <?php \yii\widgets\Pjax::begin(['id' => 'pjax-template-grid']); ?>

    <?=
    GridView::widget([
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
                'attribute' => 'revision_description',
                'format' => 'raw',
                'value' => fn($model) => Html::a($model->revision_description, ['/projectqtemplate/viewpqrevision', 'id' => $model->id]),
            ],
            'remark:ntext',
            [
                'attribute' => 'currency_id',
                'format' => 'raw',
                'label' => 'Currency',
                'contentOptions' => ['class' => 'tdnowrap px-3 text-right'],
                'headerOptions' => ['class' => 'tdnowrap px-3 text-right'],
                'value' => fn($model) => $model->currency->currency_sign,
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'contentOptions' => ['class' => 'tdnowrap px-3 text-right'],
                'headerOptions' => ['class' => 'tdnowrap px-3 text-right'],
                'value' => fn($model) => MyFormatter::asDecimal2_emptyZero($model->amount),
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => fn($model) => $model->createdBy->fullname ?? null,
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = common\models\User::findOne($model->updated_by);
                    return $user ? $user->fullname : null;
                },
            ],
            [
                'attribute' => 'is_active',
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
                'headerOptions' => ['class' => 'text-center'],
                'filter' => \frontend\models\projectquotation\ProjectQRevisionsTemplate::IS_ACTIVE,
                'value' => fn($model) => \frontend\models\projectquotation\ProjectQRevisionsTemplate::IS_ACTIVE_HTML[$model->is_active] ?? null,
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
                Html::checkbox('select_all', false, [
                    'id' => 'select-all',
                    'style' => 'margin:0;'
                ]),
                'headerOptions' => ['class' => 'col-sm-1 text-center'],
                'contentOptions' => ['class' => 'col-sm-1 text-center'],
                'checkboxOptions' => function ($model) {
                    return $model->is_active == 1 ? ['value' => $model->id, 'class' => 'my-checkbox'] : ['style' => 'display:none'];
                },
            ],
        ],
    ]);
    ?>

    <?php \yii\widgets\Pjax::end(); ?>
</div>

<script>
    (function () {
        const pjaxContainer = '#pjax-template-grid';
        let selectedIds = new Set();
        let selectAllActive = false;
        let excludedIds = new Set();

        // --- Reapply checked states after PJAX reload ---
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

        // --- Checkbox change handler (individual) ---
        $(document).on('change', pjaxContainer + ' .my-checkbox', function () {
            const id = String($(this).val());

            if (selectAllActive) {
                if (this.checked) {
                    excludedIds.delete(id);
                } else {
                    excludedIds.add(id);
                }
            } else {
                if (this.checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }

                const visible = $(pjaxContainer).find('.my-checkbox:visible');
                const allVisibleChecked = visible.length > 0 && visible.length === visible.filter(':checked').length;
                $(pjaxContainer).find('#select-all').prop('checked', allVisibleChecked);
            }
        });

        // --- Select All toggle ---
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

        // --- Reapply checks after PJAX reload ---
        $(document).on('pjax:end', function () {
            setTimeout(reapplyChecks, 50);
        });

        // --- Deactivate selected ---
        $(document).on('click', '#deactivate-selected', function (e) {
            e.preventDefault();

            let dataToSend = {};
            let confirmMessage = '';

            if (selectAllActive) {
                dataToSend.selectAll = true;
                dataToSend.excludedIds = Array.from(excludedIds);

                const excludeCount = excludedIds.size;
                confirmMessage = excludeCount > 0
                        ? `Are you sure you want to deactivate ALL templates except ${excludeCount} unchecked item(s)?`
                        : 'Are you sure you want to deactivate ALL active templates?';
            } else {
                const ids = Array.from(selectedIds);
                if (ids.length === 0) {
                    alert('Please select at least one template to deactivate.', 'warning');
                    return;
                }
                dataToSend.ids = ids;
                confirmMessage = `Are you sure you want to deactivate ${ids.length} selected template(s)?`;
            }

            if (!confirm(confirmMessage))
                return;

            const $button = $(this);
            const originalText = $button.html();
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '<?= $deactivateUrl ?>',
                type: 'POST',
                data: dataToSend,
                dataType: 'json',
                success: function (response) {
                    // Reset selection state
                    selectedIds.clear();
                    selectAllActive = false;
                    excludedIds.clear();

                    // Always reload to show FlashHandler message
                    location.reload();
                },
                error: function (xhr, status, error) {
                    alert('Server error while deactivating: ' + error);
                    $button.prop('disabled', false).html(originalText);
                },
                complete: function () {
                    setTimeout(() => {
                        $button.prop('disabled', false).html(originalText);
                    }, 500);
                }
            });
        });
    })();
</script>