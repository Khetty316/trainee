<?php

namespace frontend\models\client;

use Yii;
use common\models\User;

/**
 * This is the model class for table "client_reminder_letter_emails".
 *
 * @property int $id
 * @property int|null $template_id
 * @property int|null $client_id
 * @property string|null $sender
 * @property string|null $recipient
 * @property string|null $Cc
 * @property string|null $Bcc
 * @property string|null $subject
 * @property string|null $content
 * @property int|null $sent_by
 * @property string|null $sent_at
 * @property int|null $status 1 = draft, 2 = sent
 *
 * @property Clients $client
 * @property ClientReminderLetterEmailAttachment[] $clientReminderLetterEmailAttachments
 * @property ClientReminderLetterEmailDetail[] $clientReminderLetterEmailDetails
 * @property User $sentBy
 * @property ClientReminderLetterTemplate $template
 */
class ClientReminderLetterEmails extends \yii\db\ActiveRecord {

    public $attachment;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'client_reminder_letter_emails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['template_id', 'client_id', 'sent_by', 'status'], 'integer'],
            [['content'], 'string'],
            [['sent_at'], 'safe'],
            [['sender', 'recipient', 'subject', 'content'], 'required'],
            [['recipient'], 'email'],
            [['sender'], 'match',
                'pattern' => '/^[A-Za-z0-9._%+-]+@tenagakenari\.com\.my$/i',
                'message' => 'Sender email must be a valid @tenagakenari.com.my email address.',
            ],
            [['sender', 'recipient', 'Cc', 'Bcc', 'subject'], 'string', 'max' => 255],
            [['attachment'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 20],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['sent_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sent_by' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientReminderLetterTemplate::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'template_id' => 'Template ID',
            'client_id' => 'Client ID',
            'sender' => 'Sender',
            'recipient' => 'Recipient',
            'Cc' => 'Cc',
            'Bcc' => 'Bcc',
            'subject' => 'Subject',
            'content' => 'Content',
            'sent_by' => 'Sent By',
            'sent_at' => 'Sent At',
            'status' => 'Status',
            'created_at' => 'Created At'
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[ClientReminderLetterEmailAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientReminderLetterEmailAttachments() {
        return $this->hasMany(ClientReminderLetterEmailAttachment::className(), ['email_id' => 'id']);
    }

    /**
     * Gets query for [[ClientReminderLetterEmailDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientReminderLetterEmailDetails() {
        return $this->hasMany(ClientReminderLetterEmailDetail::className(), ['email_id' => 'id']);
    }

    /**
     * Gets query for [[SentBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSentBy() {
        return $this->hasOne(User::className(), ['id' => 'sent_by']);
    }

    /**
     * Gets query for [[Template]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate() {
        return $this->hasOne(ClientReminderLetterTemplate::className(), ['id' => 'template_id']);
    }

    public function getAttachments() {
        return $this->hasMany(
                        ClientReminderLetterEmailAttachment::className(),
                        ['email_id' => 'id']
                );
    }

    public function getSenderUser() {
        return $this->hasOne(
                        \common\models\User::className(),
                        ['id' => 'sent_by']
                );
    }
}
