<?php

namespace frontend\models\client;

use Yii;

/**
 * This is the model class for table "client_emails".
 *
 * @property int $id
 * @property int $client_id
 * @property string|null $email_address
 *
 * @property Clients $client
 */
class ClientEmails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_emails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id'], 'required'],
            [['client_id'], 'integer'],
            [['email_address'], 'string', 'max' => 255],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'email_address' => 'Email Address',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }
}
