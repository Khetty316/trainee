<?php



switch ($module) {
    case 'po_tracking_list':
        $linkList = array(
            '1' => array(
                'name' => 'Pending by ' . Yii::$app->user->identity->fullname,
                'link' => '/working/po/proc-tracking-list-individual'),
            '2' => array(
                'name' => 'Pending (All)',
                'link' => '/working/po/proc-tracking-list'),
            '3' => array(
                'name' => 'All',
                'link' => '/working/po/proc-tracking-list-all'),
        );
        break;
}
?> 
<?php

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
