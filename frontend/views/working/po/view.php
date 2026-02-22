<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\myTools\MyFormatter;

/* @var $this yii\web\View */
/* @var $model app\models\working\po\PurchaseOrderMaster */

$this->title = $model->po_number;
$this->params['breadcrumbs'][] = ['label' => 'Purchase Order', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="container">
    <div class=" justify-content-center">
        <div class="col-12">
            <p>
                <?= Html::a('Update <i class="far fa-edit"></i>', ['update', 'id' => $model->po_id], ['class' => 'btn btn-success']) ?>
                <?php
//               echo Html::a('Delete', ['delete', 'id' => $model->po_id], [
//                    'class' => 'btn btn-danger',
//                    'data' => [
//                        'confirm' => 'Are you sure you want to delete this item?',
//                        'method' => 'post',
//                    ],
//                ])
                ?>
            </p>

            <?=
            DetailView::widget([
                'model' => $model,
                'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
                'options' => ['class' => 'table table-striped table-bordered detail-view fix-width table-sm'],
                'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
                'attributes' => [
                    'po_number',
                    ['attribute' => 'po_date',
                        'value' => function($model) {
                            return MyFormatter::asDate_Read($model->po_date);
                        }
                    ],
                    [
                        'attribute' => 'quotation_master_id',
                        'format' => 'raw',
                        'label' => 'Quotation ID',
                        'value' => function($model) {
                            if ($model->quotation_master_id) {
                                return $model->quotation_master_id . " " . Html::a('<i class="fas fa-external-link-alt"></i>', ["/quotation/proc-view-process-quotation-detail", 'id' => $model->quotation_master_id], ['target' => "_blank", "class" => 'bold']);
                            } else {
                                return $model->quotation_master_id;
                            }
                        }
                    ],
                    'project_code',
                    ['attribute' => 'amount',
                        'value' => function($data) {
                            $amt = $data->currency0->currency_sign . " " . MyFormatter::asDecimal2($data->amount);
                            return $amt;
                        }
                    ],
                    'po_material_desc',
                    'po_lead_time',
                    'po_etd',
                    'po_transporter',
                    [
                        'attribute' => 'po_pic',
                        'value' => function($model) {
                            return $model->poPic ? $model->poPic->fullname : "";
                        }
                    ],
                    [
                        'attribute' => 'po_address',
                        'value' => function($model) {

                            return $model->poAddress ? $model->poAddress->address_name : "";
                        }
                    ],
                    [
                        'attribute' => 'po_receive_status',
                        'value' => function($model) {
                            if ($model->po_receive_status == 1) {
                                return "Yes";
                            } else {
                                return "No";
                            }
                        }
                    ],
                    [
                        'label' => 'File ',
                        'format' => 'raw',
                        'value' => function($modal) {
                            return Html::a('<i class="far fa-file-alt fa-lg" aria-hidden="true"></i>', "/working/po/get-file?filename=" . urlencode($modal->po_upload_file),
                                            ['target' => "_blank"]);
                        }
                    ],
                    'remarks:ntext',
                    ['attribute' => 'created_at',
                        'value' => function($model) {
                            return MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                        }
                    ],
                    [
                        'attribute' => 'created_by',
                        'value' => function($model) {
                            return $model->createdBy->fullname;
                        }
                    ],
//                    'update_at',
//                    'updated_by',
                ],
            ])
            ?>

        </div>
    </div>   
</div>