<?php

// Module configurations
$moduleConfigs = [
    'personal' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/petty-cash/personal-pending',
            'module' => 'personal'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/petty-cash/personal-all',
            'module' => 'personal'
        ],
    ],
    'director' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/petty-cash/director-approval-pending',
            'module' => 'director'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/petty-cash/director-approval-all',
            'module' => 'director'
        ]
    ],
    'finance' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/petty-cash/finance-approval-pending',
            'module' => 'finance'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/petty-cash/finance-approval-all',
            'module' => 'finance'
        ],
        '3' => [
            'name' => 'Replenishment',
            'link' => '/office/petty-cash/finance-replenishment',
            'module' => 'finance'
        ],
        '4' => [
            'name' => 'Ledger',
            'link' => '/office/petty-cash/finance-ledger',
            'module' => 'finance'
        ]
    ]
];

$module = $module ?? 'personal';
$linkList = $moduleConfigs[$module] ?? reset($moduleConfigs);

if (isset($pageKey) && isset($linkList[$pageKey])) {
    $this->title = $linkList[$pageKey]['name'];
} else {
    $this->title = 'Page';
}

$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>
