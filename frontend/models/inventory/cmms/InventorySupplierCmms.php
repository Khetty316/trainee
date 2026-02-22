<?php

namespace frontend\models\inventory\cmms;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_supplier_cmms".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $name
 * @property string|null $address1
 * @property string|null $address2
 * @property string|null $address3
 * @property string|null $address4
 * @property string|null $contact_name
 * @property string|null $contact_number
 * @property string|null $contact_email
 * @property string|null $contact_fax
 * @property string|null $agent_terms
 * @property int|null $active_sts
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class InventorySupplierCmms extends \yii\db\ActiveRecord
{
    const Prefix_SupplierCode = "SUPP";
    const runningNoLength = 3;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_supplier_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name', 'address1', 'address2', 'address3', 'address4', 'contact_name', 'contact_number', 'contact_email', 'contact_fax', 'agent_terms'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Supplier No.',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'address3' => 'Address3',
            'address4' => 'Address4',
            'contact_name' => 'Contact Person',
            'contact_number' => 'Contact Number',
            'contact_email' => 'Email Address',
            'contact_fax' => 'Fax',
            'agent_terms' => 'Agent Terms',
            'active_sts' => 'Active Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
    
    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }
    
    public function generateSupplierCode() {
        $currentYear = date("Y");

        $initialCode = self::Prefix_SupplierCode;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $code = $initialCode . $runningNo; 

        return $code;
    }
        
    public function getAllDropDownSupplierList(){
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 1])->all(), "code", "name");
    }
}
