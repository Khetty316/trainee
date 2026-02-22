<?php

$linkList = array(
    '1' => array(
        'name' => 'Project List',
        'link' => '/fab-task/index-fab-project-list'),
    '2' => array(
        'name' => 'In Progress',
        'link' => '/fab-task/index-fab-in-progress'),
//    '3' => array(
//        'name' => 'Completed',
//        'link' => '/fab-task/index-complete'),
    '3' => array(
        'name' => 'All',
        'link' => '/fab-task/index-fab-all'),
    '4' => array(
        'name' => 'Role Assignment',
        'link' => '/task-configuration/index-fab'),
);
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = "Fabrication Task Assignment";
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
