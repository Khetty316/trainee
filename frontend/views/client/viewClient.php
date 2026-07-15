<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\myTools\MyFormatter;
use common\models\myTools\MyCommonFunction;
use common\modules\auth\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\Clients */
//
$this->title = $model->company_name;
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="clients-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <div>
            <?= Html::a('Update <i class="far fa-edit"></i>', ['update-client', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a('Delete <i class="fas fa-trash"></i>', ['delete-client', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this client?',
                    'method' => 'post',
                ],
            ])
            ?>
        </div>

        <div>
            <?php if (MyCommonFunction::checkRoles([AuthItem::ROLE_Client_Module_Director, AuthItem::ROLE_Client_Module_Finance])) { ?>
                <?=
                Html::a('Send Debt Reminder Letter', ['create-reminder-letter-emails', 'client_id' => $model->id], ['class' => 'btn btn-success'])
                ?>
            <?php } ?>
        </div>
    </div>

    <?=
    DetailView::widget([
        'model' => $model,
        'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
        'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
        'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
        'attributes' => [
//            'id',
            'client_code',
            'company_name',
            'company_registration_no',
            'company_tin',
            'payment_term',
            [
                'attribute' => 'ac_no_tk',
                'label' => 'A/C No. TK',
                'value' => function ($model) {
                    return $model->ac_no_tk;
                },
            ],
            [
                'attribute' => 'ac_no_tke',
                'label' => 'A/C No. TKE',
                'value' => function ($model) {
                    return $model->ac_no_tke;
                }
            ],
            [
                'attribute' => 'ac_no_tkm',
                'label' => 'A/C No. TKM',
                'value' => function ($model) {
                    return $model->ac_no_tkm;
                }
            ],
            [
                'attribute' => 'tk_balance',
                'value' => function ($model) {
                    return number_format($model->tk_balance, 2);
                }
            ],
            [
                'attribute' => 'tke_balance',
                'value' => function ($model) {
                    return number_format($model->tke_balance, 2);
                }
            ],
            [
                'attribute' => 'tkm_balance',
                'value' => function ($model) {
                    return number_format($model->tkm_balance, 2);
                }
            ],
            [
                'attribute' => 'current_outstanding_balance',
                'value' => function ($model) {
                    return number_format($model->current_outstanding_balance, 2);
                }
            ],
            'address_1',
            'address_2',
            'postcode',
            [
                'attribute' => 'area',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model['area0']['area_name'] ?? null;
                }
            ],
            [
                'attribute' => 'state',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model['state0']['state_name'] ?? null;
                }
            ],
            [
                'attribute' => 'country',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model['country0']['country_name'] ?? null;
                }
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = User::findOne($model->created_by);
                    if ($user) {
                        return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->created_at);
                    }
                }
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    $user = User::findOne($model->updated_by);
                    if ($user) {
                        return $user->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($model->updated_at);
                    }
                }
            ],
        ],
    ])
    ?>
    <div class="tab-buttons mb-3">
        <button class="tab-btn active" data-tab="contact"
                onclick="showTab('contact')">
            Contact Person
        </button>

        <button class="tab-btn" data-tab="debt"
                onclick="showTab('debt')">
            Client Debt
        </button>

        <?php
        if (MyCommonFunction::checkRoles([
                    AuthItem::ROLE_Client_Module_Director,
                    AuthItem::ROLE_Client_Module_Finance,
                ])) {
            ?>
            <button class="tab-btn" data-tab="emailLog"
                    onclick="showTab('emailLog')">
                Debt Reminder Letter Email Log
            </button>
        <?php } ?>
    </div>

    <!-- client contacts -->
    <div id="contact" class="tab-content">
        <h3 style="margin-top: 0; margin-bottom: 15px;">
            Contact Person:
        </h3>

        <table class="table table-sm mt-2">

            <thead class="table-dark">
                <tr>
                    <th class="text-center">Name</th>
                    <th class="text-center">Position</th>
                    <th class="text-center">Contact number</th>
                    <th class="text-center">Fax</th>
                    <th class="text-center">Email address</th>
                </tr>
            </thead>
            <tbody id="listTBody">   
                <?php foreach ($contacts as $i => $contact) : ?>
                    <?php $key = $contact->id ?? $index; ?>
                    <tr data-index="<?= $key ?>">
                        <td class="text-center"><?= $contact->name ?></td>
                        <td class="text-center"><?= $contact->position ?></td>
                        <td class="text-center"><?= $contact->contact_number ?></td>
                        <td class="text-center"><?= $contact->fax ?></td>
                        <td class="text-center"><?= $contact->email_address ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

    <!-- client debt -->
    <div id="debt" class="tab-content" style="display:none;">
        <?=
        $this->render('indexClientDebt', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ])
        ?>
    </div>

    <!-- Debt Reminder Letter Email Log -->
    <div id="emailLog" class="tab-content" style="display:none;">
        <div class="email-log-wrapper">
            <?=
            $this->render('debtReminderLetterEmailLog', [
                'emailLogDataProvider' => $emailLogDataProvider,
                'emailLogSearchModel' => $emailLogSearchModel,
            ])
            ?>
        </div>
    </div>

<!--    <?
DetailView::widget([
'model' => $model,
'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ' - '],
'template' => "<tr><th style='width: 30%;'>{label}</th><td>{value}</td></tr>",
'options' => ['class' => 'table table-striped table-bordered detail-view table-sm'],
'attributes' => [
//                    'contact_person',
    [
        'attribute' => 'contact_person',
        'format' => 'raw',
        'value' => function ($model) {
            $contacts = $model->clientContacts;
            if (!$contacts) return '<span class="text-muted">(no name)</span>';
            
            $nameList = [];
            foreach ($contacts as $contact) {
                if (!empty($contact->name))  $nameList[] = htmlspecialchars($contact->name);
                else    $nameList[] = '<span class="text-muted">(no name)</span>';
            }
            return implode('<br>', $nameList);
        },
    ],
//            'contact_position',
    [
        'attribute' => 'contact_position',
        'format' => 'raw',
        'value' => function ($model) {
            $contacts = $model->clientContacts;
            if (!$contacts) return '<span class="text-muted">(no position)</span>';
            
            $positionList = [];
            foreach ($contacts as $contact) {
                if (!empty($contact->position))  $positionList[] = htmlspecialchars($contact->position);
                else    $positionList[] = '<span class="text-muted">(no position)</span>';
            }
            return implode('<br>', $positionList);
        }
    ],
//            'contact_number',
    [
        'attribute' => 'contact_number',
        'format' => 'raw',
        'value' => function ($model) {
            $contacts = $model->clientContacts;
            if (!$contacts) return '<span class="text-muted">(no contact number)</span>';
            
            $numList = [];
            foreach ($contacts as $contact) {
                if (!empty($contact->contact_number))  $numList[] = htmlspecialchars($contact->contact_number);
                else    $numList[] = '<span class="text-muted">(no contact number)</span>';
            }
            return implode('<br>', $numList);
        }
    ],
//            'contact_email',
    [
        'attribute' => 'contact_email',
        'format' => 'raw',
        'value' => function ($model) {
            $contacts = $model->clientContacts;
            if (!$contacts) return '<span class="text-muted">(no email)</span>';
            
            $emailList = [];
            foreach ($contacts as $contact) {
                if (!empty($contact->email_address))  $emailList[] = htmlspecialchars($contact->email_address);
                else    $emailList[] = '<span class="text-muted">(no email)</span>';
            }
            return implode('<br>', $emailList);
        
//                $emails = $model->getEmails($model->id);
//                foreach ($emails as $email) {
//                    if (!empty($email)) $emails = htmlspecialchars($email);
//                    else    $emails = '<span class="text-muted">(no email)</span>';
//                }
//                return implode('<br>', $emails);
//                if (!$emails) 
//                    $emails = array_map(fn($c) => htmlspecialchars($c->email_address), $model->clientContacts);
//                    return $emails ? implode('<br>', $emails) : '<span class="text-muted">(no email)</span>';   
        },
    ],
    [
        'attribute' => 'contact_fax',
        'format' => 'raw',
        'value' => function ($model) {
            $contacts = $model->clientContacts;
            if (!$contacts) return '<span class="text-muted">(no fax)</span>';
            
            $faxList = [];
            foreach ($contacts as $contact) {
                if (!empty($contact->fax))  $faxList[] = htmlspecialchars($contact->fax);
                else    $faxList[] = '<span class="text-muted">(no fax)</span>';
            }
            return implode('<br>', $faxList);
        },
    ],
],
])
?>-->
</div>

<script>

    function showTab(tabId) {

        history.replaceState(null, null, '#' + tabId);

        document.querySelectorAll('.tab-content').forEach(function (tab) {
            tab.style.display = 'none';
        });

        document.getElementById(tabId).style.display = 'block';

        document.querySelectorAll('.tab-btn').forEach(function (btn) {
            btn.classList.remove('active');
        });

        document.querySelector('[data-tab="' + tabId + '"]').classList.add('active');
    }



</script>

<script>

    function activateCurrentTab() {

        let hash = window.location.hash.replace('#', '');

        if (hash) {

            showTab(hash);

        } else {

            const urlParams = new URLSearchParams(window.location.search);

            if (
                    urlParams.has('sort') ||
                    window.location.search.includes('ClientDebtSearch') ||
                    window.location.search.includes('dp-1-page')
                    ) {

                showTab('debt');

                setTimeout(function () {

                    document.getElementById('debt').scrollIntoView({
                        behavior: 'auto',
                        block: 'start'
                    });

                }, 100);

            } else if (
                    window.location.search.includes('ClientReminderLetterEmailsSearch') ||
                    window.location.search.includes('dp-2-page')
                    ) {

                showTab('emailLog');

                setTimeout(function () {

                    document.getElementById('emailLog').scrollIntoView({
                        behavior: 'auto',
                        block: 'start'
                    });

                }, 100);

            } else {

                showTab('contact');
            }
        }
    }

    window.onload = activateCurrentTab;

</script>

<style>
    .tab-buttons {
        border-bottom: 2px solid #ddd;
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
    }

    .tab-btn {
        padding: 12px 22px;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 20px;
        font-weight: 600;
        color: #555;
        position: relative;
    }

    .tab-btn:hover {
        color: #007bff;
    }

    .tab-btn.active {
        color: #007bff;
        font-weight: 600;
    }

    .tab-btn.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: #007bff;
        border-radius: 2px;
    }

    .tab-content {
        transition: all 0.2s ease;
    }

    .email-log-wrapper {
        overflow-x: auto;
    }

    .email-log-wrapper .grid-view {
        overflow: visible;
    }

    .email-log-wrapper table {
        min-width: 1050px;
    }
</style>


