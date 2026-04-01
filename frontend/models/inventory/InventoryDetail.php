<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\inventory\InventorySupplier;
use frontend\models\inventory\InventoryBrand;
use frontend\models\inventory\InventoryModel;
use frontend\models\common\RefCurrencies;

/**
 * This is the model class for table "inventory_detail".
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $department_code
 * @property int|null $supplier_id
 * @property int|null $brand_id
 * @property int|null $model_id
 * @property int|null $currency_id
 * @property float|null $unit_price
 * @property int|null $minimum_qty
 * @property int|null $stock_level_sts
 * @property int|null $stock_in
 * @property int|null $stock_on_hand
 * @property int|null $stock_reserved
 * @property int|null $stock_out
 * @property int|null $stock_available
 * @property int|null $qty_pending_receipt
 * @property int|null $is_new 1 = existing, 2 = new
 * @property int|null $active_sts
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $reorder_qty
 * @property int|null $required_qty
 *
 * @property CmmsPartList[] $cmmsPartLists
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventorySupplier $supplier
 * @property InventoryBrand $brand
 * @property InventoryModel $model
 * @property RefCurrencies $currency
 * @property InventoryOrderRequest[] $inventoryOrderRequests
 * @property InventoryPurchaseOrderItem[] $inventoryPurchaseOrderItems
 * @property InventoryReorderItem[] $inventoryReorderItems
 * @property InventoryReserveItem[] $inventoryReserveItems
 * @property InventoryStockoutbound[] $inventoryStockoutbounds
 */
class InventoryDetail extends \yii\db\ActiveRecord {

    const PREFIX_ITEM = "INT";
    const RUNNING_NO_LENGTH = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['supplier_id', 'brand_id', 'model_id', 'currency_id', 'minimum_qty', 'stock_level_sts', 'stock_in', 'stock_on_hand', 'stock_reserved', 'stock_out', 'stock_available', 'qty_pending_receipt', 'is_new', 'active_sts', 'created_by', 'updated_by', 'reorder_qty', 'required_qty'], 'integer'],
            [['unit_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['code'], 'string', 'max' => 255],
            [['department_code'], 'string', 'max' => 50],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['supplier_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventorySupplier::className(), 'targetAttribute' => ['supplier_id' => 'id']],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['model_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryModel::className(), 'targetAttribute' => ['model_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'department_code' => 'Department Code',
            'supplier_id' => 'Supplier ID',
            'brand_id' => 'Brand ID',
            'model_id' => 'Model ID',
            'currency_id' => 'Currency ID',
            'unit_price' => 'Unit Price',
            'minimum_qty' => 'Minimum Qty',
            'stock_level_sts' => 'Stock Level Sts',
            'stock_in' => 'Stock In',
            'stock_on_hand' => 'Stock On Hand',
            'stock_reserved' => 'Stock Reserved',
            'stock_out' => 'Stock Out',
            'stock_available' => 'Stock Available',
            'qty_pending_receipt' => 'Qty Pending Receipt',
            'is_new' => 'Is New',
            'active_sts' => 'Active',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'reorder_qty' => 'Reorder Qty',
            'required_qty' => 'Required Qty',
        ];
    }

    /**
     * Gets query for [[CmmsPartLists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPartLists() {
        return $this->hasMany(CmmsPartList::className(), ['inventory_id' => 'id']);
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
     * Gets query for [[Supplier]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSupplier() {
        return $this->hasOne(InventorySupplier::className(), ['id' => 'supplier_id']);
    }

    /**
     * Gets query for [[Brand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrand() {
        return $this->hasOne(InventoryBrand::className(), ['id' => 'brand_id']);
    }

    /**
     * Gets query for [[Model]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getModel() {
        return $this->hasOne(InventoryModel::className(), ['id' => 'model_id']);
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency_id']);
    }

    /**
     * Gets query for [[InventoryOrderRequests]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOrderRequests() {
        return $this->hasMany(InventoryOrderRequest::className(), ['inventory_detail_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryPurchaseOrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderItems() {
        return $this->hasMany(InventoryPurchaseOrderItem::className(), ['inventory_detail_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryReorderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItems() {
        return $this->hasMany(InventoryReorderItem::className(), ['inventory_detail_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryReserveItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReserveItems()
    {
        return $this->hasMany(InventoryReserveItem::className(), ['inventory_detail_id' => 'id']);
    }
    
    /**
     * Gets query for [[InventoryStockoutbounds]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryStockoutbounds() {
        return $this->hasMany(InventoryStockoutbound::className(), ['inventory_detail_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->code = $this->generateItemCode();
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

    public function generateItemCode() {
        $department = \frontend\models\common\RefUserDepartments::find()
                ->select('department_key')
                ->where(['code' => $this->department_code])
                ->one();

        $deptCode = $department ? strtoupper($department->department_key) : 'NA';

        $supplier = InventorySupplier::find()
                ->select('code')
                ->where(['id' => $this->supplier_id]) // IMPORTANT
                ->one();

        $supplierCode = $supplier ? strtoupper($supplier->code) : 'NA';

        $baseCode = implode('-', [
            self::PREFIX_ITEM, // INT
            $deptCode,
            $supplierCode
        ]);

        $lastCode = self::find()
                ->select('code')
                ->where(['like', 'code', $baseCode . '%', false])
                ->orderBy(['id' => SORT_DESC])
                ->scalar();

        $runningNo = 1;

        if ($lastCode && preg_match('/(\d+)$/', $lastCode, $matches)) {
            $runningNo = ((int) $matches[1]) + 1;
        }

        $runningNo = str_pad($runningNo, self::RUNNING_NO_LENGTH, '0', STR_PAD_LEFT);

        return $baseCode . '-' . $runningNo;
    }

    public static function existsItemByModelName($departmentCode, $supplierId, $brandId, $modelName) {
        return self::find()
                        ->alias('d')
                        ->innerJoin('inventory_model m', 'm.id = d.model_id')
                        ->andWhere([
                            'd.department_code' => $departmentCode,
                            'd.supplier_id' => $supplierId,
                            'd.brand_id' => $brandId,
                            'd.active_sts' => 2,
                        ])
                        ->andWhere(['like', 'LOWER(m.type)', strtolower(trim($modelName))])
                        ->exists();
    }
}
