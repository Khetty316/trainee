<?php

$linkList = array(
    '1' => array(
        'name' => "Panel's Test List",
        'link' => '/test/testing/index-project?id=' . $project->id),
    '2' => array(
        'name' => 'Test Progress',
        'link' => '/test/testing/index-test-progress?id=' . $project->id),
); 

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>