<?php

namespace frontend\models\working\contact;

use Yii;
use frontend\models\common\RefCountries;
use frontend\models\common\RefState;
use frontend\models\common\RefContactType;
use frontend\models\common\RefArea;
use common\models\User;

/**
 * This is the model class for table "contact_master".
 *
 * @property int $id
 * @property string $contact_type
 * @property string|null $company_name
 * @property string|null $contact_person
 * @property string|null $contact_position
 * @property string|null $contact_number
 * @property string|null $email
 * @property string|null $address
 * @property string|null $postcode
 * @property int|null $area
 * @property int|null $state
 * @property string|null $country
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property RefArea $area0
 * @property RefContactType $contactType
 * @property RefCountries $country0
 * @property User $createdBy
 * @property RefState $state0
 * @property ProspectDetail[] $prospectDetails
 */
class ContactMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'contact_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['contact_type'], 'required'],
            [['area', 'state', 'created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['contact_type'], 'string', 'max' => 10],
            [['company_name', 'contact_person', 'contact_position', 'address'], 'string', 'max' => 255],
            [['contact_number', 'postcode'], 'string', 'max' => 50],
            [['email'], 'string', 'max' => 100],
            [['country'], 'string', 'max' => 5],
            [['area'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['area' => 'area_id']],
            [['contact_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefContactType::className(), 'targetAttribute' => ['contact_type' => 'code']],
            [['country'], 'exist', 'skipOnError' => true, 'targetClass' => RefCountries::className(), 'targetAttribute' => ['country' => 'country_code']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['state'], 'exist', 'skipOnError' => true, 'targetClass' => RefState::className(), 'targetAttribute' => ['state' => 'state_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'contact_type' => 'Type',
            'company_name' => 'Company Name',
            'contact_person' => 'Contact Person',
            'contact_position' => 'Contact Position',
            'contact_number' => 'Contact Number',
            'email' => 'Email',
            'address' => 'Address',
            'postcode' => 'Postcode',
            'area' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[Area0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea0() {
        return $this->hasOne(RefArea::className(), ['area_id' => 'area']);
    }

    /**
     * Gets query for [[ContactType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContactType() {
        return $this->hasOne(RefContactType::className(), ['code' => 'contact_type']);
    }

    /**
     * Gets query for [[Country0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0() {
        return $this->hasOne(RefCountries::className(), ['country_code' => 'country']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[State0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getState0() {
        return $this->hasOne(RefState::className(), ['state_id' => 'state']);
    }

    /**
     * Gets query for [[ProspectDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetails() {
        return $this->hasMany(ProspectDetail::className(), ['client_id' => 'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        }
        if ($this->country == "") {
            $this->country = null;
        }


        return parent::beforeSave($insert);
    }

    public static function getClientList() {
        return ContactMaster::find()
                        ->select(['company_name as value', 'id as id', 'company_name as label','contact_person','contact_number','email'])
                        ->where(['contact_type' => 'client'])
                        ->orderBy(["company_name" => SORT_ASC])
                        ->distinct(true)
                        ->asArray()
                        ->all();
    }
    public static function getVendorList() {
        return ContactMaster::find()
                        ->select(['company_name as value', 'id as id', 'company_name as label','contact_person','contact_number','email'])
                        ->where(['contact_type' => 'vendor'])
                        ->orderBy(["company_name" => SORT_ASC])
                        ->distinct(true)
                        ->asArray()
                        ->all();
    }

}
