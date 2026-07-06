<?php

namespace frontend\models\client;

use Yii;

/**
 * This is the model class for table "client_reminder_letter_email_detail".
 *
 * @property int $id
 * @property int|null $email_id
 * @property int|null $template_id
 * @property string|null $company_group TK / TKE / TKM
 * @property string|null $template_content
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property ClientReminderLetterEmails $email
 * @property ClientReminderLetterTemplate $template
 */
class ClientReminderLetterEmailDetail extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'client_reminder_letter_email_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['email_id', 'template_id'], 'integer'],
            [['template_content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['company_group'], 'string', 'max' => 10],
            [['company_group'], 'required', 'message' => 'Please Select a Company Group.'],
            [['company_group'], 'exist',
                'targetClass' => RefCompanyGroupList::className(),
                'targetAttribute' => ['company_group' => 'code'], // or 'id' if you store the id
                'message' => 'The selected Company Group is invalid.',
            ],
            [['email_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientReminderLetterEmails::className(), 'targetAttribute' => ['email_id' => 'id']],
            [['template_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientReminderLetterTemplate::className(), 'targetAttribute' => ['template_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'email_id' => 'Email ID',
            'template_id' => 'Template ID',
            'company_group' => 'Company Group',
            'template_content' => 'Template Content',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Email]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmail() {
        return $this->hasOne(ClientReminderLetterEmails::className(), ['id' => 'email_id']);
    }

    /**
     * Gets query for [[Template]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemplate() {
        return $this->hasOne(ClientReminderLetterTemplate::className(), ['id' => 'template_id']);
    }

    /**
     * Gets query for [[Details]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDetails() {
        return $this->hasMany(
                        ClientReminderLetterEmailDetail::className(),
                        ['email_id' => 'id']
                );
    }
}
