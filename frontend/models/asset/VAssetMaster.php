<?php

namespace frontend\models\asset;

use Yii;

/**
 * This is the model class for table "v_asset_master".
 *
 * @property int $id
 * @property string|null $asset_idx_no
 * @property int $asset_category
 * @property int $asset_sub_category
 * @property string|null $file_image
 * @property string|null $file_invoice_image
 * @property int|null $purchased_by
 * @property string $own_type
 * @property float|null $rental_fee
 * @property int $idle_sts
 * @property string $description
 * @property string $brand
 * @property string|null $model
 * @property string|null $specification
 * @property string|null $remarks
 * @property string $condition
 * @property float|null $cost
 * @property string|null $warranty_due_date
 * @property int $active_sts
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $asset_category_name
 * @property string|null $asset_sub_category_name
 * @property string|null $condition_desc
 * @property string|null $own_type_desc
 * @property string|null $purchase_by_name
 * @property string|null $created_by_name
 * @property int|null $cur_user
 * @property string|null $cur_user_fullname
 * @property int|null $pend_user
 * @property string|null $pend_user_fullname
 */
class VAssetMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_asset_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'asset_category', 'asset_sub_category', 'purchased_by', 'idle_sts', 'active_sts', 'created_by', 'cur_user', 'pend_user'], 'integer'],
            [['asset_category', 'asset_sub_category', 'own_type', 'description', 'brand', 'condition'], 'required'],
            [['rental_fee', 'cost'], 'number'],
            [['specification', 'remarks'], 'string'],
            [['warranty_due_date', 'created_at'], 'safe'],
            [['asset_idx_no'], 'string', 'max' => 30],
            [['file_image', 'file_invoice_image', 'description', 'purchase_by_name', 'created_by_name', 'cur_user_fullname', 'pend_user_fullname'], 'string', 'max' => 255],
            [['own_type'], 'string', 'max' => 8],
            [['brand', 'model', 'condition_desc', 'own_type_desc'], 'string', 'max' => 200],
            [['condition'], 'string', 'max' => 15],
            [['asset_category_name', 'asset_sub_category_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'asset_idx_no' => 'Index No.',
            'asset_category' => 'Asset Category',
            'asset_sub_category' => 'Asset Sub Category',
            'file_image' => 'Image',
            'file_invoice_image' => 'File Invoice Image',
            'purchased_by' => 'Purchased By',
            'own_type' => 'Own Type',
            'rental_fee' => 'Rental Fee',
            'idle_sts' => 'Idle?',
            'description' => 'Description',
            'brand' => 'Brand',
            'model' => 'Model',
            'specification' => 'Specification',
            'remarks' => 'Remarks',
            'condition' => 'Condition',
            'cost' => 'Cost',
            'warranty_due_date' => 'Warranty Due Date',
            'active_sts' => 'Active Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'asset_category_name' => 'Category',
            'asset_sub_category_name' => 'Sub Category',
            'condition_desc' => 'Condition',
            'own_type_desc' => 'Own Type',
            'purchase_by_name' => 'Purchase By',
            'created_by_name' => 'Created By',
            'cur_user' => 'Cur_User',
            'cur_user_fullname' => 'Current Holder',
            'pend_user' => 'Pend_User',
            'pend_user_fullname' => 'Transferring To',
        ];
    }
}
