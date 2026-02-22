<?php

namespace frontend\models\projectquotation;

use Yii;
use common\models\User;

/**
 * This is the model class for table "quotation_emails".
 *
 * @property int $id
 * @property int|null $quotation_id
 * @property int|null $client_id
 * @property string|null $sender
 * @property string|null $recipient
 * @property string|null $Cc
 * @property string|null $Bcc
 * @property string|null $subject
 * @property string|null $content
 * @property string|null $attachment
 * @property string|null $email_type
 * @property int|null $sent_by
 * @property string|null $sent_at
 *
 * @property QuotationEmailAttachments[] $quotationEmailAttachments
 * @property QuotationPdfMasters $quotation
 * @property User $sentBy
 */
class QuotationEmails extends \yii\db\ActiveRecord {

    /**
     * @var UploadedFile[] for handling multiple file uploads
     */
    public $attachments;
    
    const USER_MANUAL_SENDING_EMAIL_FILENAME = "T1B1-Sending Quotation via Email-00.pdf";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'quotation_emails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['quotation_id', 'client_id', 'sent_by'], 'integer'],
            [['content'], 'string'],
            [['sent_at'], 'safe'],
            [['sender', 'recipient', 'Cc', 'Bcc', 'subject', 'email_type'], 'string', 'max' => 255],
            [['quotation_id'], 'exist', 'skipOnError' => true, 'targetClass' => QuotationPdfMasters::className(), 'targetAttribute' => ['quotation_id' => 'id']],
            [['attachments'], 'file', 'extensions' => 'pdf', 'maxFiles' => 10],
            // check file size
            ['attachments', 'validateTotalSize'],
            // validate email address
            [['sender', 'recipient'], 'trim'],
            [['sender', 'recipient'], 'email'],
            // allow multiple emails
            [['Cc', 'Bcc'], 'validateEmails'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'quotation_id' => 'Quotation ID',
            'client_id' => 'Client ID',
            'sender' => 'Sender',
            'recipient' => 'Recipient',
            'Cc' => 'Cc',
            'Bcc' => 'Bcc',
            'subject' => 'Subject',
            'content' => 'Content',
            'attachment' => 'Attachment',
            'email_type' => 'Email Type',
            'sent_by' => 'Sent By',
            'sent_at' => 'Sent At',
        ];
    }

    /**
     * Gets query for [[QuotationEmailAttachments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationEmailAttachments() {
        return $this->hasMany(QuotationEmailAttachments::className(), ['email_id' => 'id']);
    }

    /**
     * Gets query for [[Quotation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotation() {
        return $this->hasOne(QuotationPdfMasters::className(), ['id' => 'quotation_id']);
    }

    /**
     * Gets query for [[SentBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSentBy() {
        return $this->hasOne(User::className(), ['id' => 'sent_by']);
    }

    public function validateTotalSize($attribute, $params) {
        $files = \yii\web\UploadedFile::getInstances($this, $attribute);
        $totalSize = 0;

        foreach ($files as $file) {
            $totalSize += $file->size;
        }

        if ($totalSize > 20 * 1024 * 1024) {
            $this->addError($attribute, 'Total size exceeded 20MB.');
        }
    }

    public function validateEmails($attribute, $params) {
        if ($this->$attribute === null || trim($this->$attribute) === '') {
            return;
        }

        $emails = preg_split('/[;,]+/', $this->$attribute, -1, PREG_SPLIT_NO_EMPTY);
        $emailValidator = new \yii\validators\EmailValidator();

        foreach ($emails as $email) {
            $email = trim($email);

            $email = preg_replace('/\s+/', '', $email);
//            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                $this->addError($attribute, "Invalid email address: {$email}");
//            }
            if (!$emailValidator->validate($email, $error)) {
                $this->addError($attribute, "Invalid email address: {$email}");
            }
        }
    }
    
    //created by khetty, 15/11/2025
    // Add this helper method to validate email addresses
    public function validateEmailAddress($email) {
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        // Extract domain and check DNS records
        $domain = substr(strrchr($email, "@"), 1);

        if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
            return false;
        }

        return true;
    }
}
