<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\ProjectProduction\ProjectProductionMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Master Project List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-production-master-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
    </p>

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
                [
                    'attribute' => 'project_production_code',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->project_production_code, ['view-production-main', 'id' => $model->id]);
                    }
                ],
                [
                    'attribute' => 'name',
                    'contentOptions' => ['style' => 'white-space:normal!important']
                ],
//                [
//                    'attribute' => 'client_id',
//                    'contentOptions' => ['class' => 'col-sm-1'],
//                    'headerOptions' => ['class' => 'col-sm-1'],
//                    'filter' => \frontend\models\client\Clients::getDropDownList(),
//                    'value' => function ($model) {
//                        return $model->clientName;
//                    }
//                ],
                [
                    'attribute' => 'client_id',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'headerOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'filter' => \yii\jui\AutoComplete::widget([
                        'model' => $searchModel,
                        'attribute' => 'client_id',
                        'clientOptions' => [
                            'source' => \frontend\models\client\Clients::getAutocompleteList(),
                            'minLength' => 1,
                            'autoFill' => true,
                            'select' => new \yii\web\JsExpression("function(event, ui) { 
                if(ui.item) {
                    // Show the name instead of ID
                    $(this).val(ui.item.label);
                    // Store the ID in a data attribute
                    $(this).data('selected-id', ui.item.id);
                    
                    // Update the actual hidden input with ID
                    var inputName = $(this).attr('name');
                    var hiddenInput = $('input[name=\"' + inputName + '\"][type=\"hidden\"]');
                    if (hiddenInput.length === 0) {
                        $(this).after('<input type=\"hidden\" name=\"' + inputName + '\" value=\"' + ui.item.id + '\">');
                        $(this).removeAttr('name');
                    } else {
                        hiddenInput.val(ui.item.id);
                    }
                    
                    $(this).closest('form').submit();
                }
                return false;
            }"),
                            'focus' => new \yii\web\JsExpression("function(event, ui) {
                // Show name on focus/hover
                $(this).val(ui.item.label);
                return false;
            }"),
                            'delay' => 300,
                        ],
                        'options' => [
                            'class' => 'form-control client-autocomplete',
                            'placeholder' => 'Search Client',
//            'autocomplete' => 'off',
                            'value' => $searchModel->client_id ? $searchModel->getClientName() : '', // Show name if already selected
                        ]
                    ]),
                    'value' => function ($model) {
                        return $model->clientName;
                    }
                ],
                [
                    'attribute' => 'remark',
                    'contentOptions' => ['style' => 'white-space:normal!important']
                ],
                [
                    'attribute' => 'component_percentage',
                    'contentOptions' => ['class' => 'text-right'],
                    'value' => function ($model) {
                        return MyFormatter::asDecimal2_emptyZero($model->component_percentage) . " %";
                    }
                ],
//                [
//                    'attribute' => 'fab_complete_percent',
//                    'contentOptions' => ['class' => 'text-right'],
//                    'value' => function ($model) {
//                        return MyFormatter::asDecimal2_emptyZero($model->fab_complete_percent) . " %";
//                    }
//                ],
//                [
//                    'attribute' => 'elec_complete_percent',
//                    'contentOptions' => ['class' => 'text-right'],
//                    'value' => function ($model) {
//                        return MyFormatter::asDecimal2_emptyZero($model->elec_complete_percent) . " %";
//                    }
//                ],
                [
                    'attribute' => 'fab_complete_percent',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->has_fab_tasks == 0) {
                            return '-';
                        }

                        $fabPercent = $model->production_fab_complete_percent ?? $model->fab_complete_percent;
                        $percent = $fabPercent > 100 ? 100 : $fabPercent;
                        $task = Html::a('<i class="fas fa-external-link-alt"></i>', ['/fab-task/index-fab-project-panels', 'id' => $model->id]);
                        return MyFormatter::asDecimal2_emptyZero($percent) . " % " . $task;
                    }
                ],
                [
                    'attribute' => 'elec_complete_percent',
                    'contentOptions' => ['class' => 'text-right'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model->has_elec_tasks == 0) {
                            return '-';
                        }

                        $elecPercent = $model->production_elec_complete_percent ?? $model->elec_complete_percent;
                        $percent = $elecPercent > 100 ? 100 : $elecPercent;
                        $task = Html::a('<i class="fas fa-external-link-alt"></i>', ['/elec-task/index-elec-project-panels', 'id' => $model->id]);
                        return MyFormatter::asDecimal2_emptyZero($percent) . " % " . $task;
                    }
                ],
                [
                    'attribute' => 'quotation_id',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Html::a($model->quotation->quotation_display_no . ' <i class="fas fa-external-link-alt"></i>', ['/projectquotation/view-projectquotation', 'id' => $model->quotation_id], ['target' => '_blank']);
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'label' => 'Pushed By',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => function ($model) {
                        return ($model->createdBy->fullname);
                    }
                ],
                //'client_id',
                //'proj_prod_category',
//                [
//                    'attribute' => 'created_at',
//                    'label' => 'Created Date',
//                    'contentOptions' => ['class' => 'col-sm-1'],
//                    'format' => ['date', 'php:Y-m-d'],
//                    'value' => function ($model) {
//                        return $model->created_at;
//                    }
//                ],
                [
                    'attribute' => 'created_at',
                    'label' => 'Created Date',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'value' => function ($model) {
                        return substr($model->created_at, 0, 10);
                    }
                ],
                // before plc
//                [
//                    'attribute' => 'current_target_date',
//                    'format' => 'raw',
//                    'contentOptions' => ['class' => 'text-center'],
//                    'value' => function ($model) {
//                        if (!$model->current_target_date) {
//                            return '-';
//                        }
//
//                        $today = new \DateTime('today');
//                        $target = (new \DateTime($model->current_target_date))->setTime(0, 0);
//                        $diff = (int) $today->diff($target)->format('%r%a');
//
//                        // Determine actual percent considering has_tasks
//                        $fabPercent = ($model->has_fab_tasks == 0) ? 100 : ($model->production_fab_complete_percent ?? $model->fab_complete_percent);
//                        $elecPercent = ($model->has_elec_tasks == 0) ? 100 : ($model->production_elec_complete_percent ?? $model->elec_complete_percent);
//
//                        // default styles
//                        $bg = 'transparent';
//                        $clr = '#000';
//
//                        $noTasksAtAll = ($model->has_fab_tasks == 0 && $model->has_elec_tasks == 0);
//
//                        if (!$noTasksAtAll && $fabPercent == 100 && $elecPercent == 100) {
//                            $bg = '#28a745'; // green
//                            $clr = '#fff';
//                        } else {
//                            if ($diff < 0) {
//                                $bg = '#dc3545'; // red
//                                $clr = '#fff';
//                            } elseif ($diff <= 4) {
//                                $bg = '#ffc107'; // yellow
//                                $clr = '#000';
//                            } 
//                        }
//
//                        return Html::tag(
//                                'span',
//                                MyFormatter::asDate_Read($model->current_target_date),
//                                [
//                                    'class' => 'text-center',
//                                    'style' => "background-color: {$bg}; color: {$clr}; padding: 3px 8px; border-radius: 4px;"
//                                ]
//                        );
//                    },
//                    'filter' => yii\jui\DatePicker::widget([
//                        'model' => $searchModel,
//                        'attribute' => 'current_target_date',
//                        'language' => 'en',
//                        'dateFormat' => 'php:d/m/Y',
//                        'options' => [
//                            'class' => 'form-control',
//                            'autocomplete' => 'off',
//                            'onchange' => '$("#w0").yiiGridView("applyFilter")',
//                        ],
//                        'clientOptions' => [
//                            'altFormat' => 'yy-mm-dd',
//                            'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'current_target_date'),
//                        ]
//                    ]),
//                ],
                        //all
                [
                    'attribute' => 'current_target_date',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'label' => '',
                    'headerOptions' => ['class' => 'text-center'],
                    'header' => (function () use ($dataProvider) {
                        $sort = $dataProvider->getSort();
                        $sortLink = ($sort !== false && $sort->hasAttribute('current_target_date')) ? $sort->link('current_target_date', ['label' => 'Target Date']) : 'Target Date';

                        return $sortLink . ' ' . Html::tag('i', '', [
                            'class' => 'fas fa-info-circle text-muted',
                            'style' => 'cursor:pointer;',
                            'data-toggle' => 'popover',
                            'data-html' => 'true',
                            'data-trigger' => 'click',
                            'data-placement' => 'bottom',
                            'title' => 'Status Legend',
                            'data-content' =>
                            MyFormatter::renderTierLegend('In Progress', MyFormatter::IN_PROGRESS_TIERS) .
                            MyFormatter::renderTierLegend('Due Soon', MyFormatter::DUE_SOON_TIERS) .
                            MyFormatter::renderTierLegend('Overdue', MyFormatter::OVERDUE_TIERS) .
                            MyFormatter::renderTierLegend('Production Completed', MyFormatter::COMPLETION_TIERS) .
                            MyFormatter::renderTierLegend('Delivered On Time', MyFormatter::EARLY_TIERS) .
                            MyFormatter::renderTierLegend('Delivered Late', MyFormatter::LATE_TIERS),
                        ]);
                    })(),
                    'value' => function ($model) {
                        if (!$model->current_target_date) {
                            return '-';
                        }

                        $target = (new \DateTime($model->current_target_date))->setTime(0, 0);
                        $today = new \DateTime('today');
                        $diff = (int) $today->diff($target)->format('%r%a');

                        $fabPercent = ($model->has_fab_tasks == 0) ? 100 : ($model->production_fab_complete_percent ?? $model->fab_complete_percent);
                        $elecPercent = ($model->has_elec_tasks == 0) ? 100 : ($model->production_elec_complete_percent ?? $model->elec_complete_percent);
                        $noTasksAtAll = ($model->has_fab_tasks == 0 && $model->has_elec_tasks == 0);
                        $bg = 'lightgray';
                        $clr = '#000';

                        // 1. Delivered
                        if ($model->delivery_status == 3 && !empty($model->delivered_at)) {
                            $delivered = (new \DateTime($model->delivered_at))->setTime(0, 0);
                            $daysDiff = (int) $target->diff($delivered)->format('%r%a');

                            if ($daysDiff <= 0) {
                                $bg = MyFormatter::getTierColor(MyFormatter::EARLY_TIERS, abs($daysDiff), 'max');
                            } else {
                                $bg = MyFormatter::getTierColor(MyFormatter::LATE_TIERS, $daysDiff, 'max');
                            }

                            $clr = MyFormatter::getContrastTextColor($bg);
                        }
                        // 2. Production completed — tiered by how close completion was to target date
                        elseif (!$noTasksAtAll && $fabPercent == 100 && $elecPercent == 100) {
                            $latestCompletionDate = null;

                            foreach ($model->projectProductionPanels as $panel) {
                                $completeDate = frontend\models\ProjectProduction\ProjectProductionPanels::getPanelCompletionDate($panel);
                                if ($completeDate !== null) {
                                    $latestCompletionDate = ($latestCompletionDate === null || $completeDate > $latestCompletionDate) ? $completeDate : $latestCompletionDate;
                                }
                            }

                            if ($latestCompletionDate !== null) {
                                $completedAt = (new \DateTime($latestCompletionDate))->setTime(0, 0);
                                $daysDiff = (int) $target->diff($completedAt)->format('%r%a'); // + = completed after target, - = before

                                $bg = MyFormatter::getTierColor(MyFormatter::COMPLETION_TIERS, abs($daysDiff), 'max');
                                $clr = MyFormatter::getContrastTextColor($bg);
                            } else {
                                // fallback if no completion date could be determined
                                $bg = '#007bff';
                                $clr = '#fff';
                            }
                        }
                        // 3. Not completed yet and not internal project
                        else if (!$model->internal_project) {
                            if ($diff < 0) {
                                $bg = MyFormatter::getTierColor(MyFormatter::OVERDUE_TIERS, abs($diff), 'max');
                                $clr = MyFormatter::getContrastTextColor($bg);
                            } elseif ($diff <= 5) {
                                $bg = MyFormatter::getTierColor(MyFormatter::DUE_SOON_TIERS, $diff, 'min');
                                $clr = MyFormatter::getContrastTextColor($bg);
                            }
                        }

                        return Html::tag(
                                'span',
                                MyFormatter::asDate_Read($model->current_target_date),
                                [
                                    'class' => 'text-center',
                                    'style' => "background-color: {$bg}; color: {$clr}; padding:3px 8px; border-radius:4px;"
                                ]
                        );
                    },
                    'filter' => yii\jui\DatePicker::widget([
                        'model' => $searchModel,
                        'attribute' => 'current_target_date',
                        'language' => 'en',
                        'dateFormat' => 'php:d/m/Y',
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off',
                            'onchange' => '$("#w0").yiiGridView("applyFilter")',
                        ],
                        'clientOptions' => [
                            'altFormat' => 'yy-mm-dd',
                            'altField' => '#' . \yii\helpers\Html::getInputId($searchModel, 'current_target_date'),
                        ]
                    ]),
                ],
            //'updated_by',
            //'updated_at',
            ],
        ]);
        ?>
    </div>

</div>
<?php
$this->registerJs(<<<'JS'

var popoverReinitTimeout = null;

function initPopovers() {
    clearTimeout(popoverReinitTimeout);

    popoverReinitTimeout = setTimeout(function () {

        // Remove existing popovers
        $('[data-toggle="popover"]').popover('dispose');

        // Re-initialize
        $('[data-toggle="popover"]').popover({
            html: true,
            container: 'body',
            trigger: 'click',
            placement: 'bottom',
            sanitize: false
        });

    }, 150);
}

// Initial page load
$(document).ready(function () {
    initPopovers();
});

// Re-initialize after any GridView AJAX filtering
$(document).ajaxComplete(function () {
    initPopovers();
});

// PJAX support
$(document).on('pjax:end pjax:success pjax:complete', function () {
    initPopovers();
});

// Observe DOM changes (GridView refresh)
var gridContainer = document.querySelector('.table-responsive');

if (gridContainer && window.MutationObserver) {
    var observer = new MutationObserver(function () {
        initPopovers();
    });

    observer.observe(gridContainer, {
        childList: true,
        subtree: true
    });
}

// Hide popover when clicking outside
$(document).on('click', function (e) {
    $('[data-toggle="popover"]').each(function () {
        var $this = $(this);

        if (
            !$this.is(e.target) &&
            $this.has(e.target).length === 0 &&
            $('.popover').has(e.target).length === 0
        ) {
            $this.popover('hide');
        }
    });
});

JS
);
?>
<?php
$this->registerCss(<<<CSS
    .popover {
    max-width: 500px;
    width: 500px;
}
    .popover-header {
        font-size: 1.05rem;
        font-weight: 600;
    }
    .popover-body {
        font-size: 1rem;
    }
CSS
);
?>