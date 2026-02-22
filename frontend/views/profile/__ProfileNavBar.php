<?php

$linkList = array(
    '1' => array(
        'name' => 'User Profile',
        'link' => '/profile/view-profile'
    ),
    '2' => array(
        'name' => 'Leave Application',
        'link' => '/office/leave/personal-leave'
    ),
    '3' => array(
        'name' => 'Personal Documents',
        'link' => '/profile/view-user-hr-documents'
    ),
    '4' => array(
        'name' => 'Public Documents',
        'link' => '/profile/view-user-public-documents'
    ),
    '5' => array(
        'name' => 'Work Traveling Requisition',
        'link' => '/office/leave/work-travel-req'
    ),
);

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
