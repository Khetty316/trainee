<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
?>
<?php
$this->registerCss("
    .ui-autocomplete {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
    }
    
    .ui-menu-item {
        font-size: 16px;
        padding: 3px 8px;
    }
    
//    .ui-menu-item:hover {
////        background-color: #f5f5f5;
//    }
");
?>
<div class="work-assignment-master-index">

    <?= $this->render('__navbarWorkAssignment', ['pageKey' => '1']) ?>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>
    <?php
    echo GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'layout' => "
{summary}
{pager}
<div class='table-scroll' style='margin-bottom:20px;'>
    {items}
</div>
{pager}
",
        'pager' => [
            'class' => yii\bootstrap4\LinkPager::class,
            'firstPageLabel' => '<i class="fa fa-angle-double-left"></i> First Page',
            'prevPageLabel' => '<i class="fa fa-angle-left"></i> Prev',
            'nextPageLabel' => 'Next <i class="fa fa-angle-right"></i>',
            'lastPageLabel' => 'Last Page <i class="fa fa-angle-double-right"></i>',
            'maxButtonCount' => 5,
        ],
        'tableOptions' => [
            'class' => 'table table-hover table-striped table-bordered table-sm',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'project_production_code',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->project_production_code, ['index-elec-project-panels', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'name',
                'contentOptions' => ['style' => 'white-space:normal!important']
            ],
//            [
//                'attribute' => 'client_id',
//                'contentOptions' => ['class' => 'col-sm-1'],
//                'headerOptions' => ['class' => 'col-sm-1'],
//                'filter' => \frontend\models\client\Clients::getDropDownList(),
//                'value' => function ($model) {
//                    return $model->clientName;
//                }
//            ],
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
//            [
//                'attribute' => 'elec_complete_percent',
//                'contentOptions' => ['class' => 'text-right'],
//                'value' => function ($model) {
//                    return MyFormatter::asDecimal2_emptyZero($model->elec_complete_percent) . " %";
//                }
//            ],
//            [
//                'attribute' => 'production_elec_complete_percent',
//                'label' => 'Electrical Complete %',
//                'contentOptions' => ['class' => 'text-right'],
//                'value' => function ($model) {
//                    $elecCompletePercent = MyFormatter::asDecimal2_emptyZero($model->production_elec_complete_percent);
//                    $percent = $elecCompletePercent > 100 ? 100.00 : $elecCompletePercent;
//                    return MyFormatter::asDecimal2_emptyZero($percent) . " %";
//                }
//            ],
            [
                'attribute' => 'elec_complete_percent',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    if ($model->has_elec_tasks == 0) {
                        return '-';
                    }

                    $elecPercent = $model->production_elec_complete_percent;
                    $percent = $elecPercent > 100 ? 100.00 : $elecPercent;
                    return MyFormatter::asDecimal2_emptyZero($percent) . " %";
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
            [
    'attribute' => 'current_target_date',
    'format' => 'raw',

    'headerOptions' => [
        'class' => 'sticky-target-completion-date text-center',
        'style' => 'width:180px; min-width:180px;',
    ],

    'filterOptions' => [
        'class' => 'sticky-target-completion-date',
    ],

    'contentOptions' => [
        'class' => 'sticky-target-completion-date text-center',
        'style' => 'width:180px; min-width:180px;',
    ],

    'value' => function ($model) {
        if (!$model->current_target_date) {
            return '-';
        }

        $today = new \DateTime('today');
        $target = (new \DateTime($model->current_target_date))->setTime(0, 0);
        $diff = (int) $today->diff($target)->format('%r%a');

        $elecPercent = ($model->has_elec_tasks == 0)
                ? 100
                : ($model->production_elec_complete_percent ?? $model->elec_complete_percent);

        $bg = 'transparent';
        $clr = '#000';

        $noTasksAtAll = ($model->has_elec_tasks == 0 && $model->has_fab_tasks == 0);

        if (!$noTasksAtAll && $elecPercent == 100) {
            $bg = '#28a745';
            $clr = '#fff';
        } else {
            if ($diff < 0) {
                $bg = '#dc3545';
                $clr = '#fff';
            } elseif ($diff <= 4) {
                $bg = '#ffc107';
                $clr = '#000';
            }
        }

        return Html::tag(
            'span',
            MyFormatter::asDate_Read($model->current_target_date),
            [
                'style' => "background-color: {$bg}; color: {$clr}; padding:3px 8px; border-radius:4px;",
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
    ]]));
    ?>
</div>

<style>
    .table-scroll {
        max-height: calc(100vh - 320px);
        overflow: auto;
    }

    .table-scroll table {
        width: max-content;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-scroll thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 5;
    }

    .table-scroll th.sticky-action,
    .table-scroll td.sticky-action {
        position: sticky;
        right: 0;
        background: #fff;
        white-space: nowrap;
    }

    .table-scroll thead th.sticky-action {
        z-index: 7;
    }

    .table-scroll tbody td.sticky-action {
        z-index: 2;
    }

    .table-scroll th.sticky-action,
    .table-scroll td.sticky-action {
        box-shadow: -2px 0 6px rgba(0,0,0,.12);
    }
    
    .table-scroll th.sticky-target-completion-date,
.table-scroll td.sticky-target-completion-date {
    position: sticky;
    right: 80px;
    background: #fff;
    z-index: 6;
}

.table-scroll th.sticky-action,
.table-scroll td.sticky-action {
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 7;
}

.table-scroll th.sticky-target-completion-date,
.table-scroll td.sticky-target-completion-date {
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 6;
}

.table-scroll th.sticky-target-completion-date,
.table-scroll td.sticky-target-completion-date {
    position: sticky;
    right: 0;              /* Last column */
    background: #fff;
    z-index: 6;
}

.table-scroll thead th.sticky-target-completion-date {
    z-index: 7;
}
</style>