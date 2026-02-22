<?php
// Module configurations
$moduleConfigs = [
    'personal' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/prereq-form-master/personal-pending-approval',
            'module' => 'personal'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/prereq-form-master/personal-all-approval',
            'module' => 'personal'
        ]
    ],
    'superior' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/prereq-form-master/superior-pending-approval',
            'module' => 'superior'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/prereq-form-master/superior-all-approval',
            'module' => 'superior'
        ]
    ],
    'superuser' => [
        '1' => [
            'name' => 'Pending',
            'link' => '/office/prereq-form-master/superuser-pending-approval',
            'module' => 'superuser'
        ],
        '2' => [
            'name' => 'All',
            'link' => '/office/prereq-form-master/superuser-all-approval',
            'module' => 'superuser'
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