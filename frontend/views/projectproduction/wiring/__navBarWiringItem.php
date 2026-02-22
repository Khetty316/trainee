<?php

$linkList = array(
    '1' => array(
        'name' => 'To Be Received',
        'link' => '/production/wiring-dept/index-to-receive-item'
    ),
    '2' => array(
        'name' => 'Dispatched List',
        'link' => '/production/wiring-dept/index-all-dispatched-list'
    ),
);
$this->params['breadcrumbs'][] = "Wiring Department";
if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}

$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


