<?php

namespace frontend\models\working\project;

use Yii;

/**
 * This is the model class for table "v_claim_costing".
 *
 * @property int $claims_detail_sub_id
 * @property int $claims_detail_id
 * @property int $claims_master_id
 * @property string $claim_type
 * @property string $claim_name
 * @property int $claims_status
 * @property string|null $status_name
 * @property int|null $claimant_id
 * @property string|null $fullname
 * @property string $claims_id
 * @property string $date1
 * @property string|null $date2
 * @property float $amount
 * @property string|null $project_account
 * @property int $project_id
 * @property string $detail
 */
class VClaimCosting extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_claim_costing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claims_detail_sub_id', 'claims_detail_id', 'claims_master_id', 'claims_status', 'claimant_id', 'project_id'], 'integer'],
            [['claim_type', 'claim_name', 'claims_id', 'date1', 'detail'], 'required'],
            [['date1', 'date2'], 'safe'],
            [['amount'], 'number'],
            [['claim_type'], 'string', 'max' => 5],
            [['claim_name', 'status_name'], 'string', 'max' => 100],
            [['fullname', 'detail'], 'string', 'max' => 255],
            [['claims_id'], 'string', 'max' => 20],
            [['project_account'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'claims_detail_sub_id' => 'Claims Detail Sub ID',
            'claims_detail_id' => 'Claims Detail ID',
            'claims_master_id' => 'Claims Master ID',
            'claim_type' => 'Claim Type',
            'claim_name' => 'Claim Name',
            'claims_status' => 'Claims Status',
            'status_name' => 'Status Name',
            'claimant_id' => 'Claimant ID',
            'fullname' => 'Fullname',
            'claims_id' => 'Claims ID',
            'date1' => 'Date1',
            'date2' => 'Date2',
            'amount' => 'Amount',
            'project_account' => 'Project Account',
            'project_id' => 'Project ID',
            'detail' => 'Detail',
        ];
    }

}
