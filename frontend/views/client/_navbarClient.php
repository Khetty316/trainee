<?php

use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

$linkList = [
    '1' => [
        'name' => 'Clients',
        'link' => '/client/index'
    ],
];

//var_dump(MyCommonFunction::checkRoles([
//            AuthItem::ROLE_Client_Module_Projcoor
//        ]));
//die;
//echo '<pre>';
//var_dump(Yii::$app->user->can(AuthItem::ROLE_Client_Module_Director));
//var_dump(Yii::$app->user->can(AuthItem::ROLE_Client_Module_Procurement));
//var_dump(Yii::$app->user->can(AuthItem::ROLE_Client_Module_Projcoor));
//var_dump(Yii::$app->user->can(AuthItem::ROLE_Client_Module_Finance));
//die;

if (MyCommonFunction::checkRoles([
            AuthItem::ROLE_Client_Module_Director,
            AuthItem::ROLE_Client_Module_Finance,
        ])) {
    $linkList['2'] = [
        'name' => 'Debt Summary',
        'link' => '/client/index-general-client-debt'
    ];

    $linkList['3'] = [
        'name' => 'Debt Reminder Email Log',
        'link' => '/client/index-general-debt-reminder-letter-email-log'
    ];

    $linkList['4'] = [
        'name' => 'Debt Reminder Letter Templates',
        'link' => '/client/index-debt-reminder-letter-template'
    ];
}

if (isset($pageKey)) {
    $this->title = $linkList[$pageKey]['name'];
}

$this->params['breadcrumbs'][] = $this->title;

echo $this->renderFile(
        Yii::getAlias('@app') . '/views/__commonNavBar.php',
        [
            'title' => $this->title,
            'linkList' => $linkList
        ]
);
