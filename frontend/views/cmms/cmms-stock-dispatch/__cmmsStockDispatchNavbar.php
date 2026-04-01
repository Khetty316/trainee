<?php

$linkList = array(
    '1' => array(
        'name' => 'Pending',
        'link' => '/cmms/cmms-stock-dispatch/my-pending-acknowledgements'),
    '2' => array(
        'name' => 'Acknowledged',
        'link' => '/cmms/cmms-stock-dispatch/my-acknowledgement-list'),
);
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = "My Acknowledgement List";
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);