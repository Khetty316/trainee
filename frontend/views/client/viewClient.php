<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\myTools\MyFormatter;

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

    <p>
        <?= Html::a('Update', ['update-client', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Delete', ['delete-client', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this client?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

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

    <legend class="w-auto px-2 m-0">Contact Person:</legend>
    <table class="table table-sm mt-2" width="100%">
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

    <!-- ClientDebt table -->
    <?=
    $this->render('indexClientDebt', [
        'dataProvider' => $dataProvider,
        'searchModel' => $searchModel
    ])
    ?>

</div>
