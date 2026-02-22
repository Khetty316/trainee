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
        <?= Html::a('Reset <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?> 
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
                        $task = Html::a('<i class="fas fa-external-link-alt"></i>', ['/fab-task/index-fab-project-panels', 'id' => $model->id]);
                        return MyFormatter::asDecimal2_emptyZero($fabPercent) . " % " . $task;
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
                        $task = Html::a('<i class="fas fa-external-link-alt"></i>', ['/elec-task/index-elec-project-panels', 'id' => $model->id]);
                        return MyFormatter::asDecimal2_emptyZero($elecPercent) . " % " . $task;
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
                [
                    'attribute' => 'current_target_date',
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'text-center'],
                    'value' => function ($model) {
                        if (!$model->current_target_date) {
                            return '-';
                        }

                        $today = new \DateTime('today');
                        $target = (new \DateTime($model->current_target_date))->setTime(0, 0);
                        $diff = (int) $today->diff($target)->format('%r%a');

                        // Determine actual percent considering has_tasks
                        $fabPercent = ($model->has_fab_tasks == 0) ? 100 : ($model->production_fab_complete_percent ?? $model->fab_complete_percent);
                        $elecPercent = ($model->has_elec_tasks == 0) ? 100 : ($model->production_elec_complete_percent ?? $model->elec_complete_percent);

                        // default styles
                        $bg = 'transparent';
                        $clr = '#000';

                        $noTasksAtAll = ($model->has_fab_tasks == 0 && $model->has_elec_tasks == 0);

                        if (!$noTasksAtAll && $fabPercent == 100 && $elecPercent == 100) {
                            $bg = '#28a745'; // green
                            $clr = '#fff';
                        } else {
                            if ($diff < 0) {
                                $bg = '#dc3545'; // red
                                $clr = '#fff';
                            } elseif ($diff <= 4) {
                                $bg = '#ffc107'; // yellow
                                $clr = '#000';
                            } 
                        }

                        return Html::tag(
                                'span',
                                MyFormatter::asDate_Read($model->current_target_date),
                                [
                                    'class' => 'text-center',
                                    'style' => "background-color: {$bg}; color: {$clr}; padding: 3px 8px; border-radius: 4px;"
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
