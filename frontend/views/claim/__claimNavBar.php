<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$prodotmeal = MyCommonFunction::checkRoles([AuthItem::ROLE_PROD_OT_MEAL_EXEC]);
$prodotmealFinance = MyCommonFunction::checkRoles([AuthItem::ROLE_PROD_OT_MEAL_FINANCE]);
// Module configurations
$moduleConfigs = [
    'personal' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/claim/personal-claim-pending',
            'module' => 'personal'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/claim/personal-claim-all',
            'module' => 'personal'
        ],
    ],
    'superior' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/claim/superior-approval-pending',
            'module' => 'superior'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/claim/superior-approval-all',
            'module' => 'superior'
        ]
    ],
    'director' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/claim/director-approval-pending',
            'module' => 'director'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/claim/director-approval-all',
            'module' => 'director'
        ]
    ],
    'finance' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/claim/finance-approval-pending',
            'module' => 'finance'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/claim/finance-approval-all',
            'module' => 'finance'
        ],
    ]
];

// Conditionally add Production OT Meal Record tab
if ($prodotmeal) {
    $moduleConfigs['personal']['3'] = [
        'name' => 'Production Overtime Meal Record',
        'link' => '/office/prod-ot-meal-record-master/index',
        'module' => 'personal'
    ];
}

$moduleConfigs['personal']['4'] = [
    'name' => 'Claim Summary',
    'link' => '/office/claim/personal-claim-summary',
    'module' => 'personal'
];

if ($prodotmealFinance) {
    $moduleConfigs['finance']['3'] = [
        'name' => 'Production Overtime Meal Record',
        'link' => '/office/prod-ot-meal-record-master/index-finance',
        'module' => 'finance'
    ];
}

$moduleConfigs['finance']['4'] = [
    'name' => 'Claim Summary',
    'link' => '/office/claim/finance-claim-summary',
    'module' => 'finance'
];

// Get module from parameters
$module = isset($module) ? $module : 'personal';
$linkList = $moduleConfigs[$module] ?? $moduleConfigs['personal'];

// Safely set page title
if (isset($pageKey) && isset($linkList[$pageKey])) {
    $this->title = $linkList[$pageKey]['name'];
} else {
    $this->title = 'Claim';
}

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
