<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\projectquotation\QuotationPdfMastersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quotation Approval';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quotation-pdf-masters-index">

    <?= $this->render('__quotationPdfNavBar', ['pageKey' => '1']) ?>

    <?= $this->render('_directorQuotationPdfList', [
        'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,
    ]);
    ?>

</div>
