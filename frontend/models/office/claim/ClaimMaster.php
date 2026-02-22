<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\employeeHandbook\EmployeeHandbookMaster;
use frontend\models\office\employeeHandbook\EhOutpatientMedDetail;
use frontend\models\office\employeeHandbook\EhOutpatientMedMaster;
use frontend\models\office\employeeHandbook\EhTravelAllowanceDetail;
use frontend\models\office\employeeHandbook\EhTravelAllowanceMaster;
use frontend\models\office\claim\ClaimEntitlementSummary;

/**
 * This is the model class for table "claim_master".
 *
 * @property int $id
 * @property string|null $claim_code
 * @property int|null $claimant_id
 * @property string|null $claim_type
 * @property string|null $ref_code for travel, medical and material claim
 * @property int|null $superior_id
 * @property float|null $total_amount
 * @property int $claim_status
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property int|null $status_flag 0 = pending, 1 = complete
 * @property int|null $has_payment 0 = no, 1 = yes
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @property string|null $delete_remark
 *
 * @property ClaimApprovalWorklist[] $claimApprovalWorklists
 * @property ClaimDetail[] $claimDetails
 * @property User $claimant
 * @property User $superior
 * @property RefGeneralStatus $claimStatus
 * @property User $updatedBy
 * @property RefClaimType $claimType
 * @property User $deletedBy
 */
class ClaimMaster extends \yii\db\ActiveRecord {

    public $leave_start_date;
    public $leave_end_date;
    public $leave_days;
    public $leave_status;
    public $leave_reason;
    public $leave_doc;
    public $sick_leave_code;
    public $wtf_code;
    public $prf_code;
    public $prodotmeal_code; //prod_ot_meal
    public $travel_location_code;
    public $material_total_amount;
    public $total_allowance_to_be_paid;

    const Prefix_ClaimCode = "CF";
    const runningNoLength = 5;
    const STATUS_APPROVED = 0;
    const STATUS_REJECTED = 1;
    const STATUS_PAID = 1;
    const STATUS_HOLD_PAYMENT = 0;
    const MODULE_SUPERIOR_APPROVAL = 1;
    const MODULE_FINANCE_APPROVAL = 2;
    const MODULE_FINANCE_PAYMENT = 3;
    const PERSONAL_USER_MANUAL_FILENAME = "T6B2a-Staff Claim Personal Module-03.pdf";
    const SUPERIOR_USER_MANUAL_FILENAME = "T6B2a-Staff Claim Superior Module-03.pdf";
    const FINANCE_USER_MANUAL_FILENAME = "T6B2a-Staff Claim Finance Module-03.pdf";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claimant_id', 'superior_id', 'claim_status', 'is_deleted', 'status_flag', 'has_payment', 'updated_by', 'deleted_by'], 'integer'],
            [['total_amount'], 'number'],
            [['created_at', 'updated_at', 'deleted_at', 'ref_code', 'ref_code_sts'], 'safe'],
            [['claim_code', 'ref_code', 'delete_remark'], 'string', 'max' => 255],
            [['claim_type'], 'string', 'max' => 10],
            [['claim_code'], 'unique'],
            [['claimant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['claimant_id' => 'id']],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['superior_id' => 'id']],
            [['claim_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['claim_status' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['claim_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefClaimType::className(), 'targetAttribute' => ['claim_type' => 'code']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'claim_code' => 'Claim Code',
            'claimant_id' => 'Claimant ID',
            'claim_type' => 'Claim Type',
            'ref_code' => 'Ref Code',
            'superior_id' => 'Superior ID',
            'total_amount' => 'Total Amount',
            'claim_status' => 'Claim Status',
            'is_deleted' => 'Is Deleted',
            'status_flag' => 'Status Flag',
            'has_payment' => 'Has Payment',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
//            'scannedFile' => 'Receipt',
            'leave_start_date' => 'Start Date',
            'leave_end_date' => 'End Date',
            'leave_days' => 'Days',
            'leave_status' => 'Status'
        ];
    }

    /**
     * Gets query for [[ClaimApprovalWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimApprovalWorklists() {
        return $this->hasMany(ClaimApprovalWorklist::className(), ['claim_master_id' => 'id']);
    }

    /**
     * Gets query for [[ClaimDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimDetails() {
        return $this->hasMany(ClaimDetail::className(), ['claim_master_id' => 'id']);
    }

    /**
     * Gets query for [[Claimant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimant() {
        return $this->hasOne(User::className(), ['id' => 'claimant_id']);
    }

    /**
     * Gets query for [[Superior]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperior() {
        return $this->hasOne(User::className(), ['id' => 'superior_id']);
    }

    /**
     * Gets query for [[ClaimStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimStatus() {
        return $this->hasOne(RefGeneralStatus::className(), ['id' => 'claim_status']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[ClaimType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimType() {
        return $this->hasOne(RefClaimType::className(), ['code' => 'claim_type']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->claimant_id = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

    // Main processing method - simplified
    public function processAndSave($claimMasterData, $receiptsData, $uploadedFiles, $isUpdate = false) {
        if (empty($claimMasterData) || empty($receiptsData)) {
            return 'Invalid input data';
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Process claim master
            $result = $this->processClaimMaster($claimMasterData, $receiptsData, $isUpdate);
            if ($result !== true) {
                throw new \Exception($result);
            }

            // Process claim details
            $claimDetailModel = new ClaimDetail();
            $result = $claimDetailModel->processClaimDetails($this, $claimMasterData, $receiptsData, $uploadedFiles, $isUpdate);

            if ($result !== true) {
                throw new \Exception($result);
            }

            $result = $this->updateClaimMasterStatus($this, $claimMasterData, $receiptsData);
            if ($result !== true) {
                throw new \Exception($result);
            }

            $result = $this->updateClaimFlagLeaveMaster($claimMasterData);
            if ($result !== true) {
                throw new \Exception($result);
            }
            if (empty($this->id)) {
                throw new \Exception('Claim master ID not found. Failed to update entitlement summary.');
            }

//            $result = $this->validateClaimEntitlement($this, $claimMasterData, $receiptsData);
//            if ($result !== true) {
//                throw new \Exception($result);
//            }

            $transaction->commit();
            return true;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            return 'Failed to submit the claim: ' . $exc->getMessage();
        }
    }

    private function updateClaimMasterStatus($claimMaster, $claimMasterData, $receiptsData) {
        $claimant = User::findOne($claimMasterData["claimant_id"]);
        $hasNewItems = false;
        $hasExistingItems = false;

        foreach ($receiptsData as $detail) {
            if (!isset($detail["id"])) {
                $hasNewItems = true;
            } else {
                $hasExistingItems = true;
            }
        }

        // If only existing items (no new items), keep current status
        if ($hasExistingItems && !$hasNewItems) {
            $claimMaster->claim_status = $claimMaster->claim_status;
        }
        // If has new items (with or without existing items), restart approval process
        else if ($hasNewItems) {
//            if ($claimant->skip_claim_authorize == 1) {
            $claimMaster->claim_status = RefGeneralStatus::STATUS_GetFinanceApproval;
//            } else {
//                $claimMaster->claim_status = RefGeneralStatus::STATUS_GetFinanceApproval;
//            }
        }

        $claimMaster->status_flag = 0;
        return $claimMaster->save() ? true : 'Failed to update claim and flag status';
    }

    private function updateClaimFlagLeaveMaster($claimMasterData) {
        $leaveMaster = null;

        switch ($claimMasterData["claim_type"]) {
            case RefClaimType::codeMaterial:
                $leaveMaster = \frontend\models\office\preReqForm\PrereqFormMaster::findOne(['prf_no' => $claimMasterData['prf_code']]);
                break;

            case RefClaimType::codeMedical:
                if (!empty($claimMasterData['sick_leave_code'])) {
                    $leaveMaster = LeaveMaster::findOne(['leave_code' => $claimMasterData['sick_leave_code']]);
                }
                break;

            case RefClaimType::codeTravel:
                if (!empty($claimMasterData['wtf_code'])) {
                    $leaveMaster = LeaveMaster::findOne(['leave_code' => $claimMasterData['wtf_code']]);
                }
                break;

            case RefClaimType::codeRepair:
                $leaveMaster = \frontend\models\office\preReqForm\PrereqFormMaster::findOne(['prf_no' => $claimMasterData['prf_code']]);
                break;

            case RefClaimType::codeProdOTMeal:
                $master = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::findOne([
                    'ref_code' => $claimMasterData['prodotmeal_code'] ?? null
                ]);
                if ($master) {
                    $master->status = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::STATUS_CLAIM_SUBMITTED;
                    return $master->save() ?: 'Failed to update claim flag';
                }
                return true; // No matching record found — skip silently

            default:
                return true; // No processing needed for other claim types
        }

        // Check if we found a record to update
        if ($leaveMaster === null) {
            return true; // No record to update, consider it successful
        }

        // Update the claim flag
        $leaveMaster->claim_flag = 1;
        return $leaveMaster->save() ? true : 'Failed to update claim flag (Leave Master)';
    }

    // Handle claim master data processing
    private function processClaimMaster($claimMasterData, $receiptsData, $isUpdate) {
        try {
            if (!$isUpdate) {
                $this->claim_code = $this->generateClaimCode();
            }

            $this->claimant_id = $claimMasterData["claimant_id"];
            $this->claim_type = $claimMasterData["claim_type"];
            $this->superior_id = Yii::$app->user->identity->superior_id ?? null;
//            $this->ref_code_sts = $claimMasterData["ref_code_sts"];

            switch ($claimMasterData["claim_type"]) {
                case RefClaimType::codeMaterial:
                    $this->ref_code = $claimMasterData["prf_code"] ?? null;
                    break;
                case RefClaimType::codeMedical:
//                    $this->ref_code = ($claimMasterData["ref_code_sts"] === '1' ? ($claimMasterData['sick_leave_code'] ?? null) : null);
                    $this->ref_code = $claimMasterData["sick_leave_code"] ?? null;
                    break;
                case RefClaimType::codeTravel:
                    $this->ref_code = $claimMasterData["wtf_code"] ?? null;
                    break;
                case RefClaimType::codeRepair:
                    $this->ref_code = $claimMasterData["prf_code"] ?? null;
                    break;
                case RefClaimType::codeProdOTMeal:
                    $this->ref_code = $claimMasterData["prodotmeal_code"] ?? null;
                    break;
                default:
                    $this->ref_code = null;
            }

            $this->ref_code_sts = (($this->ref_code === null || $this->ref_code === '') ? 0 : 1);

            // Validate claim by type
            $validationResult = $this->validateClaimByType($claimMasterData, $receiptsData);
            if ($validationResult !== true) {
                return $validationResult;
            }

            // Calculate total amount
            $this->calculateTotalAmount($claimMasterData);

            return $this->save() ? true : 'Failed to save claim master: ' . implode(', ', $this->getFirstErrors());
        } catch (\Exception $exc) {
            return 'Error processing claim master: ' . $exc->getMessage();
        }
    }

    // Generate claim code
    private function generateClaimCode() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialClaimCode = self::Prefix_ClaimCode;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $claimCode = $initialClaimCode . $runningNo . "-" . $currentMonth . $currentYearShort; // Generate the claim code

        return $claimCode;
    }

    // Validate claim based on type
    private function validateClaimByType($claimMasterData, $receiptsData) {
        switch ($claimMasterData["claim_type"]) {
            case RefClaimType::codeMaterial:
                return $this->validatePRFClaim($claimMasterData, $receiptsData);

            case RefClaimType::codeMedical:
                return $this->validateMedicalClaim($receiptsData);

            case RefClaimType::codeTravel:
                return $this->validateTravelClaim($claimMasterData);

            case RefClaimType::codeRepair:
                return $this->validatePRFClaim($claimMasterData, $receiptsData);
                
            case RefClaimType::codeProdOTMeal:
                return $this->validateProdotmealClaim($claimMasterData, $receiptsData);

            default:
                return true;
        }
    }

    // production ot meal claim validation
    private function validateProdotmealClaim($claimMasterData, $receiptsData) {
        if (empty($claimMasterData["prodotmeal_code"])) {
            return "Production Overtime Meal Record Form Code is required for this claim";
        }

        $prodotmealMaster = \frontend\models\office\prodOtMealRecord\ProdOtMealRecordMaster::findOne(['ref_code' => $claimMasterData["prodotmeal_code"]]);
        if (!$prodotmealMaster) {
            return "Production Overtime Meal Record form not found for code: " . $claimMasterData["prodotmeal_code"];
        }

        $limitAmount = round((float) $prodotmealMaster->total_amount, 2);
        if ($limitAmount <= 0) {
            return "Invalid limit amount in production overtime meal record form";
        }

        $totalSubmitted = 0;
        foreach ($receiptsData as $receipt) {
            // Add validation for receipt data
            if (!isset($receipt['amount_to_be_paid'])) {
                continue; // Skip invalid receipt entries
            }

            $receiptAmount = round((float) $receipt['amount_to_be_paid'], 2);
            $totalSubmitted += $receiptAmount;
        }

        if ($totalSubmitted > $limitAmount) {
            return "Total amount RM" . number_format($totalSubmitted, 2) .
                    " exceeds limit of RM" . number_format($limitAmount, 2);
        }

        return true; // Valid
    }
    
    private function validateClaimEntitlement($claimMaster, $claimMasterData, $receiptsData) {
        $summary = new ClaimEntitlementSummary();

        switch ($claimMasterData['claim_type']) {
            case RefClaimType::codePetrol:
            case RefClaimType::codeTelephone:
            case RefClaimType::codeRepair:
                return $summary->updateClaimEntitlementSummary($claimMaster, $claimMasterData, $receiptsData);

            default:
                return true;
        }
    }

    // Material/Repair and Maintenance claim validation
    private function validatePRFClaim($claimMasterData, $receiptsData) {
        if (empty($claimMasterData["prf_code"])) {
            return "Pre-Requisition Form Code is required for this claim";
        }

        $prereqMaster = \frontend\models\office\preReqForm\PrereqFormMaster::findOne(['prf_no' => $claimMasterData["prf_code"]]);
        if (!$prereqMaster) {
            return "Pre-requisition form not found for code: " . $claimMasterData["prf_code"];
        }

//        $limitAmount = round((float) $prereqMaster->total_amount, 2);
        // Check if limit is valid
//        if ($limitAmount <= 0) {
//            return "Invalid limit amount in pre-requisition form";
//        }
//
//        $totalSubmitted = 0;
//        foreach ($receiptsData as $receipt) {
//            // Add validation for receipt data
//            if (!isset($receipt['amount_to_be_paid'])) {
//                continue; // Skip invalid receipt entries
//            }
//
//            $receiptAmount = round((float) $receipt['amount_to_be_paid'], 2);
//            $totalSubmitted += $receiptAmount;
//        }
//
//        if ($totalSubmitted > $limitAmount) {
//            return "Total amount RM" . number_format($totalSubmitted, 2) .
//                    " exceeds PRF limit of RM" . number_format($limitAmount, 2);
//        }

        return true; // Valid
    }

    // Medical claim validation
    private function validateMedicalClaim($receiptsData) {
        $medicalConfig = $this->getMedicalConfiguration();
        if (!is_array($medicalConfig)) {
            return $medicalConfig; // Error message
        }

        $limitAmount = round((float) $medicalConfig['limit_amount'], 2);

        foreach ($receiptsData as $receipt) {
            $receiptAmount = round((float) $receipt['amount_to_be_paid'], 2);
            if ($receiptAmount > $limitAmount) {
                return "Receipt amount " . number_format($receiptAmount, 2) .
                        " exceeds limit of " . number_format($limitAmount, 2);
            }
        }

        return true;
    }

    // Travel claim validation
    private function validateTravelClaim($claimMasterData) {
        $this->ref_code = $claimMasterData["wtf_code"];

        $leaveRecord = LeaveMaster::findOne(['leave_code' => $this->ref_code]);
        $claimant = User::findOne($claimMasterData["claimant_id"]);

        if (!$leaveRecord || !$claimant) {
            return 'Invalid leave record or claimant';
        }

        return $this->validateTravelAllowance($claimMasterData["total_allowance_to_be_paid"], $leaveRecord, $claimant, $claimMasterData["travel_location_code"]);
    }

    // Travel allowance validation
    private function validateTravelAllowance($claimedTotal, $leaveRecord, $claimant, $locationCode) {
        $travelConfig = $this->getTravelConfiguration($claimant->grade, $locationCode);
        if (!is_array($travelConfig)) {
            return $travelConfig; // Error message
        }

        $allowanceAmount = round((float) $travelConfig['amount_per_day'], 2);
        $calculatedTotal = round($leaveRecord->total_days * $allowanceAmount, 2);
        $claimedAmount = round((float) $claimedTotal, 2);

        if (abs($claimedAmount - $calculatedTotal) > 0.01) {
            return "Travel allowance mismatch. Expected: " . number_format($calculatedTotal, 2) .
                    ", Claimed: " . number_format($claimedAmount, 2);
        }

        return true;
    }

    // Get medical configuration
    private function getMedicalConfiguration() {
        $activeEH = EmployeeHandbookMaster::findOne(['is_active' => 1]);
        if (!$activeEH) {
            return 'No active employee handbook found';
        }

        $medicalMaster = EhOutpatientMedMaster::findOne(['eh_master_id' => $activeEH->id]);
        if (!$medicalMaster) {
            return 'No medical master configuration found';
        }

        $medicalLimit = EhOutpatientMedDetail::findOne([
            'eh_master_id' => $activeEH->id,
            'eh_outpatient_med_master_id' => $medicalMaster->id
        ]);
        if (!$medicalLimit) {
            return 'No medical limit configuration found';
        }

        return ['limit_amount' => $medicalLimit->amount_per_receipt];
    }

    // Get travel configuration
    private function getTravelConfiguration($grade, $locationCode) {
        $employeeHandbook = EmployeeHandbookMaster::findOne(['is_active' => 1]);
        if (!$employeeHandbook) {
            return 'No active employee handbook found';
        }

        $travelMaster = EhTravelAllowanceMaster::findOne(['eh_master_id' => $employeeHandbook->id]);
        if (!$travelMaster) {
            return 'No travel allowance configuration found';
        }

        $travelDetail = EhTravelAllowanceDetail::findOne([
            'eh_travel_allowance_master_id' => $travelMaster->id,
            'eh_master_id' => $employeeHandbook->id,
            'grade' => $grade,
            'location_type' => $locationCode
        ]);

        if (!$travelDetail) {
            return 'No travel allowance found for your grade and location';
        }

        if ($travelDetail->amount_per_day <= 0) {
            return 'Invalid allowance amount configuration';
        }

        return ['amount_per_day' => $travelDetail->amount_per_day];
    }

    // Calculate total amount
    private function calculateTotalAmount($claimMasterData) {
        $allowanceAmount = isset($claimMasterData["total_allowance_to_be_paid"]) ? round((float) $claimMasterData["total_allowance_to_be_paid"], 2) : 0.00;
        $totalAmount = round((float) $claimMasterData["total_amount"], 2);
        $this->total_amount = round($totalAmount + $allowanceAmount, 2);
    }
}
