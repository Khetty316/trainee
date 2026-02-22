<?php

namespace frontend\models\inventory\cmms;

use Yii;
use frontend\models\inventory\InventoryReorderMaster;

/**
 * This is the model class for table "inventory_reorder_cmms".
 *
 * @property int $id
 * @property int|null $inventory_reorder_master_id
 * @property int|null $status
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property InventoryReorderMaster $inventoryReorderMaster
 * @property InventoryReorderItemCmms[] $inventoryReorderItemCmms
 */
class InventoryReorderCmms extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'inventory_reorder_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['inventory_reorder_master_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['inventory_reorder_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryReorderMaster::className(), 'targetAttribute' => ['inventory_reorder_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'inventory_reorder_master_id' => 'Inventory Reorder Master ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[InventoryReorderMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderMaster() {
        return $this->hasOne(InventoryReorderMaster::className(), ['id' => 'inventory_reorder_master_id']);
    }

    /**
     * Gets query for [[InventoryReorderItemCmms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderItemCmms() {
        return $this->hasMany(InventoryReorderItemCmms::className(), ['inventory_reorder_cmms_id' => 'id']);
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
