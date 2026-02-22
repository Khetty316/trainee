<?php

namespace frontend\models\client;

use Yii;

/**
 * This is the model class for table "client_contact".
 *
 * @property int $id
 * @property int $client_id
 * @property string|null $email_address
 * @property string|null $name
 * @property string|null $position
 * @property string|null $contact_number
 * @property string|null $fax
 *
 * @property Clients $client
 */
class ClientContact extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_contact';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id'], 'required'],
            [['client_id'], 'integer'],
            [['email_address', 'name', 'position', 'contact_number', 'fax'], 'string', 'max' => 255],
            [['email_address', 'name', 'position', 'contact_number', 'fax', 'id'], 'safe'],
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
            'name' => 'Name',
            'position' => 'Position',
            'contact_number' => 'Contact Number',
            'fax' => 'Fax',
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
