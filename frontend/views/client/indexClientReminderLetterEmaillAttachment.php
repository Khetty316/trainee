<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\client\ClientReminderLetterEmailAttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Reminder Letter Email Attachments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-reminder-letter-email-attachment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Reminder Letter Email Attachment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'email_id:email',
            'file_name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
