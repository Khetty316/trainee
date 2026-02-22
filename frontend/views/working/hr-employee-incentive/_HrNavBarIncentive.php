<?php

$linkList = array(
    '1' => array(
        'name' => 'Factory Staff',
        'link' => '/working/hr-employee-incentive/factory-staff-performance-detail'),
//    '2' => array(
//        'name' => '',
//        'link' => '/working/hr-employee-incentive/factory-staff-performance-detail'),
);
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
