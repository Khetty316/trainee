<?php

$linkList = array(
    '2' => array(
        'name' => 'Issue B.Q.',
        'link' => '/production/material-bq/index-material-bq-by-projects'
    ),
    '1' => array(
        'name' => 'B.Q. List (All)',
        'link' => '/production/material-bq/index-material-bq'
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


