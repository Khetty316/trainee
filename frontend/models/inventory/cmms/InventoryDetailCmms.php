<?php

namespace frontend\models\inventory\cmms;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_detail_cmms".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $supplier_cmms_code
 * @property string|null $brand_cmms_code
 * @property string|null $model_cmms_code
 * @property int|null $stock_level_min
 * @property int|null $stock_level_sts
 * @property int|null $quantity_stock
 * @property int|null $quantity_required
 * @property int|null $quantity_reorder
 * @property int|null $quantity_pending_arrival
 * @property int|null $active_sts
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryReorderItemCmms[] $inventoryReorderItemCmms
 */
class InventoryDetailCmms extends \yii\db\ActiveRecord {

    const Prefix_Code = "CODE";
    const runningNoLength = 5;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_detail_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['stock_level_min', 'stock_level_sts', 'quantity_stock', 'quantity_required', 'quantity_reorder', 'quantity_pending_arrival', 'active_sts', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['supplier_cmms_code', 'brand_cmms_code', 'model_cmms_code'], 'string', 'max' => 255],
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
            'supplier_cmms_code' => 'Supplier No.',
            'brand_cmms_code' => 'Brand Code',
            'model_cmms_code' => 'Model Code',
            'stock_level_min' => 'Stock Level Minimum',
            'stock_level_sts' => 'Stock Level Status',
            'quantity_stock' => 'Quantity Stock',
            'quantity_required' => 'Quantity Required',
            'quantity_reorder' => 'Quantity Reorder',
            'quantity_pending_arrival' => 'Quantity Pending Arrival',
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

    /**
     * Gets query for [[InventoryReorderItemCmms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItemCmms() {
        return $this->hasMany(InventoryReorderItemCmms::className(), ['inventory_detail_cmms_id' => 'id']);
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
    
    public function generateModelCode() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialCode = self::Prefix_Code;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $code = $initialCode . $runningNo . "-" . $currentMonth . $currentYearShort;

        return $code;
    }
}
