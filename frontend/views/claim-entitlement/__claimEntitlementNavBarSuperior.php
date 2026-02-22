<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$linkList = array(
    '1' => array(
        'name' => 'Pending',
        'link' => '/office/claim-entitlement/superior-pending-approval'),
    '2' => array(
        'name' => 'All',
        'link' => '/office/claim-entitlement/superior-all-approval'),
);
$this->params['breadcrumbs'][] = ['label' => 'Claim Entitlements - Superior'];

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>

