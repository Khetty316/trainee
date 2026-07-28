<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\client\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Clients';
$this->params['breadcrumbs'][] = $this->title;
?>
<link rel="stylesheet" type="text/css" href="/css/responsiveTableIndex.css">
<?php
$this->registerCss("
.table td,
.table th {
    padding: 18px !important;
    font-size: 17px !important;
}
");
?>
<div class="clients-index">
    <?= $this->render('_navbarClient', ['pageKey' => '1']) ?>
    <div class="d-flex justify-content-between mb-3">
        <div>
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Client_Module_Director, AuthItem::ROLE_Client_Module_Projcoor, AuthItem::ROLE_Client_Module_Procurement, AuthItem::ROLE_Client_Module_Finance])) { ?>
                <?= Html::a('Create Clients <i class="fas fa-plus"></i>', ['create-client'], ['class' => 'btn btn-success']) ?>
            <?php } ?>

            <?= Html::a('Reset Filter <i class="fas fa-search-minus"></i>', '?', ['class' => 'btn btn-primary']) ?>
        </div>
        <div>
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Client_Module_Director, AuthItem::ROLE_Client_Module_Projcoor, AuthItem::ROLE_Client_Module_Procurement, AuthItem::ROLE_Client_Module_Finance])) { ?>
                <?=
                Html::a(
                        'User Manual <i class="fas fa-book"></i>',
                        ['user-manual'],
                        ['class' => 'btn btn-warning float-right', 'title' => 'View User Manual', 'target' => '_blank']
                )
                ?>
            <?php } ?>
        </div>
    </div>

    <?=
    GridView::widget([
        'layout' => "
    {summary}
    {pager}
    <div class='table-scroll'>
        {items}
    </div>
    {pager}
",
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => ['class' => yii\bootstrap4\LinkPager::class],
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'tableOptions' => ['class' => 'table table-striped table-bordered table-sm'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => 'width:45px;',
                ],
                'contentOptions' => [
                    'style' => 'width:45px; text-align:center;',
                ],
            ],
            [
                'attribute' => 'client_code',
                'format' => 'raw',
                'headerOptions' => [
                    'style' => 'width:100px;',
                ],
                'contentOptions' => [
                    'style' => 'width:100px; min-width:100px; white-space:nowrap;',
                ],
                'value' => function ($model) {
                    return Html::a(
                            $model->client_code,
                            ['view-client', 'id' => $model->id]
                    );
                }
            ],
            'company_name',
            'company_registration_no',
            'company_tin',
            [
                'attribute' => 'payment_term',
                'headerOptions' => [
                    'style' => 'width:110px;',
                ],
                'contentOptions' => [
                    'style' => 'width:110px;',
                ],
            ],
            [
                'attribute' => 'tk_balance',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'contentOptions' => [
                    'class' => 'text-right',
                    'style' => 'width:140px; min-width:140px;',
                ],
                'value' => function ($model) {
                    return number_format($model->tk_balance, 2);
                }
            ],
            [
                'attribute' => 'tke_balance',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'contentOptions' => [
                    'class' => 'text-right',
                    'style' => 'width:140px; min-width:140px;',
                ],
                'value' => function ($model) {
                    return number_format($model->tke_balance, 2);
                }
            ],
            [
                'attribute' => 'tkm_balance',
                'headerOptions' => [
                    'style' => 'width:140px;',
                ],
                'contentOptions' => [
                    'class' => 'text-right',
                    'style' => 'width:140px; min-width:140px;',
                ],
                'value' => function ($model) {
                    return number_format($model->tkm_balance, 2);
                }
            ],
            [
                'attribute' => 'current_outstanding_balance',
                'headerOptions' => [
                    'style' => 'width:160px;',
                ],
                'contentOptions' => [
                    'class' => 'text-right',
                    'style' => 'width:160px; min-width:160px;',
                ],
                'value' => function ($model) {
                    return number_format($model->current_outstanding_balance, 2);
                }
            ],
            [
                'attribute' => 'contact_person',
                'format' => 'raw',
                'value' => function ($model) {
                    $contacts = $model->clientContacts;
                    if (!$contacts)
                        return '<span class="text-muted">(no name)</span>';

                    $nameList = [];
                    foreach ($contacts as $contact) {
                        if (!empty($contact->name))
                            $nameList[] = htmlspecialchars($contact->name);
                        else
                            $nameList[] = '<span class="text-muted">(no name)</span>';
                    }
                    return implode('<br>', $nameList);
                },
            ],
            [
                'attribute' => 'contact_position',
                'format' => 'raw',
                'value' => function ($model) {
                    $contacts = $model->clientContacts;
                    if (!$contacts)
                        return '<span class="text-muted">(no position)</span>';

                    $positionList = [];
                    foreach ($contacts as $contact) {
                        if (!empty($contact->position))
                            $positionList[] = htmlspecialchars($contact->position);
                        else
                            $positionList[] = '<span class="text-muted">(no position)</span>';
                    }
                    return implode('<br>', $positionList);
                }
            ],
            [
                'attribute' => 'contact_number',
                'format' => 'raw',
                'value' => function ($model) {
                    $contacts = $model->clientContacts;
                    if (!$contacts)
                        return '<span class="text-muted">(no contact number)</span>';

                    $numList = [];
                    foreach ($contacts as $contact) {
                        if (!empty($contact->contact_number))
                            $numList[] = htmlspecialchars($contact->contact_number);
                        else
                            $numList[] = '<span class="text-muted">(no contact number)</span>';
                    }
                    return implode('<br>', $numList);
                }
            ],
            [
                'attribute' => 'contact_email',
                'format' => 'raw',
                'value' => function ($model) {
                    $contacts = $model->clientContacts;
                    if (!$contacts)
                        return '<span class="text-muted">(no email)</span>';

                    $emailList = [];
                    foreach ($contacts as $contact) {
                        if (!empty($contact->email_address))
                            $emailList[] = htmlspecialchars($contact->email_address);
                        else
                            $emailList[] = '<span class="text-muted">(no email)</span>';
                    }
                    return implode('<br>', $emailList);
                },
            ],
            'postcode',
            [
                'attribute' => 'area',
                'value' => function ($model) {
                    return $model['area0']['area_name'] ?? null;
                }
            ],
            [
                'attribute' => 'state',
                'value' => function ($model) {
                    return $model['state0']['state_name'] ?? null;
                }
            ],
            [
                'attribute' => 'country',
                'value' => function ($model) {
                    return $model['country0']['country_name'] ?? null;
                }
            ],
        //'email:email',
        //'address_1',
        //'address_2',
        //'postcode',
        //'area',
        //'state',
        //'country',
        //'created_at',
        //'created_by',
        //'updated_at',
        //'updated_by',
        ],
    ]);
    ?>

    <!--            <div id="debtReminder"
                     class="tab-pane fade">
    
                    <h3>Debt Reminder Letter</h3>
    
                </div>
            </div>-->

