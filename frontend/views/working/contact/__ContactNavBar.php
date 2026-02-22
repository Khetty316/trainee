<?php

switch ($module) {
    case 'contacts':
        $linkList = array(
            '1' => array(
                'name' => 'Client',
                'link' => '/working/contact/index?type=client'),
            '2' => array(
                'name' => 'Vendor',
                'link' => '/working/contact/index?type=vendor'),
        );
        break;

 
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
