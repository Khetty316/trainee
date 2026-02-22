<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\prodOtMealRecord\ProdOtMealRecordMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $model frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster */
/* @var $hideFilter boolean */
?>
<div class="prod-ot-meal-record-master-index">
    <p>
        <?php
        $notFinalize = ($model->status == \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::STATUS_NOT_FINALIZE);

        if ($notFinalize && !$hideFilter && $module === 'personal') {
            echo Html::button('Delete Selected', [
                'class' => 'btn btn-danger float-right mb-2',
                'id' => 'delete-selected'
            ]);

            echo Html::a('Add New Daily Record', ['add-new-record', 'id' => $model->id], [
                'class' => 'btn btn-success mr-1'
            ]);
        }

        if (!$hideFilter) {
            echo Html::a('Reset <i class="fas fa-search-minus"></i>', '?id=' . $model->id, [
                'class' => 'btn btn-primary'
            ]);
        }
        ?>
    </p>

    <div class="table-responsive">
        <?=
        GridView::widget([
            'id' => 'detail-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => !$hideFilter ? $searchModel : null,
            'pager' => false, // ✅ no pagination
            'summary' => false, // ✅ hide "Showing X of Y"
            'headerRowOptions' => ['class' => 'my-thead'],
            'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
            'layout' => "{summary}\n{pager}\n{items}\n{pager}",
            'columns' => array_filter([
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'receipt_date',
                    'contentOptions' => ['class' => 'col-sm-1 text-center'],
                    'enableSorting' => !$hideFilter, // ✅ disable sorting if hideFilter = true
                    'value' => function ($detail) {
                        return $detail->receipt_date ? Yii::$app->formatter->asDate($detail->receipt_date, 'php:d/m/Y') : '-';
                    },
                    'filter' => !$hideFilter ? (
                            Html::activeHiddenInput($searchModel, 'receipt_date', ['id' => 'receipt-date-hidden']) .
                            yii\jui\DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'receipt_date_display',
                                'language' => 'en',
                                'dateFormat' => 'php:d/m/Y',
                                'options' => ['class' => 'form-control', 'autocomplete' => 'off'],
                                'clientOptions' => [
                                    'altFormat' => 'yy-mm-dd',
                                    'altField' => '#receipt-date-hidden',
                                    'changeMonth' => true,
                                    'changeYear' => true,
                                ],
                            ])
                            ) : false,
                ],
                [
                    'attribute' => 'receipt_total_amount',
                    'contentOptions' => ['class' => 'text-right'],
                    'enableSorting' => !$hideFilter,
                    'format' => 'raw',
                    'value' => fn($detail) => MyFormatter::asDecimal2($detail->receipt_total_amount),
                    'filter' => !$hideFilter ? Html::activeTextInput($searchModel, 'receipt_total_amount', ['class' => 'form-control']) : false,
                ],
                [
                    'attribute' => 'total_staff',
                    'contentOptions' => ['class' => 'col-sm-1 text-center'],
                    'headerOptions' => ['class' => 'col-sm-1 text-center'],
                    'enableSorting' => !$hideFilter,
                    'value' => fn($detail) => $detail->total_staff,
                    'filter' => !$hideFilter ? Html::activeTextInput($searchModel, 'total_staff', ['class' => 'form-control']) : false,
                ],
                [
                    'attribute' => 'staff',
                    'label' => 'Selected Staffs',
                    'format' => 'raw',
                    'enableSorting' => false, // static column
                    'value' => function ($detail) {
                        if (empty($detail->staff))
                            return '-';
                        $staffList = explode(';', $detail->staff);
                        $returnStr = '';
                        foreach ($staffList as $key => $staffName) {
                            $returnStr .= ($key + 1) . ' - ' . Html::encode(trim($staffName)) . '<br/>';
                        }
                        return $returnStr;
                    },
                    'filter' => false,
                ],
                [
                    'attribute' => 'created_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'enableSorting' => !$hideFilter,
                    'format' => 'raw',
                    'value' => fn($detail) => "By " . ($detail->createdBy->fullname ?? '-') .
                    " @ " . MyFormatter::asDateTime_ReaddmYHi($detail->created_at),
                    'filter' => !$hideFilter ? (
                            Html::activeHiddenInput($searchModel, 'created_at', ['id' => 'created-at-hidden']) .
                            yii\jui\DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'created_at_display',
                                'language' => 'en',
                                'dateFormat' => 'php:d/m/Y',
                                'options' => ['class' => 'form-control', 'autocomplete' => 'off'],
                                'clientOptions' => [
                                    'altFormat' => 'yy-mm-dd',
                                    'altField' => '#created-at-hidden',
                                    'changeMonth' => true,
                                    'changeYear' => true,
                                ],
                            ])
                            ) : false,
                ],
                [
                    'attribute' => 'updated_at',
                    'contentOptions' => ['class' => 'col-sm-1'],
                    'enableSorting' => !$hideFilter,
                    'format' => 'raw',
                    'value' => function ($detail) {
                        if ($detail->updated_by === null)
                            return '-';
                        return "By " . ($detail->updatedBy->fullname ?? '-') .
                        " @ " . MyFormatter::asDateTime_ReaddmYHi($detail->updated_at);
                    },
                    'filter' => !$hideFilter ? (
                            Html::activeHiddenInput($searchModel, 'updated_at', ['id' => 'updated-at-hidden']) .
                            yii\jui\DatePicker::widget([
                                'model' => $searchModel,
                                'attribute' => 'updated_at_display',
                                'language' => 'en',
                                'dateFormat' => 'php:d/m/Y',
                                'options' => ['class' => 'form-control', 'autocomplete' => 'off'],
                                'clientOptions' => [
                                    'altFormat' => 'yy-mm-dd',
                                    'altField' => '#updated-at-hidden',
                                    'changeMonth' => true,
                                    'changeYear' => true,
                                ],
                            ])
                            ) : false,
                ],
                // View button
                (!$hideFilter && $notFinalize && $module === 'personal') ? [
                    'format' => 'raw',
                    'contentOptions' => ['class' => 'col-sm-1 text-center'],
                    'value' => function ($detail) {
                        if ($detail->deleted_by === null && $detail->deleted_at === null) {
                            return Html::a('View <i class="far fa-eye"></i>', ['view-detail', 'id' => $detail->id], [
                                        'class' => 'btn btn-sm btn-success mx-1',
                            ]);
                        }
                        return '';
                    },
                        ] : false,
                // Checkbox
                (!$hideFilter && $notFinalize && $module === 'personal') ? [
                    'class' => 'yii\grid\CheckboxColumn',
                    'header' => Html::tag('div', 'Select All', ['style' => 'margin-bottom:5px;']) .
                    Html::checkbox('selection_all', false, [
                        'id' => 'select-all-checkbox',
                        'style' => 'margin:0;',
                    ]),
                    'headerOptions' => ['class' => 'col-sm-1 text-center'],
                    'contentOptions' => ['class' => 'col-sm-1 text-center'],
                    'checkboxOptions' => function ($detail) use ($notFinalize) {
                        if ($notFinalize && $detail->deleted_by === null && $detail->deleted_at === null) {
                            return ['value' => $detail->id, 'class' => 'select-on-check-item'];
                        }
                        return ['style' => 'display:none', 'disabled' => true];
                    },
                        ] : false,
            ]),
        ])
        ?>
    </div>
</div>

<?php
$deleteUrl = \yii\helpers\Url::to(['delete-selected']);
$js = <<<JS
$('#delete-selected').on('click', function() {
    var keys = $('#detail-grid').yiiGridView('getSelectedRows');
    if (keys.length === 0) {
        alert('Please select at least one record to delete.');
        return;
    }

    if (confirm('Are you sure you want to delete the selected records?')) {
        $.post('$deleteUrl', {ids: keys}, function() {
            location.reload();
        });
    }
});
JS;
$this->registerJs($js);
?>
