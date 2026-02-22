<?php

namespace frontend\models\office\claim;

use Yii;

/**
 * This is the model class for table "v_claim_master_details".
 *
 * @property int $claim_master_id
 * @property string|null $claim_code
 * @property int|null $claimant_id
 * @property string|null $claimant_fullname
 * @property string|null $claim_type
 * @property string|null $claim_type_name
 * @property int|null $superior_id
 * @property string|null $superior_fullname
 * @property int $claims_status
 * @property string|null $claims_status_name
 * @property string $master_created_date
 * @property string|null $master_updated_date
 * @property int|null $master_updated_by
 * @property string|null $master_updated_by_fullname
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property int|null $detail_id
 * @property string|null $ref_filename
 * @property string|null $ref_code
 * @property string|null $receipt_date
 * @property string|null $description
 * @property float|null $receipt_amount
 * @property float|null $amount_to_be_paid
 * @property string|null $detail_created_date
 * @property string|null $detail_updated_date
 * @property int|null $detail_updated_by
 * @property string|null $detail_updated_by_fullname
 */
class VClaimMasterDetails extends \yii\db\ActiveRecord {

    public $scannedFile;
    public $ref_code_tra;
    public $ref_code_med;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_claim_master_details';
    }

    public static function primaryKey() {
        return ['claim_master_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claim_master_id', 'claimant_id', 'superior_id', 'claims_status', 'master_updated_by', 'is_deleted', 'detail_id', 'detail_updated_by'], 'integer'],
            [['master_created_date', 'master_updated_date', 'receipt_date', 'detail_created_date', 'detail_updated_date', 'ref_code_tra'], 'safe'],
            [['receipt_amount', 'amount_to_be_paid'], 'number'],
            [['claim_code', 'claimant_fullname', 'superior_fullname', 'master_updated_by_fullname', 'ref_filename', 'ref_code', 'description', 'detail_updated_by_fullname'], 'string', 'max' => 255],
            [['claim_type'], 'string', 'max' => 5],
            [['claim_type_name', 'claims_status_name'], 'string', 'max' => 100],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => "png, jpg, jpeg, pdf", 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'checkExtensionByMimeType' => false],
            [['ref_filename', 'ref_code', 'receipt_date', 'description', 'receipt_amount', 'amount_to_be_paid', 'claim_type', 'scannedFile'], 'required'],
            ['receipt_amount', 'number', 'min' => 0, 'message' => 'Receipt amount must be greater than or equal to 0'],
            ['ref_code_tra', 'validateTravelCode'],
        ];
    }

    public function validateTravelCode($attribute, $params) {
        if ($this->claim_type === RefClaimType::codeTravel && empty($this->$attribute)) {
            $this->addError($attribute, 'here');
            \common\models\myTools\Mydebug::dumpFileW("HERE"); // optional debug
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'claim_master_id' => 'Claim Master ID',
            'claim_code' => 'Claim Code',
            'claimant_id' => 'Claimant ID',
            'claimant_fullname' => 'Claimant Fullname',
            'claim_type' => 'Claim Type',
            'claim_type_name' => 'Claim Type Name',
            'superior_id' => 'Superior ID',
            'superior_fullname' => 'Superior Fullname',
            'claims_status' => 'Claims Status',
            'claims_status_name' => 'Claims Status Name',
            'master_created_date' => 'Master Created Date',
            'master_updated_date' => 'Master Updated Date',
            'master_updated_by' => 'Master Updated By',
            'master_updated_by_fullname' => 'Master Updated By Fullname',
            'is_deleted' => 'Is Deleted',
            'detail_id' => 'Detail ID',
            'ref_filename' => 'Reference Filename',
            'ref_code' => 'Reference Code',
            'receipt_date' => 'Receipt Date',
            'description' => 'Description',
            'receipt_amount' => 'Receipt Amount',
            'amount_to_be_paid' => 'Amount To Be Paid',
            'detail_created_date' => 'Detail Created Date',
            'detail_updated_date' => 'Detail Updated Date',
            'detail_updated_by' => 'Detail Updated By',
            'detail_updated_by_fullname' => 'Detail Updated By Fullname',
            'scannedFile' => 'Attachment',
        ];
    }
}
