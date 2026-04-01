<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$moduleConfigs = [
    'inventory' => [
        '1' => ['name' => 'Pending', 'link' => '/cmms/cmms-wo-material-request/pending-material-request-master-list'],
        '2' => ['name' => 'All', 'link' => '/cmms/cmms-wo-material-request/all-material-request-master-list?moduleIndex=inventory&type=all'],
        '3' => ['name' => 'Dispatched Master List', 'link' => '/cmms/cmms-stock-dispatch/index'],
    ],
    'superior' => [
//'1' => ['name' => 'Pending', 'link' => '/cmms/cmms-wo-material-request/material-request-master-list'],
//        '2' => ['name' => 'All', 'link' => '/cmms/cmms-wo-material-request/material-request-master-list?moduleIndex=inventory&dispatch=all'],       
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
