<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$linkList = array(
    '1' => array(
        'name' => 'Pending',
        'link' => '/office/claim-entitlement/pending-approval'),
    '2' => array(
        'name' => 'All',
        'link' => '/office/claim-entitlement/hr-all-approval'),
    '3' => array(
        'name' => 'Claim Summary',
        'link' => '/office/claim-entitlement/hr-claim-summary')
);

$this->params['breadcrumbs'][] = ['label' => 'Claim Entitlements - HR'];

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}
$this->params['breadcrumbs'][] = $this->title;
echo $this->renderFile(Yii::getAlias('@app') . '/views/__commonNavBar.php', ['title' => $this->title, 'linkList' => $linkList]);
?>

