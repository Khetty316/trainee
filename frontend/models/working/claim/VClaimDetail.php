<?php

namespace frontend\models\working\claim;

use Yii;

/**
 * This is the model class for table "v_claim_detail".
 *
 * @property int $claims_master_id
 * @property string $claims_id
 * @property string|null $claimant
 * @property int $claims_status
 * @property string|null $claims_status_name
 * @property int|null $claims_mi_id
 * @property string|null $mi_idx_no
 * @property string $invoice_date
 * @property string|null $company_name
 * @property string|null $receipt_no
 * @property string|null $detail
 * @property int $claims_detail_id
 * @property float $amount
 * @property int $receipt_lost
 * @property string $claim_type
 * @property string $claim_type_name
 */
class VClaimDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'v_claim_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['claims_master_id', 'claims_status', 'claims_mi_id', 'claims_detail_id', 'receipt_lost'], 'integer'],
            [['claims_id', 'invoice_date', 'amount', 'claim_type'], 'required'],
            [['invoice_date'], 'safe'],
            [['amount'], 'number'],
            [['claims_id', 'mi_idx_no'], 'string', 'max' => 20],
            [['claimant', 'company_name', 'receipt_no', 'detail','claim_type_name'], 'string', 'max' => 255],
            [['claims_status_name'], 'string', 'max' => 100],
            [['claim_type'], 'string', 'max' => 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'claims_master_id' => 'Claims Master ID',
            'claims_id' => 'Claims ID',
            'claimant' => 'Claimant',
            'claims_status' => 'Claims Status',
            'claims_status_name' => 'Claims Status Name',
            'claims_mi_id' => 'Claims Mi ID',
            'mi_idx_no' => 'Mi Idx No',
            'invoice_date' => 'Invoice Date',
            'company_name' => 'Company Name',
            'receipt_no' => 'Receipt No',
            'detail' => 'Detail',
            'claims_detail_id' => 'Claims Detail ID',
            'amount' => 'Amount',
            'receipt_lost' => 'Receipt Lost',
            'claim_type' => 'Claim Type',
            'claim_type_name'=>'Claim Type Name'
        ];
    }
}
