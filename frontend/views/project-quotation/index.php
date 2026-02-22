<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\projectquotation\QuotationPdfMastersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quotation Pdf Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-pdf-masters-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Quotation Pdf Masters', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'quotation_no',
            'project_q_client_id',
            'revision_id',
            'to_company',
            //'to_pic',
            //'to_tel_no',
            //'to_fax_no',
            //'q_from',
            //'q_your_ref',
            //'q_date',
            //'proj_title',
            //'proj_q_rev_id',
            //'with_sst',
            //'currency_id',
            //'show_breakdown',
            //'show_breakdown_price',
            //'discount_amt',
            //'discount_type',
            //'show_panel_description',
            //'q_material_offered:ntext',
            //'q_switchboard_standard:ntext',
            //'q_quotation',
            //'q_delivery_ship_mode',
            //'q_delivery_destination',
            //'q_delivery',
            //'q_validity',
            //'q_payment',
            //'q_remark:ntext',
            //'filename',
            //'file_type',
            //'file_size',
            //'file_blob',
            //'prepared_by',
            //'approved_by',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'prepared_by_sign',
            //'approved_by_sign',
            //'md_approval_status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
