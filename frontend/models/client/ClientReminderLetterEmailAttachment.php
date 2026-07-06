<?php

namespace frontend\models\client;

use Yii;

/**
 * This is the model class for table "client_reminder_letter_email_attachment".
 *
 * @property int $id
 * @property int|null $email_id
 * @property string|null $file_name
 * @property string|null $company_group
 *
 * @property ClientReminderLetterEmails $email
 */
class ClientReminderLetterEmailAttachment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_reminder_letter_email_attachment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_id'], 'integer'],
            [['file_name'], 'string', 'max' => 255],
            [['company_group'], 'string', 'max' => 10],
            [['email_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientReminderLetterEmails::className(), 'targetAttribute' => ['email_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email_id' => 'Email ID',
            'file_name' => 'File Name',
            'company_group' => 'Company Group',
        ];
    }

    /**
     * Gets query for [[Email]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmail()
    {
        return $this->hasOne(ClientReminderLetterEmails::className(), ['id' => 'email_id']);
    }
}
