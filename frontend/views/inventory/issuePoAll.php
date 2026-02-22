<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\office\preReqForm\PrereqFormMaster */

$this->title = 'Issued PO';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Control'];
$this->params['breadcrumbs'][] = ['label' => 'Receiving', 'url' => ['executive-pending-receive-purchase-order']];
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