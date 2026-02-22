<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

// Module configurations
$moduleConfigs = [
    'exec' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/inventory/inventory/executive-pending-receive-purchase-order'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/inventory/inventory/executive-all-purchase-order'
        ],
        '3' => [
            'name' => 'History',
            'link' => '/inventory/inventory/executive-receiving-history'
        ],
    ],

    'assist' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/inventory/inventory/executive-pending-receive-purchase-order'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/inventory/inventory/executive-all-purchase-order'
        ],
        '3' => [
            'name' => 'Receiving History',
            'link' => '/inventory/inventory/receiving-history'
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
