<?php

namespace frontend\models\inventory;

use Yii;
use frontend\models\inventory\InventoryPurchaseRequest;
use common\models\User;
use frontend\models\inventory\InventoryBrand;
use frontend\models\common\RefCurrencies;

/**
 * This is the model class for table "inventory_purchase_request_item".
 *
 * @property int $id
 * @property int|null $inventory_pr_id
 * @property int|null $source_type
 * @property int|null $source_id
 * @property string|null $department_code
 * @property int|null $brand_id
 * @property string|null $model_type
 * @property string|null $model_group
 * @property string|null $model_description
 * @property int|null $quantity
 * @property string|null $unit_type
 * @property int|null $currency_id
 * @property float|null $unit_price
 * @property float|null $total_price
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property InventoryPurchaseOrderItem[] $inventoryPurchaseOrderItems
 * @property InventoryPurchaseRequest $inventoryPr
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryBrand $brand
 * @property RefCurrencies $currency
 */
class InventoryPurchaseRequestItem extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_purchase_request_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_pr_id', 'source_type', 'source_id', 'brand_id', 'quantity', 'currency_id', 'created_by', 'updated_by'], 'integer'],
            [['unit_price', 'total_price'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['department_code', 'model_type', 'model_group', 'model_description', 'unit_type'], 'string', 'max' => 255],
            [['inventory_pr_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPurchaseRequest::className(), 'targetAttribute' => ['inventory_pr_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryBrand::className(), 'targetAttribute' => ['brand_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_pr_id' => 'Inventory Pr ID',
            'source_type' => 'Source Type',
            'source_id' => 'Source ID',
            'department_code' => 'Department Code',
            'brand_id' => 'Brand ID',
            'model_type' => 'Model Type',
            'model_group' => 'Model Group',
            'model_description' => 'Model Description',
            'quantity' => 'Quantity',
            'unit_type' => 'Unit Type',
            'currency_id' => 'Currency ID',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InventoryPurchaseOrderItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPurchaseOrderItems() {
        return $this->hasMany(InventoryPurchaseOrderItem::className(), ['inventory_pr_item_id' => 'id']);
    }

    /**
     * Gets query for [[InventoryPr]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPr() {
        return $this->hasOne(InventoryPurchaseRequest::className(), ['id' => 'inventory_pr_id']);
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
     * Gets query for [[Brand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBrand() {
        return $this->hasOne(InventoryBrand::className(), ['id' => 'brand_id']);
    }

    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency_id']);
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
}
