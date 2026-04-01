<?php

namespace frontend\models\inventory;

use Yii;

/**
 * This is the model class for table "v_inventory_model".
 *
 * @property int $id
 * @property string|null $departments
 * @property string|null $type
 * @property string|null $group
 * @property string|null $description
 * @property string|null $unit_type
 * @property string|null $image
 * @property int|null $active_sts
 * @property int|null $inventory_brand_id
 * @property string|null $brand_name
 * @property string|null $brand_model
 * @property int|null $total_stock_on_hand
 * @property int|null $total_stock_reserved
 * @property int|null $total_stock_available
 * @property int|null $minimum_qty
 * @property int|null $stock_level_sts
 * @property int|null $created_by
 * @property string|null $created_by_name
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_by_name
 * @property string|null $updated_at
 */
class VInventoryModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_inventory_model';
    }

    public static function primaryKey() {
        return ['id'];
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'active_sts', 'inventory_brand_id', 'total_stock_on_hand', 'total_stock_reserved', 'total_stock_available', 'minimum_qty', 'stock_level_sts', 'created_by', 'updated_by'], 'integer'],
            [['departments'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['type', 'group', 'description', 'image', 'brand_name', 'created_by_name', 'updated_by_name'], 'string', 'max' => 255],
            [['unit_type'], 'string', 'max' => 100],
            [['brand_model'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'departments' => 'Departments',
            'type' => 'Type',
            'group' => 'Group',
            'description' => 'Description',
            'unit_type' => 'Unit Type',
            'image' => 'Image',
            'active_sts' => 'Active',
            'inventory_brand_id' => 'Inventory Brand ID',
            'brand_name' => 'Brand',
            'brand_model' => 'Brand Model',
            'total_stock_on_hand' => 'Total Stock On Hand',
            'total_stock_reserved' => 'Total Stock Reserved',
            'total_stock_available' => 'Total Stock Available',
            'minimum_qty' => 'Minimum Qty',
            'stock_level_sts' => 'Stock Level Sts',
            'created_by' => 'Created By',
            'created_by_name' => 'Created By Name',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_by_name' => 'Updated By Name',
            'updated_at' => 'Updated At',
        ];
    }
    
    public function getInventoryDetails()
    {
        return $this->hasMany(InventoryDetail::className(), ['model_id' => 'id'])
            ->andWhere(['active_sts' => 2]);
    }
}
