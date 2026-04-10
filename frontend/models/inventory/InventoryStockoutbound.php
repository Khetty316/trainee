<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;
use frontend\models\inventory\InventoryDetail;

/**
 * This is the model class for table "inventory_stockoutbound".
 *
 * @property int $id
 * @property int|null $inventory_detail_id
 * @property string|null $reference_type
 * @property int|null $reference_id
 * @property int|null $qty
 * @property int|null $dispatched_qty
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $reserve_item_id
 *
 * @property InventoryDetail $inventoryDetail
 * @property User $createdBy
 * @property InventoryReserveItem $reserveItem
 * @property User $updatedBy
 */
class InventoryStockoutbound extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_stockoutbound';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_detail_id', 'reference_id', 'qty', 'dispatched_qty', 'created_by', 'updated_by', 'reserve_item_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['reference_type'], 'string', 'max' => 100],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['reserve_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryReserveItem::className(), 'targetAttribute' => ['reserve_item_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_detail_id' => 'Inventory Detail ID',
            'reference_type' => 'Reference Type',
            'reference_id' => 'Reference ID',
            'qty' => 'Qty',
            'dispatched_qty' => 'Dispatched Qty',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'reserve_item_id' => 'Reserve Item ID',
        ];
    }

    /**
     * Gets query for [[InventoryDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetail() {
        return $this->hasOne(InventoryDetail::className(), ['id' => 'inventory_detail_id']);
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
     * Gets query for [[ReserveItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReserveItem() {
        return $this->hasOne(InventoryReserveItem::className(), ['id' => 'reserve_item_id']);
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
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }
}
