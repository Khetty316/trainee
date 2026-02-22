<?php
// Module configurations
$moduleConfigs = [
        '1' => [
            'name' => 'Pending',
            'link' => '/projectquotation/director-pending-approval',
        ],
        '2' => [
            'name' => 'All',
            'link' => '/projectquotation/director-all-approval',
        ]
];

$linkList = $moduleConfigs;

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile(
    Yii::getAlias('@app') . '/views/__commonNavBar.php', 
    [
        'title' => $this->title, 
        'linkList' => $linkList,
    ]
);
?>