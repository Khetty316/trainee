<?php

$linkList = array(
    '1' => array(
        'name' => 'Pending',
        'link' => '/production/material-bq/index-to-receive-material'
    ),
    '2' => array(
        'name' => 'Dispatched List (All)',
        'link' => '/production/material-bq/index-all-dispatched-list'
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


