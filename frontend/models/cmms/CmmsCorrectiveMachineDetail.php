<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_corrective_machine_detail".
 *
 * @property int $id
 * @property int|null $inventory_machine_id
 * @property int|null $inventory_machine_category_id
 * @property string|null $manual_file
 * @property resource|null $circuit_diagram
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property RefCorrectiveInventoryMachineCategory $inventoryMachineCategory
 * @property CmmsCorrectiveMachinePart[] $cmmsCorrectiveMachineParts
 * @property CmmsCorrectiveWorkOrderMaster[] $cmmsCorrectiveWorkOrderMasters
 */
class CmmsCorrectiveMachineDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_corrective_machine_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'inventory_machine_id', 'inventory_machine_category_id', 'created_by', 'updated_by'], 'integer'],
            [['circuit_diagram'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['manual_file'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['inventory_machine_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCorrectiveInventoryMachineCategory::className(), 'targetAttribute' => ['inventory_machine_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_machine_id' => 'Inventory Machine ID',
            'inventory_machine_category_id' => 'Inventory Machine Category ID',
            'manual_file' => 'Manual File',
            'circuit_diagram' => 'Circuit Diagram',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InventoryMachineCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryMachineCategory()
    {
        return $this->hasOne(RefCorrectiveInventoryMachineCategory::className(), ['id' => 'inventory_machine_category_id']);
    }

    /**
     * Gets query for [[CmmsCorrectiveMachineParts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveMachineParts()
    {
        return $this->hasMany(CmmsCorrectiveMachinePart::className(), ['cmms_machine_detail_id' => 'id']);
    }

    /**
     * Gets query for [[CmmsCorrectiveWorkOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsCorrectiveWorkOrderMasters()
    {
        return $this->hasMany(CmmsCorrectiveWorkOrderMaster::className(), ['cmms_machine_detail_id' => 'id']);
    }
}
