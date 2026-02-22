<?php

$linkList = array(
    '1' => array(
        'name' => 'Pending Orders',
        'link' => '/production/procurement/index-pending-order-list'
    ),
    '2' => array(
        'name' => 'Dispatched List',
        'link' => '/production/procurement/index-proc-dispatched-list'
    ),
);
$this->params['breadcrumbs'][] = "Procurement";
if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}

$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


