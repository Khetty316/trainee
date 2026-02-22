<?php

namespace frontend\models\working\po;

use Yii;

/**
 * This is the model class for table "v_purchase_order_master".
 *
 * @property int $po_id
 * @property string $po_number
 * @property string|null $po_date
 * @property string $project_code
 * @property string|null $po_material_desc
 * @property float|null $amount
 * @property string|null $po_lead_time
 * @property string|null $po_etd
 * @property string|null $po_transporter
 * @property int|null $po_pic
 * @property int|null $po_address
 * @property int $po_receive_status
 * @property string|null $po_upload_file
 * @property string|null $remarks
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $update_at
 * @property int|null $updated_by
 * @property string|null $address_name
 * @property string|null $address_description
 * @property int|null $area_id
 * @property string|null $area_name
 * @property string|null $created_by_fullname
 * @property string|null $po_pic_fullname
 * @property string|null $project_name
 * @property string|null $project_description
 * @property string|null $currency_name
 * @property string|null $currency_code
 * @property string|null $currency_sign
 */
class VPurchaseOrderMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_purchase_order_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['po_id', 'po_pic', 'po_address', 'po_receive_status', 'created_by', 'updated_by', 'area_id','quotation_master_id'], 'integer'],
            [['po_number', 'project_code'], 'required'],
            [['po_date', 'created_at', 'update_at'], 'safe'],
            [['amount'], 'number'],
            [['remarks'], 'string'],
            [['po_number', 'po_lead_time', 'po_etd'], 'string', 'max' => 100],
            [['project_code'], 'string', 'max' => 20],
            [['po_material_desc', 'po_transporter', 'po_upload_file', 'address_name', 'address_description', 'area_name', 'created_by_fullname', 'po_pic_fullname', 'project_name', 'project_description', 'currency_name'], 'string', 'max' => 255],
            [['currency_code', 'currency_sign'], 'string', 'max' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'po_id' => 'P.O. ID',
            'po_number' => 'P.O. Number',
            'po_date' => 'P.O. Date',
            'project_code' => 'Project Code',
            'po_material_desc' => 'P.O. Material Desc',
            'amount' => 'Amount',
            'po_lead_time' => 'P.O. Lead Time',
            'po_etd' => 'P.O. Etd',
            'po_transporter' => 'P.O. Transporter',
            'po_pic' => 'Person In Charge',
            'po_address' => 'P.O. Address',
            'po_receive_status' => 'Received?',
            'po_upload_file' => 'P.O. Reference',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'update_at' => 'Update At',
            'updated_by' => 'Updated By',
            'address_name' => 'Address',
            'address_description' => 'Address Description',
            'area_id' => 'Area ID',
            'area_name' => 'Area Name',
            'created_by_fullname' => 'Created By',
            'po_pic_fullname' => 'Person In Charge',
            'project_name' => 'Project Name',
            'project_description' => 'Project Description',
            'currency_name' => 'Currency Name',
            'currency_code' => 'Currency Code',
            'currency_sign' => 'Currency Sign',
        ];
    }

}
