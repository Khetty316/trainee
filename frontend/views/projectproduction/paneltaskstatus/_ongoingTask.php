<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\jui\DatePicker;

$dataProvider->sort = false;
$dataProvider->pagination = false;
?>
<div class="vfab-staff-production-index">

    <h4><?= Html::encode($this->title) ?></h4>

    <?=
    GridView::widget(array_merge(Yii::$app->params['gridViewCommonOption'], [
        'dataProvider' => $dataProvider,
        'options' => ['style' => 'overflow:auto;'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'task_type',
                'value' => function ($model) {
                    return $model->task_type == "elec" ? "Electrical" : "Fabrication";
                }
            ],
            [
                'attribute' => 'task_name',
            ],
            [
                'attribute' => 'panel_code',
                'headerOptions' => ['style' => 'width:17%'],
            ],
            [
                'attribute' => 'panel_description',
                'contentOptions' => ['style' => 'white-space: inherit!important'],
            ],
            'assigned_qty',
            'assigned_complete_qty',
            [
                'attribute' => 'assigned_start_date',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'assigned_start_date',
                    'language' => 'en',
                    'dateFormat' => 'php:d-m-Y',
                    'options' => ['class' => 'form-control'],
                ]),
                'value' => function ($model) {
                    return MyFormatter::asDate_Read($model->assigned_start_date);
                },
            ],
            [
                'attribute' => 'assigner_fullname',
                'headerOptions' => ['style' => 'width:15%'],
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->assigner_fullname . "<br/>" . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                }
            ],
        ],
    ]));
    ?>
</div>
<script>
    $(document).on('pjax:beforeSend', function (event, xhr, settings) {
        var formData = new URLSearchParams(settings.data);
        var dateInputName = 'VStaffProductionAllSearch[assigned_start_date]';
        var dateValue = formData.get(dateInputName);

        if (dateValue) {
            var parts = dateValue.split('/');
            var formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
            formData.set(dateInputName, formattedDate);
            settings.data = formData.toString();
        }
    });
</script>