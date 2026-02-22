<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\quotation\QuotationMasters;
use frontend\models\working\po\PurchaseOrderMaster;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\quotation\QuotationMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = 'Request For Quotation';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-masters-index">

    <?= $this->render('__RFQNavBar', ['module' => 'staff', 'pageKey' => '2']) ?>
    <p>
        <?= Html::a('New Request <i class="fas fa-plus"></i>', ['staff-request-quotation'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]);   ?>

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
            [
                'attribute' => 'id',
                'format' => 'raw',
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'label' => 'Request ID',
                'value' => function($model) {
                    return Html::a($model->id, ['staff-view-quotation-detail', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'project_code',
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            ],
            [
                'attribute' => 'file_reference',
                'format' => 'raw',
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell text-center'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'value' => function($model) {
                    if ($model->file_reference) {
                        return Html::a(" <i class='far fa-file-alt fa-lg' ></i>", ['get-file', 'id' => $model->id], ['target' => '_blank']);
                    } else {
                        return " - ";
                    }
                }
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'value' => function($model) {
                    return nl2br(Html::encode(substr($model->description, 0, 50))) . (strlen($model->description) > 50 ? "..." : "");
                }
            ],
            [
                'attribute' => 'overallStatus',
                'format' => 'raw',
                'label' => 'Status',
                'filter' => QuotationMasters::getStatusList(),
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'value' => function($model) {
                    return ($model->getStatus() == QuotationMasters::STS_REQ ? '<i class="fas fa-exclamation-triangle fa-lg text-danger"></i> ' : "") . $model->getStatus();
                }
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'label' => 'P.O.',
                'filterOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'value' => function($model) {
                    $poLink = ' - '; //$model->request_is_complete?;
                    if ($model->request_is_complete && $model->purchaseOrderMasters) {
                        $po = $model->purchaseOrderMasters[0];
                        $poLink = Html::a($po->po_number, "/working/po/view-individual?id=$po->po_id")
                                . Html::a("<i class='far fa-file-alt pl-2'></i>", "/working/po/get-file?filename=" .
                                        urlencode($po->po_upload_file), ['target' => "_blank", 'class' => 'pull-right mr-2', 'title' => $po->po_upload_file]);
                    }
                    return $poLink;
                }
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'filterOptions' => ['class' => 'd-none'],
                'contentOptions' => ['class' => 'd-md-none px-3'],
                'headerOptions' => ['class' => 'd-none'],
                'value' => function($model) {

                    $str = "Request ID: "
                            . Html::tag('span', $model->id, ['class' => 'font-weight-bold'])
                            . Html::a(' <i class="fas fa-arrow-alt-circle-right"></i>', ['staff-view-quotation-detail', 'id' => $model->id], ['title' => 'Process', 'class' => 'text-success']) . "<br/>";

                    $str .= "Project Code: " . Html::tag('span', $model->project_code, ['class' => 'font-weight-bold']) . "<br/>";
                    if ($model->file_reference) {
                        $str .= "File Reference: " . Html::a(" <i class='far fa-file-alt fa-lg' ></i>", ['get-file', 'id' => $model->id], ['target' => '_blank']) . "<br/>";
                    }
                    $str .= "Status: " . ($model->getStatus() == QuotationMasters::STS_REQ ? '<i class="fas fa-exclamation-triangle fa-lg text-danger"></i> ' : "") . Html::tag('span', $model->getStatus(), ['class' => 'font-weight-bold']) . "<br/>";

                    if ($model->request_is_complete && $model->purchaseOrderMasters) {
                        $po = $model->purchaseOrderMasters[0];
                        $str .= "P.O.: " . Html::a($po->po_number, "/working/po/view-individual?id=$po->po_id", ['class' => 'font-weight-bold'])
                                . Html::a("<i class='far fa-file-alt pl-2 fa-lg'></i>", "/working/po/get-file?filename=" .
                                        urlencode($po->po_upload_file), ['target' => "_blank", 'class' => 'pull-right mr-2', 'title' => $po->po_upload_file]);
                    }


                    return $str;
                }
            ],
        ],
    ]);
    ?>


</div>
