<?php

$linkList = array(
    '1' => array(
        'name' => 'Project List',
        'link' => '/test/testing/index-project-lists'),
    '2' => array(
        'name' => 'Test List',
        'link' => '/test/testing/index-test-lists'),
); 

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = 'Test ' . $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>