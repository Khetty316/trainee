<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

// Module configurations
//$moduleConfigs = [
//    'newItem' => [
//        '1' => [
//            'name' => 'Pending Requisition Approval',
//            'link' => '/inventory/inventory/executive-pre-requisition-pending-approval'
//        ],
//        '2' => [
//            'name' => 'All Pre-Requisitions',
//            'link' => '/inventory/inventory/executive-pre-requisition-all-application'
//        ],
//        '3' => [
//            'name' => 'Ready for Purchase Order',
//            'link' => '/inventory/inventory/executive-new-item-ready-for-po-list'
//        ],
//        '4' => [
//            'name' => 'All Purchase Orders',
//            'link' => '/inventory/inventory/executive-new-item-po-list'
//        ],
//    ],
//
//    'reorderItem' => [
//        '1' => [
//            'name' => 'Items to Reorder',
//            'link' => '/inventory/inventory/items-to-reorder'
//        ],
//        '2' => [
//            'name' => 'Ready for Purchase Order',
//            'link' => '/inventory/inventory/executive-reorder-item-ready-for-po-list'
//        ],
//        '3' => [
//            'name' => 'All Purchase Orders',
//            'link' => '/inventory/inventory/executive-reorder-item-po-list'
//        ],
//    ],
//];

$moduleConfigs = [
    'execPurchasing' => [
        '1' => [
            'name' => 'Pending Requisition Approval',
            'link' => '/inventory/inventory/executive-pre-requisition-pending-approval'
        ],
        '2' => [
            'name' => 'All Pre-Requisitions',
            'link' => '/inventory/inventory/executive-pre-requisition-all-application'
        ],
        '3' => [
            'name' => 'Pending Order Request',
            'link' => '/inventory/inventory/pending-order-request-list?type=execPending'
        ],
        '4' => [
            'name' => 'All Order Request',
            'link' => '/inventory/inventory/all-order-request-list?type=execAll'
        ],
        '5' => [
            'name' => 'Pending Purchase Orders',
            'link' => '/inventory/inventory/po?type=execPendingPurchasing'
        ],
        '6' => [
            'name' => 'All Purchase Orders',
            'link' => '/inventory/inventory/po?type=execAllPurchasing'
        ],
    ],
    'execReceiving' => [
        '1' => [
            'name' => 'Pending Purchase Orders',
            'link' => '/inventory/inventory/po?type=execPendingReceiving'
        ],
        '2' => [
            'name' => 'All Purchase Orders',
            'link' => '/inventory/inventory/po?type=execAllReceiving'
        ],
        '3' => [
            'name' => 'History',
            'link' => '/inventory/inventory/receiving-history'
        ],
    ],
    'projcoor' => [
        '1' => [
            'name' => 'Pending Requisition Approval',
            'link' => '/inventory/inventory/projcoor-pre-requisition-pending-approval'
        ],
        '2' => [
            'name' => 'Ready for Procurement',
            'link' => '/inventory/inventory/projcoor-ready-for-procurement'
        ],
        '3' => [
            'name' => 'All Pre-Requisitions',
            'link' => '/inventory/inventory/projcoor-pre-requisition-all-application'
        ],
        '4' => [
            'name' => 'Your Order Request',
            'link' => '/inventory/inventory/all-order-request-list?type=projcoor'
        ],
    ],
];

$linkList = $moduleConfigs[$module] ?? [];

$this->title = $linkList[$pageKey]['name'] ?? 'Inventory Control';

$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile(
        Yii::getAlias('@app') . '/views/__commonNavBar.php',
        [
            'title' => $this->title,
            'linkList' => $linkList,
            'module' => $module
        ]
);
?>
