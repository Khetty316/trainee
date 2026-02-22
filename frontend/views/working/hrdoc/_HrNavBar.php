<?php

switch ($module) {
    case 'hrdoc':
        $linkList = array(
            '1' => array(
                'name' => 'Personal Documents',
                'link' => '/working/hr-employee-document/index'),
            '2' => array(
                'name' => 'Public Documents',
                'link' => '/working/hr-public-document/index'),
        );
        break;

 
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
