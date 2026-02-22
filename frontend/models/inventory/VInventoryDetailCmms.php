<?php

namespace frontend\models\inventory;

use Yii;

/**
 * This is the model class for table "v_inventory_detail_cmms".
 *
 * @property int $inventory_id
 * @property int|null $supplier_id
 * @property string|null $supplier_code
 * @property string|null $supplier_name
 * @property string|null $supplier_contact_name
 * @property string|null $supplier_contact_number
 * @property string|null $supplier_contact_email
 * @property string|null $supplier_contact_fax
 * @property string|null $supplier_agent_terms
 * @property int|null $brand_id
 * @property string|null $brand_code
 * @property string|null $brand_name
 * @property int|null $model_id
 * @property string|null $model_code
 * @property string|null $model_description
 * @property string|null $unit_type
 * @property string|null $image
 * @property int|null $minimum_qty
 * @property int|null $stock_level_sts
 * @property int|null $stock_on_hand
 * @property int|null $required_qty
 * @property int|null $reorder_qty
 * @property int|null $pending_receive_qty
 * @property int|null $active_sts
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $created_by_fullname
 * @property string|null $updated_by_fullname
 */
class VInventoryDetailCmms extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_inventory_detail_cmms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inventory_id', 'supplier_id', 'brand_id', 'model_id', 'minimum_qty', 'stock_level_sts', 'stock_on_hand', 'required_qty', 'reorder_qty', 'pending_receive_qty', 'active_sts'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['supplier_code', 'supplier_name', 'supplier_contact_name', 'supplier_contact_number', 'supplier_contact_email', 'supplier_contact_fax', 'supplier_agent_terms', 'brand_code', 'brand_name', 'model_code', 'model_description', 'image', 'created_by_fullname', 'updated_by_fullname'], 'string', 'max' => 255],
            [['unit_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inventory_id' => 'Inventory ID',
            'supplier_id' => 'Supplier ID',
            'supplier_code' => 'Supplier Code',
            'supplier_name' => 'Supplier Name',
            'supplier_contact_name' => 'Supplier Contact Name',
            'supplier_contact_number' => 'Supplier Contact Number',
            'supplier_contact_email' => 'Supplier Contact Email',
            'supplier_contact_fax' => 'Supplier Contact Fax',
            'supplier_agent_terms' => 'Supplier Agent Terms',
            'brand_id' => 'Brand ID',
            'brand_code' => 'Brand Code',
            'brand_name' => 'Brand Name',
            'model_id' => 'Model ID',
            'model_code' => 'Model Code',
            'model_description' => 'Model Description',
            'unit_type' => 'Unit Type',
            'image' => 'Image',
            'minimum_qty' => 'Minimum Qty',
            'stock_level_sts' => 'Stock Level Sts',
            'stock_on_hand' => 'Stock On Hand',
            'required_qty' => 'Required Qty',
            'reorder_qty' => 'Reorder Qty',
            'pending_receive_qty' => 'Pending Receive Qty',
            'active_sts' => 'Active Sts',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by_fullname' => 'Created By Fullname',
            'updated_by_fullname' => 'Updated By Fullname',
        ];
    }
}
