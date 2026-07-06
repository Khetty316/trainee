<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\client\ClientReminderLetterEmailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Reminder Letter Emails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-reminder-letter-emails-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Reminder Letter Emails', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'template_id',
            'client_id',
            'sender',
            'recipient',
            //'Cc',
            //'Bcc',
            //'subject',
            //'content:ntext',
            //'sent_by',
            //'sent_at',
            //'status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
