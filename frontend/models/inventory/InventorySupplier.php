<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_supplier".
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
 * @property int|null $active_sts 0 = no, 1 = yes
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property InventoryDetail[] $inventoryDetails
 * @property InventoryPurchaseRequest[] $inventoryPurchaseRequests
 * @property User $createdBy
 * @property User $updatedBy
 */
class InventorySupplier extends \yii\db\ActiveRecord {

    const Prefix_SupplierCode = "SUPP";
    const runningNoLength = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_supplier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['code', 'name', 'address1', 'address2', 'address3', 'address4', 'contact_name', 'contact_number', 'contact_email', 'contact_fax', 'agent_terms'], 'string', 'max' => 255],
            [['code'], 'unique'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'address3' => 'Address3',
            'address4' => 'Address4',
            'contact_name' => 'Contact Name',
            'contact_number' => 'Contact Number',
            'contact_email' => 'Contact Email',
            'contact_fax' => 'Contact Fax',
            'agent_terms' => 'Agent Terms',
            'active_sts' => 'Active Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InventoryDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetails() {
        return $this->hasMany(InventoryDetail::className(), ['supplier_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryPurchaseRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseRequests() {
        return $this->hasMany(InventoryPurchaseRequest::className(), ['inventory_supplier_id' => 'id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->code = $this->generateSupplierCode();
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

//    public function generateSupplierCode() {
//        $currentYear = date("Y");
//
//        $initialCode = self::Prefix_SupplierCode;
//        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);
//
//        $runningNo = $query->count() + 1;
//        if (strlen($runningNo) < self::runningNoLength) {
//            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
//        }
//
//        $code = $initialCode . $runningNo;
//
//        return $code;
//    }

    public function generateSupplierCode() {
        $prefix = strtoupper(substr($this->name, 0, 1));

        $lastCode = self::find()
                ->select('code')
                ->where(['like', 'code', $prefix . '%', false])
                ->orderBy(['code' => SORT_DESC])
                ->scalar();

        $lastNumber = $lastCode ? (int) substr($lastCode, 1) : 0;
        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function getAllDropDownSupplierList() {
        return \yii\helpers\ArrayHelper::map(self::find()->where(['active_sts' => 2])->all(), "id", "name");
    }

    /* ==============================
     * NORMALIZE NAME
     * ============================== */

    public static function normalizeName($name) {
        $name = strtoupper($name);
        $name = preg_replace('/\b(SDN|BHD|SDN\. BHD\.)\b/', '', $name);
        $name = preg_replace('/[^A-Z0-9 ]/', '', $name);
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    /* ==============================
     * CHECK SIMILAR SUPPLIER
     * ============================== */

    public static function findSimilarSupplier($name, $threshold = 80) {
        $normalizedInput = self::normalizeName($name);
        $suppliers = self::find()->all();

        foreach ($suppliers as $supplier) {
            $normalizedDb = self::normalizeName($supplier->name);
            similar_text($normalizedInput, $normalizedDb, $percent);

            if ($percent >= $threshold) {
                return [
                    'match' => true,
                    'percent' => round($percent, 2),
                    'existing_name' => $supplier->name,
                ];
            }
        }

        return ['match' => false];
    }
}
