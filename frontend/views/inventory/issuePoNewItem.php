<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Issued PO';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Purchasing - ' . $label, 'url' => [$url]];
$this->params['breadcrumbs'][] = ['label' => $this->title];
$this->params['breadcrumbs'][] = ['label' => $po->po_no];
?>
<?=

$this->render('_issuePoQuotationForm', [
    'purchaseRequest' => $purchaseRequest,
    'purchaseRequestItems' => $purchaseRequestItems,
    'po' => $po,
    'currencies' => $currencies,
    'currencyList' => $currencyList,
    'companyGroupList' => $companyGroupList,
    'itemList' => $itemList,
    'receivedItemIds' => $receivedItemIds ?? null,
    'page' => $page
])
?>