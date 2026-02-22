<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_tool_list".
 *
 * @property int $id
 * @property string|null $description
 * @property int|null $inventory_id
 *
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 * @property VInventoryModel $inventory
 */
class CmmsToolList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_tool_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_id'], 'integer'],
            [['description'], 'string', 'max' => 255],
            [['inventory_id'], 'exist', 'skipOnError' => true, 'targetClass' => VInventoryModel::className(), 'targetAttribute' => ['inventory_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'description' => 'Description',
            'inventory_id' => 'Inventory ID',
        ];
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['tool_id' => 'id']);
    }

    /**
     * Gets query for [[Inventory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventory()
    {
        return $this->hasOne(VInventoryModel::className(), ['id' => 'inventory_id']);
    }
}
