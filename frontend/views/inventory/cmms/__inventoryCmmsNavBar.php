<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

// Module configurations
$moduleConfigs = [
    'superior' => [
        '1' => ['name' => 'Item List', 'link' => '/inventory/inventory-cmms/item-list', 'module' => 'superior'],
        '2' => ['name' => 'Supplier', 'link' => '/inventory/inventory-cmms/supplier-list', 'module' => 'superior'],
        '3' => ['name' => 'Brand',    'link' => '/inventory/inventory-cmms/brand-list',   'module' => 'superior'],
        '4' => ['name' => 'Model',    'link' => '/inventory/inventory-cmms/model-list',   'module' => 'superior'],
    ]
];

$module = isset($module) ? $module : 'superior';
$linkList = $moduleConfigs[$module];

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
