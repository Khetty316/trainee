<?php

namespace frontend\models\inventory;

use Yii;
use frontend\models\office\preReqForm\PrereqFormItem;
use common\models\User;
use frontend\models\inventory\InventoryReorderMaster;
use frontend\models\inventory\InventoryDetail;

/**
 * This is the model class for table "inventory_reorder_item".
 *
 * @property int $id
 * @property int|null $inventory_reorder_master_id
 * @property int|null $inventory_detail_id
 * @property int|null $prereq_form_item_id
 * @property int|null $order_qty
 * @property int|null $received_qty
 * @property int|null $remaining_qty
 * @property int|null $receipt_status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property PrereqFormItem $prereqFormItem
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryReorderMaster $inventoryReorderMaster
 * @property InventoryDetail $inventoryDetail
 * @property InventoryReorderItemWorklist[] $inventoryReorderItemWorklists
 */
class InventoryReorderItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_reorder_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_reorder_master_id', 'inventory_detail_id', 'prereq_form_item_id', 'order_qty', 'received_qty', 'remaining_qty', 'receipt_status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['prereq_form_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormItem::className(), 'targetAttribute' => ['prereq_form_item_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['inventory_reorder_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryReorderMaster::className(), 'targetAttribute' => ['inventory_reorder_master_id' => 'id']],
            [['inventory_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetail::className(), 'targetAttribute' => ['inventory_detail_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_reorder_master_id' => 'Inventory Reorder Master ID',
            'inventory_detail_id' => 'Inventory Detail ID',
            'prereq_form_item_id' => 'Prereq Form Item ID',
            'order_qty' => 'Order Qty',
            'received_qty' => 'Received Qty',
            'remaining_qty' => 'Remaining Qty',
            'receipt_status' => 'Receipt Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[PrereqFormItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormItem()
    {
        return $this->hasOne(PrereqFormItem::className(), ['id' => 'prereq_form_item_id']);
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

    /**
     * Gets query for [[InventoryReorderMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderMaster()
    {
        return $this->hasOne(InventoryReorderMaster::className(), ['id' => 'inventory_reorder_master_id']);
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
     * Gets query for [[InventoryReorderItemWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItemWorklists()
    {
        return $this->hasMany(InventoryReorderItemWorklist::className(), ['inventory_reorder_item_id' => 'id']);
    }
    
    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }else{
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }
}
