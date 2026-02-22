<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
?>
<!--<style>
    .grid-view td {
        white-space: inherit!important;
    }
</style>-->
<div class="work-assignment-master-index">

    <?= $this->render('__navbarWorkAssignment', ['pageKey' => '1']) ?>
    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>
    <?php
    echo GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'project_production_code',
                'contentOptions' => ['class' => 'col-sm-1'],
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::encode($model->project_production_code), ['index-fab-project-panels', 'id' => $model->id]);
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
//                'attribute' => 'fab_complete_percent',
//                'contentOptions' => ['class' => 'text-right'],
//                'value' => function ($model) {
//                    return MyFormatter::asDecimal2_emptyZero($model->fab_complete_percent) . " %";
//                }
//            ],
//            [
//                'attribute' => 'production_fab_complete_percent',
//                'label' => 'Fabrication Complete %',
//                'contentOptions' => ['class' => 'text-right'],
//                'value' => function ($model) {
//                    $fabCompletePercent = MyFormatter::asDecimal2_emptyZero($model->production_fab_complete_percent);
//                    $percent = $fabCompletePercent > 100 ? 100 : $fabCompletePercent;
//                    return MyFormatter::asDecimal2_emptyZero($percent) . " %";
//                }
//            ],
            [
                'attribute' => 'fab_complete_percent',
                'contentOptions' => ['class' => 'text-right'],
                'value' => function ($model) {
                    if ($model->has_fab_tasks == 0) {
                        return '-';
                    }

                    $fabPercent = $model->production_fab_complete_percent ?? $model->fab_complete_percent;
                    $percent = $fabPercent > 100 ? 100 : $fabPercent;
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

                    // default styles
                    $bg = 'transparent';
                    $clr = '#000';

                    $noTasksAtAll = ($model->has_fab_tasks == 0 && $model->has_elec_tasks == 0);

                    if (!$noTasksAtAll && $fabPercent == 100) {
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
    ]]));
    ?>

</div>
