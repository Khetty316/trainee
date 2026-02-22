<?php

namespace frontend\models\projectquotation;

use Yii;

/**
 * This is the model class for table "v_project_quotation_master".
 *
 * @property int $id
 * @property string|null $quotation_no
 * @property string|null $quotation_display_no
 * @property string|null $project_code Auto-generated
 * @property string|null $project_name
 * @property string|null $company_group_code
 * @property float|null $amount
 * @property int|null $project_coordinator
 * @property string|null $status
 * @property string|null $remark
 * @property int $active
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property float|null $is_finalized
 * @property string|null $clients
 * @property string|null $project_coordinator_fullname
 */
class VProjectQuotationMaster extends \yii\db\ActiveRecord {

    public $total_amount;
    public $currency_sign;

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_project_quotation_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'project_coordinator', 'active', 'created_by', 'updated_by'], 'integer'],
            [['amount', 'is_finalized'], 'number'],
            [['remark', 'clients'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['quotation_no', 'quotation_display_no', 'project_code', 'project_name', 'project_coordinator_fullname'], 'string', 'max' => 255],
            [['company_group_code', 'status'], 'string', 'max' => 10],
            [['total_amount'], 'number'],
            [['currency_sign'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'quotation_no' => 'Quotation No',
            'quotation_display_no' => 'Quotation Display No',
            'project_code' => 'Project Code',
            'project_name' => 'Project Name',
            'company_group_code' => 'Company Group Code',
            'amount' => 'Amount',
            'project_coordinator' => 'Project Coordinator',
            'status' => 'Status',
            'remark' => 'Remark',
            'active' => 'Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'is_finalized' => 'Is Finalized',
            'clients' => 'Clients',
            'project_coordinator_fullname' => 'Project Coordinator Fullname',
            'total_amount' => 'Total Amount',
            'currency_sign' => 'Currency Sign',
        ];
    }
}
