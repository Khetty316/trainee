<?php
// Module configurations
$moduleConfigs = [
    'personal' => [
        '1' => [
            'name' => 'Active',
            'link' => '/cmms/cmms-fault-list/personal-active',
            'module' => 'personal'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/cmms/cmms-fault-list/personal-all',
            'module' => 'personal'
        ]
//        '3' => [
//            'name' => 'Report Fault',
//            'link' => '/cmms/cmms-fault-list/personal-report-fault',
//            'module' => 'personal'
//        ]
    ],
    'superior' => [
        '1' => [
            'name' => 'Active',
            'link' => '/cmms/cmms-fault-list/superior-active',
            'module' => 'superior'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/cmms/cmms-fault-list/superior-all',
            'module' => 'superior'
        ]
    ],
];

// Get module from parameters
$module = isset($module) ? $module : 'personal';
$linkList = $moduleConfigs[$module] ?? $moduleConfigs['personal'];

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
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