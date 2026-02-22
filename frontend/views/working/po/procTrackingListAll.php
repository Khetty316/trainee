<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\jui\DatePicker;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $searchModel app\models\working\po\PurchaseOrderMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
?>
<div class="purchase-order-master-index">



    <?php
    echo $this->render('__PoNavBar', ['module' => 'po_tracking_list', 'pageKey' => '3']);
    $this->params['breadcrumbs'][] = ['label' => 'Procurement Tracking'];
    $this->params['breadcrumbs'][] = $this->title;
    ?>

    <p>
        <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
    </p>

    <?=
    GridView::widget([
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            [
                'attribute' => 'po_date',
                'value' => function($data) {
                    return $data->po_date == "" ? "" : MyFormatter::asDate_Read($data->po_date);
                },
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'po_date',
                    'language' => 'en',
                    'dateFormat' => 'dd-MM-yyyy',
//                    'dateFormat'=>'php:d/m/Y',
                    'options' => ['class' => 'form-control'],
                ]),
                'format' => 'html',
            ],
            [
                'attribute' => 'po_number',
                'format' => 'raw',
                'value' => function($model) {
                    $title = "Registered By: " . $model->created_by_fullname . "\n" . "At time: " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    $title .= "\nRemarks: " . $model->remarks;
                    return Html::a($model->po_number, "/working/po/view-individual?id=$model->po_id", ["title" => $title])
                            . Html::a("<i class='far fa-file-alt pl-2'></i>", "/working/po/get-file?filename=" .
                                    urlencode($model->po_upload_file), ['target' => "_blank", 'class' => 'pull-right mr-2', 'title' => $model->po_upload_file]);
                }
            ],
            [
                'attribute' => 'project_code',
                'format' => 'raw',
                'value' => function($model) {
                    $title = $model->project_name;
                    return Html::tag("span", $model->project_code, ["title" => $title]);
                }
            ],
            [
                'attribute' => 'amount',
                'format' => 'raw',
                'value' => function($data) {
                    $amt = $data->amount > 0 ? $data->currency_sign . " " . MyFormatter::asDecimal2($data->amount) : '';
                    return '<p class="text-right p-0 m-0">' . $amt . '</p>';
                }
            ],
            [
                'attribute' => 'po_material_desc',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => 'white-space:normal'
                ],
                'headerOptions' => [
                    'style' => 'white-space:nowrap'
                ],
                'label' => 'P.O. Material Descriptions',
                'value' => function($data) {
                    return $data->po_material_desc;
                }
            ],
            [
                'attribute' => 'po_lead_time',
                'format' => 'raw',
                'contentOptions' => [
                    'style' => 'white-space:normal'
                ],
                'headerOptions' => [
                    'style' => 'white-space:nowrap'
                ],
                'label' => 'P.O. Lead Time .',
                'value' => function($data) {
                    return $data->po_lead_time;
                }
            ],
            [
                'attribute' => 'po_etd',
//                <a href="/working/po/index?sort=po_lead_time" data-sort="po_lead_time">Po Lead Time</a>
                'label' => 'Est. Lead Time(Days/Wks)'
//                'header' => Html::a('Est. Lead Time <br> (Days/Wks)',"/working/po/index?sort=po_etd",["data-sort"=>"po_etd"])
            ],
            'po_transporter',
            'po_pic_fullname',
            [
                'attribute' => 'address_name',
                'filter' => $addressList
            ],
            [
                'attribute' => 'po_receive_status',
                'value' => function ($model) {
                    return $model->po_receive_status ? 'Yes' : 'No';
                },
                'filter' => array("1" => "Yes", "0" => "No"),
            ],
            'onsite_receive_by'

        //'po_upload_file',
        //'remarks:ntext',
        //'created_at',
        //'created_by',
        //'update_at',
        //'updated_by',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>


</div>
