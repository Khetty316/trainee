<?php

namespace frontend\models\projectquotation;

use Yii;

/**
 * This is the model class for table "quotation_email_attachments".
 *
 * @property int $id
 * @property int $email_id
 * @property resource|null $file_content
 *
 * @property QuotationEmails $email
 */
class QuotationEmailAttachments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation_email_attachments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email_id'], 'required'],
            [['email_id'], 'integer'],
            [['file_content'], 'string'],
            [['email_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotationEmails::className(), 'targetAttribute' => ['email_id' => 'id']],
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
            'file_content' => 'File Content',
        ];
    }

    /**
     * Gets query for [[Email]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmail()
    {
        return $this->hasOne(QuotationEmails::className(), ['id' => 'email_id']);
    }
}
