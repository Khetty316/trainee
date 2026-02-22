<?php

$linkList = array(
    '1' => array(
        'name' => 'Pending',
        'link' => '/production/material-bq-store/index-to-dispatch'
    ),
    '3' => array(
        'name' => 'Dispatched List',
        'link' => '/production/material-bq-store/index-store-dispatched-list'
    ),
    '2' => array(
        'name' => 'B.Q. List (All)',
        'link' => '/production/material-bq-store/index-all-bq'
    ),
);
//$this->params['breadcrumbs'][] = 'Covid-19 Test-Kit (Admin)';
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}

$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);


