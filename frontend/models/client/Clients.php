<?php

namespace frontend\models\client;

use Yii;
use frontend\models\common\RefArea;
use frontend\models\common\RefCountries;
use common\models\User;
use frontend\models\common\RefState;
//
/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property string $client_code
 * @property string|null $ac_no_tk
 * @property string|null $ac_no_tke
 * @property string|null $ac_no_tkm
 * @property string $company_name
 * @property string $company_registration_no
 * @property string $company_tin
 * @property string|null $payment_term
 * @property float|null $tk_balance
 * @property float|null $tke_balance
 * @property float|null $tkm_balance
 * @property float|null $current_outstanding_balance
 * @property string|null $contact_person
 * @property string|null $contact_position
 * @property string|null $contact_number
 * @property string|null $contact_fax
 * @property string|null $email
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $postcode
 * @property int|null $area
 * @property int|null $state
 * @property string|null $country
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property RefArea $area0
 * @property ClientContact[] $clientContacts
 * @property ClientDebt[] $clientDebts
 * @property ClientEmails[] $clientEmails
 * @property RefCountries $country0
 * @property User $createdBy
 * @property RefState $state0
 * @property ProjectProductionMaster[] $projectProductionMasters
 * @property ProjectQClients[] $projectQClients
 * @property RefState $state0
 */
class Clients extends \yii\db\ActiveRecord {

    public $areaName, $stateName, $countryName;
    public $contact_person;
    public $contact_number;
    public $contact_fax;
    public $contact_email;
    public $contact_position;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['client_code', 'company_name'], 'required'],
            [['current_outstanding_balance'], 'number'],
            [['area', 'state', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at', 'company_registration_no', 'company_tin'], 'safe'],
            [['client_code'], 'string', 'max' => 10],
            [['ac_no_tk', 'ac_no_tke', 'ac_no_tkm', 'company_name', 'company_registration_no', 'company_tin', 'payment_term', 'contact_person', 'contact_position', 'address_1', 'address_2'], 'string', 'max' => 255],
            [['contact_number', 'contact_fax', 'postcode'], 'string', 'max' => 50],
            [['areaName', 'stateName', 'countryName'], 'string', 'max' => 100],
            [['country'], 'string', 'max' => 5],
            [['client_code'], 'unique'],
//            [['ac_no_tk'], 'unique'],
//            [['ac_no_tke'], 'unique'],
//            [['ac_no_tkm'], 'unique'],
            [['area'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['area' => 'area_id']],
            [['country'], 'exist', 'skipOnError' => true, 'targetClass' => RefCountries::className(), 'targetAttribute' => ['country' => 'country_code']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['state'], 'exist', 'skipOnError' => true, 'targetClass' => RefState::className(), 'targetAttribute' => ['state' => 'state_id']],
            [['tk_balance', 'tke_balance', 'tkm_balance'], 'string', 'max' => 50],
//            [['emails'], 'each', 'rule' => ['email']],
//            [['emails'], 'safe'],
//            [['emails'], 'validateEmails']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'client_code' => 'Client Code',
            'ac_no_tk' => 'A/C No. TK',
            'ac_no_tke' => 'A/C No. TKE',
            'ac_no_tkm' => 'A/C No. TKM',
            'company_name' => 'Company Name',
            'company_registration_no' => 'Company Registration No',
            'company_tin' => 'Company TIN',
            'payment_term' => 'Payment Term',
            'current_outstanding_balance' => 'Total Outstanding Balance',
            'contact_person' => 'Contact Person',
            'contact_position' => 'Contact Position',
            'contact_number' => 'Contact Number',
            'contact_fax' => 'Fax',
            'address_1' => 'Address',
            'address_2' => 'Address 2',
            'postcode' => 'Postcode',
            'area' => 'Area',
            'state' => 'State',
            'country' => 'Country',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'tk_balance' => 'Current Outstanding Balance (TK)',
            'tke_balance' => 'Current Outstanding Balance (TKE)',
            'tkm_balance' => 'Current Outstanding Balance (TKM)',
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
     * Gets query for [[Country0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0() {
        return $this->hasOne(RefCountries::className(), ['country_code' => 'country']);
    }

    /**
     * Gets query for [[ClientDebts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClientDebts() {
        return $this->hasMany(ClientDebt::className(), ['client_id' => 'id']);
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
     * Gets query for [[Country0]].
     *
     * @return \yii\db\ActiveQuery
     */
//    public function getCountry0()
//    {
//        return $this->hasOne(RefCountries::className(), ['country_code' => 'country']);
//    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
//    public function getCreatedBy()
//    {
//        return $this->hasOne(User::className(), ['id' => 'created_by']);
//    }

    /**
     * Gets query for [[ProjectProductionMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionMasters() {
        return $this->hasMany(ProjectProductionMaster::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectQClients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQClients() {
        return $this->hasMany(ProjectQClients::className(), ['client_id' => 'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_by = Yii::$app->user->id;
            $this->updated_at = new \yii\db\Expression('NOW()');
        }

        return parent::beforeSave($insert);
    }

    public static function getAutocompleteList() {
        $data = Clients::find()
                ->select(['company_name as value', 'company_name as label', 'id as id'])
                ->asArray()
                ->orderBy(['company_name' => SORT_ASC])
                ->all();
        return $data;
    }

    public static function getAttns($clientId) {
        $names = ClientContact::find()
                ->select('name')
                ->where(['client_id' => $clientId])
                ->andWhere(['<>', 'name', ''])
                ->column();

        return array_unique(array_filter($names));
    }

    public static function getEmails($clientId) {
        $emails = ClientContact::find()
                ->select('email_address')
                ->where(['client_id' => $clientId])
                ->andWhere(['<>', 'email_address', ''])
                ->column();

//        return array_combine($email, $email);
        return array_unique(array_filter($emails));
    }

    public static function getTelNos($clientId) {
        $telNos = ClientContact::find()
                ->select('contact_number')
                ->where(['client_id' => $clientId])
                ->andWhere(['<>', 'contact_number', ''])
                ->column();

        return array_unique(array_filter($telNos));
    }

    public static function getFaxes($clientId) {
        $faxes = ClientContact::find()
                ->select('fax')
                ->where(['client_id' => $clientId])
                ->andWhere(['<>', 'fax', ''])
                ->column();

        return array_unique(array_filter($faxes));
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(self::find()->orderBy(['company_name' => SORT_ASC])->all(), "id", "company_name");
    }

//    public function processAndSave() {
//        $this->setArea();
//        $this->setState();
//        $this->setCountry();
////        $this->setEmails();
//        $companyCodePrefix = substr($this->company_name, 0, 1);
//
//        $lastCode = (Clients::find()->where('client_code like "' . addslashes($companyCodePrefix) . '%"')->max('client_code')) ?? "00";
//        $lastNumber = substr_replace($lastCode, "", 0, 1);
//
//        $currentNumber = $lastNumber + 1;
//        if ($currentNumber < 10) {
//            $this->client_code = $companyCodePrefix . "00" . $currentNumber;
//        } else if ($currentNumber < 100) {
//            $this->client_code = $companyCodePrefix . "0" . $currentNumber;
//        }
//        
//        return $this->save();
//    }

    public function processAndSave() {
        $this->setArea();
        $this->setState();
        $this->setCountry();
//  $this->setEmails();

        $companyCodePrefix = substr($this->company_name, 0, 1);
        $lastCode = (Clients::find()->where('client_code like "' . addslashes($companyCodePrefix) . '%"')->max('client_code')) ?? "00";
        $lastNumber = (int) substr($lastCode, 1); // Get number part and convert to int
        $currentNumber = $lastNumber + 1;

        // Pad with zeros to make it 3 digits (handles any number)
        $this->client_code = $companyCodePrefix . str_pad($currentNumber, 3, '0', STR_PAD_LEFT);

        return $this->save();
    }

    private function setArea() {
        $this->areaName = trim($this->areaName);

        $area = RefArea::find()->where(['area_name' => $this->areaName])->one();
        if ($area) {
            $this->area = $area->area_id;
        } else if ($this->areaName != "") {
            $area = new RefArea();
            $area->createNew($this->areaName);
            $this->area = $area->area_id;
        }
    }

    private function setState() {
        $this->stateName = trim($this->stateName);
        $state = RefState::find()->where(['state_name' => $this->stateName])->one();
        if ($state) {
            $this->state = $state->state_id;
        } else if ($this->stateName != "") {
            $state = new RefState();
            $state->createNew($this->stateName);
            $this->state = $state->state_id;
        }
    }

    private function setCountry() {
        $this->countryName = trim($this->countryName);
        $country = RefCountries::find()->where(['country_name' => $this->countryName])->one();
        if ($country) {
            $this->country = $country->country_code;
        } else if ($this->countryName != "") {
            $country = new RefCountries();
            $country->createNew($this->countryName);
            $this->state = $country->state_id;
        }
    }

//    public function validateEmails($attribute, $params)
//    {
////    if ($this->$attribute === null || trim($this->$attribute) === '') {
////        return;
////    }
//        
////        $emails = preg_split('/[;,]+/', $this->$attribute, -1, PREG_SPLIT_NO_EMPTY);
//        $emailValidator = new \yii\validators\EmailValidator();
//        
//        foreach ($this->emails as $email) {
//            $email = trim($email);
//            
//            $email = preg_replace('/\s+/', '', $email);
////            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
////                $this->addError($attribute, "Invalid email address: {$email}");
////            }
//            if (!$emailValidator->validate($email, $error)) {
//                $this->addError($attribute, "Invalid email address: {$email}");
//            }
//        }
//    }

    public function getClientContacts() {
        return $this->hasMany(ClientContact::className(), ['client_id' => 'id']);
    }
}
