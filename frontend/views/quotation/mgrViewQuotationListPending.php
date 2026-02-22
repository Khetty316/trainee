<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\models\quotation\QuotationMasters;
/* @var $this yii\web\View */
/* @var $searchModel frontend\models\quotation\QuotationMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'RFQ Pending List (Manager)';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-masters-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <h3><?= Html::encode($this->title) ?></h3>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'headerRowOptions' => ['class' => 'my-thead'],
        'layout' => "{summary}\n{pager}\n{items}\n{pager}",
        'tableOptions' => ['class' => 'table-hover table table-striped table-bordered table-sm'],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'columns' => [
            [
                'attribute' => '',
                'format' => 'raw',
                'label' => 'Process',
                'headerOptions' => ['class' => 'text-center d-none d-md-table-cell'],
                'contentOptions' => ['class' => 'text-center d-none d-md-table-cell'],
                'value' => function($model) {
                    return Html::a('<i class="fas fa-arrow-alt-circle-right"></i>', ['mgr-view-process-quotation-detail', 'id' => $model->id], ['title' => 'Process', 'class' => 'text-success']);
                }
            ],
            [
                'attribute' => 'id',
                'format' => 'raw',
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'label' => 'Request ID',
            ],
            [
                'attribute' => 'project_code',
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
            ],
            [
                'attribute' => 'file_reference',
                'format' => 'raw',
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
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'value' => function($model) {
                    return nl2br(Html::encode(substr($model->description, 0, 50))) . (strlen($model->description) > 50 ? "..." : "");
                }
            ],
            [
                'attribute' => 'proc_approval',
                'format' => 'raw',
                'label' => 'Status',
                'contentOptions' => ['class' => 'd-none d-md-table-cell'],
                'headerOptions' => ['class' => 'd-none d-md-table-cell'],
                'value' => function($model) {
                    return ($model->getStatus() == QuotationMasters::STS_REQ ? '<i class="fas fa-exclamation-triangle fa-lg text-danger"></i> ' : "") . $model->getStatus();
                }
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'contentOptions' => ['class' => 'd-md-none px-3'],
                'headerOptions' => ['class' => 'd-md-none'],
                'value' => function($model) {
                    $str = "Request ID: "
                            . Html::tag('span', $model->id, ['class' => 'font-weight-bold'])
                            . Html::a(' <i class="fas fa-arrow-alt-circle-right"></i>', ['mgr-view-process-quotation-detail', 'id' => $model->id], ['title' => 'Process', 'class' => 'text-success']) . "<br/>";
                    $str .= "Project Code: " . Html::tag('span', $model->project_code, ['class' => 'font-weight-bold']) . "<br/>";
                    if ($model->file_reference) {
                        $str .= "File Reference: " . Html::a(" <i class='far fa-file-alt fa-lg' ></i>", ['get-file', 'id' => $model->id], ['target' => '_blank']) . "<br/>";
                    }
                    $str .= "Status: " . Html::tag('span', $model->getStatus(), ['class' => 'font-weight-bold']) . "<br/>";
                    return $str;
                }
            ],
        ],
    ]);
    ?>


</div>
