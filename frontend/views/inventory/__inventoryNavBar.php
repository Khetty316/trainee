<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$moduleConfigs = [
    'execStock' => [
        '1' => ['name' => 'Item List', 'link' => '/inventory/inventory/item-list?type=execStock'],
        '2' => ['name' => 'Supplier', 'link' => '/inventory/inventory/supplier-list?type=execStock'],
        '3' => ['name' => 'Brand', 'link' => '/inventory/inventory/brand-list?type=execStock'],
        '4' => ['name' => 'Model', 'link' => '/inventory/inventory/model-list?type=execStock'],
        '5' => ['name' => 'Reserved Items', 'link' => '/inventory/inventory/reserved-item-list?type=execStock'],
    ],
    'assistStock' => [
        '1' => ['name' => 'Item List', 'link' => '/inventory/inventory/item-list?type=assistStock'],
        '2' => ['name' => 'Supplier', 'link' => '/inventory/inventory/supplier-list?type=assistStock'],
        '3' => ['name' => 'Brand', 'link' => '/inventory/inventory/brand-list?type=assistStock'],
        '4' => ['name' => 'Model', 'link' => '/inventory/inventory/model-list?type=assistStock'],
        '5' => ['name' => 'Reserved Items', 'link' => '/inventory/inventory/reserved-item-list?type=assistStock'],
    ],
    'projcoorStock' => [
        '1' => ['name' => 'Item List', 'link' => '/inventory/inventory/item-list?type=projcoorStock'],
        '2' => ['name' => 'Supplier', 'link' => '/inventory/inventory/supplier-list?type=projcoorStock'],
        '3' => ['name' => 'Brand', 'link' => '/inventory/inventory/brand-list?type=projcoorStock'],
        '4' => ['name' => 'Model', 'link' => '/inventory/inventory/model-list?type=projcoorStock'],
        '5' => ['name' => 'Reserved Items', 'link' => '/inventory/inventory/reserved-item-list?type=projcoorStock'],
    ],
    'maintenanceHeadStock' => [
        '1' => ['name' => 'Item List', 'link' => '/inventory/inventory/item-list?type=maintenanceHeadStock'],
        '2' => ['name' => 'Supplier', 'link' => '/inventory/inventory/supplier-list?type=maintenanceHeadStock'],
        '3' => ['name' => 'Brand', 'link' => '/inventory/inventory/brand-list?type=maintenanceHeadStock'],
        '4' => ['name' => 'Model', 'link' => '/inventory/inventory/model-list?type=maintenanceHeadStock'],
        '5' => ['name' => 'Reserved Items', 'link' => '/inventory/inventory/reserved-item-list?type=maintenanceHeadStock'],
    ],
    'execPurchasing' => [
        '1' => [
            'name' => 'Pending Requisition Approval',
            'link' => '/inventory/inventory/pre-requisition-list?type=execPendingPurchasing&context=pendingInventory'
        ],
        '2' => [
            'name' => 'All Pre-Requisitions',
            'link' => '/inventory/inventory/pre-requisition-list?type=execAllPurchasing&context=allInventory'
        ],
        '3' => [
            'name' => 'Pending Order Request',
            'link' => '/inventory/inventory/order-request-list?type=execPending'
        ],
        '4' => [
            'name' => 'All Order Request',
            'link' => '/inventory/inventory/order-request-list?type=execAll'
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
            'link' => '/inventory/inventory/receiving-history?type=exec'
        ],
    ],
    'assistPurchasing' => [
        '1' => [
            'name' => 'Pending Requisition Approval',
            'link' => '/inventory/inventory/pre-requisition-list?type=assistPendingPurchasing&context=pendingInventory'
        ],
        '2' => [
            'name' => 'All Pre-Requisitions',
            'link' => '/inventory/inventory/pre-requisition-list?type=assistAllPurchasing&context=allInventory'
        ],
        '3' => [
            'name' => 'Pending Order Request',
            'link' => '/inventory/inventory/order-request-list?type=assistPending'
        ],
        '4' => [
            'name' => 'All Order Request',
            'link' => '/inventory/inventory/order-request-list?type=assistAll'
        ],
        '5' => [
            'name' => 'Pending Purchase Orders',
            'link' => '/inventory/inventory/po?type=assistPendingPurchasing'
        ],
        '6' => [
            'name' => 'All Purchase Orders',
            'link' => '/inventory/inventory/po?type=assistAllPurchasing'
        ],
    ],
    'assistReceiving' => [
        '1' => [
            'name' => 'Pending Purchase Orders',
            'link' => '/inventory/inventory/po?type=assistPendingReceiving'
        ],
        '2' => [
            'name' => 'All Purchase Orders',
            'link' => '/inventory/inventory/po?type=assistAllReceiving'
        ],
        '3' => [
            'name' => 'History',
            'link' => '/inventory/inventory/receiving-history?type=assist'
        ],
    ],
    'projcoor' => [
        '1' => [
            'name' => 'Pending Requisition Approval',
            'link' => '/inventory/inventory/pre-requisition-list?type=projcoorPendingApproval&context=pendingApprovalInventoryProjcoor'
        ],
        '2' => [
            'name' => 'Ready for Procurement',
            'link' => '/inventory/inventory/pre-requisition-list?type=projcoorReadyForProcurement&context=pendingProcurementInventoryProjcoor'
        ],
        '3' => [
            'name' => 'All Pre-Requisitions',
            'link' => '/inventory/inventory/pre-requisition-list?type=projcoorAllApproval&context=allInventoryProjcoor'
        ],
        '4' => [
            'name' => 'Your Order Request',
            'link' => '/inventory/inventory/order-request-list?type=projcoor'
        ],
    ],
    'maintenanceHeadPurchasing' => [
        '1' => [
            'name' => 'Pending Requisition Approval',
            'link' => '/inventory/inventory/pre-requisition-list?type=maintenanceHeadPendingApproval&context=pendingApprovalInventoryMaintenanceHead'
        ],
        '2' => [
            'name' => 'Ready for Procurement',
            'link' => '/inventory/inventory/pre-requisition-list?type=maintenanceHeadReadyForProcurement&context=pendingProcurementInventoryMaintenanceHead'
        ],
        '3' => [
            'name' => 'All Pre-Requisitions',
            'link' => '/inventory/inventory/pre-requisition-list?type=maintenanceHeadAllApproval&context=allInventoryMaintenanceHead'
        ],
//        '4' => [
//            'name' => 'Your Order Request',
//            'link' => '/inventory/inventory/order-request-list?type=maintenanceHead'
//        ],
        '4' => [
            'name' => 'Pending Order Request',
            'link' => '/inventory/inventory/order-request-list?type=maintenanceHeadPending'
        ],
        '5' => [
            'name' => 'All Order Request',
            'link' => '/inventory/inventory/order-request-list?type=maintenanceHeadAll'
        ],
        '6' => [
            'name' => 'Pending Purchase Orders',
            'link' => '/inventory/inventory/po?type=maintenanceHeadPendingPurchasing'
        ],
        '7' => [
            'name' => 'All Purchase Orders',
            'link' => '/inventory/inventory/po?type=maintenanceHeadAllPurchasing'
        ],
    ],
    'maintenanceHeadReceiving' => [
        '1' => [
            'name' => 'Pending Purchase Orders',
            'link' => '/inventory/inventory/po?type=maintenanceHeadPendingReceiving'
        ],
        '2' => [
            'name' => 'All Purchase Orders',
            'link' => '/inventory/inventory/po?type=maintenanceHeadAllReceiving'
        ],
        '3' => [
            'name' => 'History',
            'link' => '/inventory/inventory/receiving-history?type=maintenanceHead'
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
