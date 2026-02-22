<?php
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

//$linkList = array(
//    '1' => array(
//        'name' => 'Approval',
//        'link' => '/working/leavemgmt/hr-leave-approval'),
//    '2' => array(
//        'name' => 'Pending',
//        'link' => '/working/leavemgmt/hr-leave-pending'),
//    '3' => array(
//        'name' => 'Leave History',
//        'link' => '/working/leavemgmt/hr-leave-to-record'),
//    '4' => array(
//        'name' => 'Monthly Summary',
//        'link' => '/working/leavemgmt/hr-leave-summary'),
//    '5' => array(
//        'name' => 'Annual Summary',
//        'link' => '/working/leavemgmt/hr-final-leave-summary'),
//    '7' => array(
//        'name' => 'Leave Entitlement',
//        'link' => '/working/leavemgmt/hr-leave-entitlement'),
//    '8' => array(
//        'name' => 'Holiday List',
//        'link' => '/working/leavemgmt/hr-holiday-list'),
//    '9' => array(
//        'name' => 'Compulsory Leave',
//        'link' => '/working/leavemgmt/hr-compulsory-leave'),
//    '6' => array(
//        'name' => 'All',
//        'link' => '/working/leavemgmt/hr-all-leave'),
//);
if (MyCommonFunction::checkRoles([AuthItem::ROLE_HR_Senior])) {
    $linkList = array(
        '1' => array(
            'name' => 'Approval',
            'link' => '/working/leavemgmt/hr-leave-approval'),
        '2' => array(
            'name' => 'Pending',
            'link' => '/working/leavemgmt/hr-leave-pending'),
        '3' => array(
            'name' => 'Leave History',
            'link' => '/working/leavemgmt/hr-leave-to-record'),
        '4' => array(
            'name' => 'Monthly Summary',
            'link' => '/working/leavemgmt/hr-leave-summary'),
        '5' => array(
            'name' => 'Annual Summary',
            'link' => '/working/leavemgmt/hr-final-leave-summary'),
        '7' => array(
            'name' => 'Leave Entitlement',
            'link' => '/working/leavemgmt/hr-leave-entitlement'),
        '8' => array(
            'name' => 'Holiday List',
            'link' => '/working/leavemgmt/hr-holiday-list'),
        '9' => array(
            'name' => 'Compulsory Leave',
            'link' => '/working/leavemgmt/hr-compulsory-leave'),
        '6' => array(
            'name' => 'All',
            'link' => '/working/leavemgmt/hr-all-leave'),
    );
} else {
    $linkList = array(
        '6' => array(
            'name' => 'All',
            'link' => '/working/leavemgmt/hr-all-leave'),
    );
}

$this->params['breadcrumbs'][] = ['label' => 'HR - Leave Management'];

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>

