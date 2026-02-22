<?php

namespace frontend\models\inventory;

use Yii;
use common\models\User;

/**
 * This is the model class for table "inventory_reserve_item".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $inventory_detail_id
 * @property int|null $reserved_qty
 * @property int|null $dispatched_qty
 * @property int|null $available_qty
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $status
 *
 * @property User $user
 * @property InventoryDetail $inventoryDetail
 * @property User $createdBy
 * @property User $updatedBy
 */
class InventoryReserveItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_reserve_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'inventory_detail_id', 'reserved_qty', 'dispatched_qty', 'available_qty', 'created_by', 'updated_by', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
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
            'user_id' => 'User ID',
            'inventory_detail_id' => 'Inventory Detail ID',
            'reserved_qty' => 'Reserved Qty',
            'dispatched_qty' => 'Dispatched Qty',
            'available_qty' => 'Available Qty',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[InventoryDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetail()
    {
        return $this->hasOne(InventoryDetail::className(), ['id' => 'inventory_detail_id']);
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
}
