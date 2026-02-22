<?php

namespace frontend\models\inventory\cmms;

use Yii;
use frontend\models\office\preReqForm\PrereqFormItem;
use frontend\models\inventory\cmms\InventoryDetailCmms;
use frontend\models\inventory\cmms\InventoryReorderCmms;
use common\models\User;

/**
 * This is the model class for table "inventory_reorder_item_cmms".
 *
 * @property int $id
 * @property int|null $inventory_reorder_cmms_id
 * @property int|null $inventory_detail_cmms_id
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
 * @property InventoryDetailCmms $inventoryDetailCmms
 * @property PrereqFormItem $prereqFormItem
 * @property InventoryReorderCmms $inventoryReorderCmms
 * @property User $createdBy
 * @property User $updatedBy
 * @property InventoryReorderItemWorklistCmms[] $inventoryReorderItemWorklistCmms
 */
class InventoryReorderItemCmms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_reorder_item_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_reorder_cmms_id', 'inventory_detail_cmms_id', 'prereq_form_item_id', 'order_qty', 'received_qty', 'remaining_qty', 'receipt_status'], 'integer'],
            [['inventory_detail_cmms_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryDetailCmms::className(), 'targetAttribute' => ['inventory_detail_cmms_id' => 'id']],
            [['prereq_form_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormItem::className(), 'targetAttribute' => ['prereq_form_item_id' => 'id']],
            [['inventory_reorder_cmms_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryReorderCmms::className(), 'targetAttribute' => ['inventory_reorder_cmms_id' => 'id']],
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
            'inventory_reorder_cmms_id' => 'Inventory Reorder Cmms ID',
            'inventory_detail_cmms_id' => 'Inventory Detail Cmms ID',
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
     * Gets query for [[InventoryDetailCmms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryDetailCmms()
    {
        return $this->hasOne(InventoryDetailCmms::className(), ['id' => 'inventory_detail_cmms_id']);
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
     * Gets query for [[InventoryReorderCmms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderCmms()
    {
        return $this->hasOne(InventoryReorderCmms::className(), ['id' => 'inventory_reorder_cmms_id']);
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
     * Gets query for [[InventoryReorderItemWorklistCmms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItemWorklistCmms()
    {
        return $this->hasMany(InventoryReorderItemWorklistCmms::className(), ['inventory_reorder_item_cmms_id' => 'id']);
    }
    
    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } 

        return parent::beforeSave($insert);
    }
}
