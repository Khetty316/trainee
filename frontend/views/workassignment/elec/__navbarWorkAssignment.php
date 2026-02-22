<?php

$linkList = array(
    '1' => array(
        'name' => 'Project List',
        'link' => '/elec-task/index-elec-project-list'),
    '2' => array(
        'name' => 'In Progress',
        'link' => '/elec-task/index-elec-in-progress'),
//    '3' => array(
//        'name' => 'Completed',
//        'link' => '/elec-task/index-complete'),
    '3' => array(
        'name' => 'All',
        'link' => '/elec-task/index-elec-all'),
    '4' => array(
        'name' => 'Role Assignment',
        'link' => '/task-configuration/index-elec'),
);
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = "Electrical Task Assignment";
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
