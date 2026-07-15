<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\client\ClientReminderLetterEmails */

$this->title = 'Debt Email Reminder';
$this->params['breadcrumbs'][] = ['label' => 'Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->client->company_name, 'url' => ['view-client', 'id' => $model->client_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="client-reminder-letter-emails-view">

    <?php
    $this->registerCss("
.detail-view th,
.detail-view td {
    padding: 4px 8px !important;
    vertical-align: top;
    line-height: 1.2;
}
.email-content,
.email-content * {
    font-family: Arial, Helvetica, sans-serif !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
}
");
    ?>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 style="margin:0;"><?= Html::encode($this->title) ?></h3>
        <?php
        if ($model->status === 1) {
            echo
            Html::a(
                    'Edit Draft <i class="fas fa-edit"></i>',
                    [
                        'create-reminder-letter-emails',
                        'client_id' => $model->client_id,
                        'id' => $model->id,
                    ],
                    [
                        'class' => 'btn btn-primary',
                    ]
            );
        }
        ?>
    </div>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            [
                'label' => 'Reminder Letter Template',
                'format' => 'raw',
                'value' => function ($model) {
                    if (empty($model->clientReminderLetterEmailDetails)) {
                        return 'N/A';
                    }
                    $templates = [];
                    foreach ($model->clientReminderLetterEmailDetails as $detail) {
                        $company = $detail->company_group;
                        $template = $detail->template->letter_name ?? '-';
                        $templates[] = $company . ' - ' . $template;
                    }
                    return implode('<br>', $templates);
                },
            ],
//            'client_id',
            'sender',
            [
                'attribute' => 'recipient',
                'format' => 'html',
                'value' => function ($model) {
                    if (empty($model->recipient)) {
                        return '-';
                    }
                    $emails = array_map('trim', explode(',', $model->recipient));
                    return implode('<br>', $emails);
                },
            ],
            [
                'attribute' => 'Cc',
                'format' => 'html',
                'value' => function ($model) {

                    if (empty($model->Cc)) {
                        return '-';
                    }
                    $emails = array_map('trim', explode(',', $model->Cc));
                    return implode('<br>', $emails);
                },
            ],
            [
                'attribute' => 'Bcc',
                'format' => 'html',
                'value' => function ($model) {
                    if (empty($model->Bcc)) {
                        return '-';
                    }
                    $emails = array_map('trim', explode(',', $model->Bcc));
                    return implode('<br>', $emails);
                },
            ],
            'subject',
            [
                'attribute' => 'content',
                'format' => 'raw',
                'value' => function ($model) {

                    return '<div class="email-content">'
                    . $model->content .
                    '</div>';
                },
            ],
            [
                'attribute' => 'sent_by',
                'label' => 'Sent By',
                'value' => function ($model) {

                    if ($model->status == 1) {
                        return 'Not Sent Yet';
                    }
                    return $model->senderUser->fullname ?? '-';
                },
            ],
            [
                'attribute' => 'sent_at',
                'label' => 'Sent At',
                'value' => function ($model) {

                    if ($model->status == 1 || empty($model->sent_at)) {
                        return 'Not Sent Yet';
                    }
                    return Yii::$app->formatter->asDatetime(
                            $model->sent_at,
                            'php:d/m/Y H:i'
                    );
                },
            ],
            [
                'label' => 'Attachment',
                'format' => 'raw',
                'value' => function ($model) {
                    if (empty($model->attachments)) {
                        return 'N/A';
                    }
                    $links = [];
                    foreach ($model->attachments as $attachment) {
                        $displayName = preg_replace(
                                '/_\(\d+\)(?=\.[^.]+$)/',
                                '',
                                $attachment->file_name
                        );
                        $links[] = Html::a(
                                $displayName,
                                Yii::getAlias('@web/uploads/client-reminder-letter-attachment/' . $attachment->file_name),
                                ['target' => '_blank']
                        );
                    }
                    return implode('<br>', $links);
                },
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    switch ($model->status) {
                        case 1:
                            return 'Draft';
                        case 2:
                            return 'Sent';
                        default:
                            return 'Unknown';
                    }
                },
            ],
        ],
    ])
    ?>
</div>
