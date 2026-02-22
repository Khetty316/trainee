<?php

namespace frontend\models\inventory;

use Yii;

/**
 * This is the model class for table "ref_inventory_departments".
 *
 * @property int $id
 * @property string|null $table_name
 *
 * @property InventoryReorderMaster[] $inventoryReorderMasters
 */
class RefInventoryDepartments extends \yii\db\ActiveRecord {

    CONST maintenance_department = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_inventory_departments';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['table_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
        ];
    }

    /**
     * Gets query for [[InventoryReorderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryReorderMasters() {
        return $this->hasMany(InventoryReorderMaster::className(), ['inventory_department_id' => 'id']);
    }
}
