<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyFormatter;
use yii\widgets\Pjax;
use frontend\models\working\leavemgmt\VMasterLeaveBreakdown;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\office\leave\LeaveMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'All Leave';
//$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];
//$this->params['breadcrumbs'][] = $this->title;
?>

<div style="overflow: auto">
    <?php Pjax::begin() ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'options' => [
            'id' => 'grid',
            'style' => 'width:100%;overflow:hidden;'
        ],
        'columns' => [
            [
                'attribute' => 'break_id',
                'label' => 'Leave ID',
                'headerOptions' => ['style' => 'width:4%;'],
            ],
            'leave_code',
            [
                'attribute' => 'requestor',
            ],
            [
                'attribute' => 'leave_type_name',
                'label' => 'Leave Type',
            ],
            [
                'attribute' => 'reason',
                'label' => 'Reason',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:25%;'],
                'value' => function ($data) {
                    $text = $data->reason . '.<br/> ' . '<span class = "text-info">Applied at : </span>' . MyFormatter::asDateTime_ReaddmYHi($data->created_at);
                    return $text;
                },
            ],
            [
                'attribute' => 'start_date',
                'label' => 'Date',
                'format' => 'raw',
                'headerOptions' => ['style' => 'width:15%;'],
                'value' => function ($data) {
                    $str = 'From: ' . MyFormatter::asDate_Read($data->start_date) . $data->start_sec_name . ' (' . MyFormatter::asDay_Read($data->start_date) . ') '
                            . '<br/>'
                            . 'To: ' . MyFormatter::asDate_Read($data->end_date) . $data->end_sec_name . ' (' . MyFormatter::asDay_Read($data->end_date) . ') ';
                    return$str;
                }
            ],
            [
                'attribute' => 'total_days',
                'contentOptions' => ['style' => 'width:3%; text-align: center;'],
            ],
            [
                'attribute' => 'support_doc',
                'label' => 'Attachment',
                'format' => 'raw',
                'filter' => false,
                'value' => function ($data) {
                    return $data->support_doc == '' ? '' : Html::a("<i class='far fa-file-alt fa-lg' ></i>", "#",
                            [
                                'title' => "Click to view me",
                                "value" => ("/working/leavemgmt/get-file?filename=" . urlencode($data->support_doc)),
                                "class" => "modalButtonPdf m-2"]);
                }
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'multiple' => true,
                'contentOptions' => [
                    'class' => 'checkBoxRecord',
                    'style' => 'text-align: center; vertical-align: middle; transform: scale(1.5); padding: 10px;',
                ],
                'headerOptions' => [
                    'style' => 'text-align: center; vertical-align: middle; transform: scale(1.5); padding: 10px;',
                ],
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->break_id];
                }
            ],
        ],
    ]);
    ?>
    <?php Pjax::end() ?>
</div>
