<?php

namespace frontend\models\office\preReqForm;

use Yii;

/**
 * This is the model class for table "v_prereq_form_master_detail".
 *
 * @property int $master_id
 * @property string $prf_no
 * @property string|null $date_of_material_required
 * @property float|null $total_amount
 * @property int|null $superior_id
 * @property string|null $filename
 * @property int|null $status
 * @property string|null $master_created_at
 * @property string|null $master_updated_at
 * @property int|null $item_id
 * @property string|null $department_code
 * @property string|null $department_name
 * @property string|null $supplier_name
 * @property string|null $brand_name
 * @property string|null $model_name
 * @property string|null $item_description
 * @property int|null $quantity
 * @property string|null $currency
 * @property float|null $unit_price
 * @property float|null $total_price
 * @property string|null $purpose_or_function
 * @property string|null $remark
 * @property string|null $item_created_at
 * @property string|null $item_updated_at
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property int|null $superior_user_id
 * @property string|null $superior_name
 * @property int|null $created_by_user_id
 * @property string|null $created_by_name
 * @property int|null $updated_by_user_id
 * @property string|null $updated_by_name
 * @property int|null $status_id
 * @property string|null $status_code
 * @property string|null $status_name
 * @property string|null $currency
 */
class VPrereqFormMasterDetail extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_prereq_form_master_detail';
    }

    public static function primaryKey() {
        return ['master_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['master_id', 'superior_id', 'status', 'item_id', 'quantity', 'is_deleted', 'superior_user_id', 'created_by_user_id', 'updated_by_user_id', 'status_id', 'quantity_approved'], 'integer'],
            [['prf_no'], 'required'],
            [['date_of_material_required', 'master_created_at', 'master_updated_at', 'item_created_at', 'item_updated_at'], 'safe'],
            [['total_amount', 'unit_price', 'total_price', 'unit_price_approved', 'total_price_approved'], 'number'],
            [['item_description', 'purpose_or_function', 'remark'], 'string'],
            [['prf_no', 'filename', 'department_name', 'supplier_name', 'brand_name', 'model_name', 'currency', 'superior_name', 'created_by_name', 'updated_by_name', 'status_code', 'status_name', 'currency_approved'], 'string', 'max' => 255],
            [['department_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'master_id' => 'Master ID',
            'prf_no' => 'Prf No',
            'date_of_material_required' => 'Date Of Material Required',
            'total_amount' => 'Total Amount',
            'superior_id' => 'Superior ID',
            'filename' => 'Filename',
            'status' => 'Status',
            'master_created_at' => 'Master Created At',
            'master_updated_at' => 'Master Updated At',
            'item_id' => 'Item ID',
            'department_code' => 'Department Code',
            'department_name' => 'Department Name',
            'supplier_name' => 'Supplier Name',
            'brand_name' => 'Brand Name',
            'model_name' => 'Model Name',
            'item_description' => 'Item Description',
            'quantity' => 'Quantity',
            'currency' => 'Currency',
            'unit_price' => 'Unit Price',
            'total_price' => 'Total Price',
            'purpose_or_function' => 'Purpose Or Function',
            'remark' => 'Remark',
            'item_created_at' => 'Item Created At',
            'item_updated_at' => 'Item Updated At',
            'is_deleted' => 'Is Deleted',
            'superior_user_id' => 'Superior User ID',
            'superior_name' => 'Superior Name',
            'created_by_user_id' => 'Created By User ID',
            'created_by_name' => 'Created By Name',
            'updated_by_user_id' => 'Updated By User ID',
            'updated_by_name' => 'Updated By Name',
            'status_id' => 'Status ID',
            'status_code' => 'Status Code',
            'status_name' => 'Status Name',
            'quantity_approved' => 'Quantity Approved',
            'currency_approved' => 'Currency Approved',
            'unit_price_approved' => 'Unit Price Approved',
            'total_price_approved' => 'Total Price Approved',
        ];
    }
    
    public function getItems() {
        return $this->hasMany(PrereqFormItem::className(), ['id' => 'item_id'])
                ->andWhere(['is_deleted' => 0]);
    }
}
