<?php

$linkList = array(
    '1' => array(
        'name' => 'Production - Ongoing Task',
        'link' => '/production/panel-task-status/my-active-task'),
    '2' => array(
        'name' => 'All Task Records',
             'link' => '/production/panel-task-status/my-all-task'),
    '3' => array(
        'name' => 'Panel Defect Complaints',
             'link' => '/production/panel-task-status/index-defects'),

);

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
