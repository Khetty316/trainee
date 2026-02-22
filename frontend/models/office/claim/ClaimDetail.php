<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;

/**
 * This is the model class for table "claim_detail".
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string|null $travel_location_code
 * @property int $claimant_id
 * @property int $claim_master_id
 * @property string|null $receipt_file
 * @property string $receipt_date
 * @property string $detail
 * @property float $receipt_amount
 * @property float $amount_to_be_paid
 * @property int|null $claim_status 0 = approved, 1 = rejected
 * @property int|null $is_paid 0 = no, 1 = yes
 * @property int|null $is_deleted 0 = no, 1 = yes
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @property string|null $payment_proof_file
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ClaimApprovalWorklist[] $claimApprovalWorklists
 * @property User $claimant
 * @property ClaimMaster $claimMaster
 * @property User $updatedBy
 * @property ClaimDetail $parent
 * @property ClaimDetail[] $claimDetails
 * @property User $deletedBy
 */
class ClaimDetail extends \yii\db\ActiveRecord {

    public $scannedFile;
    public $approval_remark;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['parent_id', 'claimant_id', 'claim_master_id', 'claim_status', 'is_paid', 'is_deleted', 'deleted_by', 'updated_by'], 'integer'],
            [['claimant_id', 'claim_master_id', 'receipt_date', 'detail', 'receipt_amount', 'amount_to_be_paid'], 'required'],
            [['receipt_date', 'deleted_at', 'created_at', 'updated_at'], 'safe'],
            [['receipt_amount', 'amount_to_be_paid'], 'number'],
            [['travel_location_code'], 'string', 'max' => 100],
            [['receipt_file', 'detail', 'payment_proof_file'], 'string', 'max' => 255],
            [['claimant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['claimant_id' => 'id']],
            [['claim_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimMaster::className(), 'targetAttribute' => ['claim_master_id' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimDetail::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => "png, jpg, jpeg, pdf", 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'parent_id' => 'Parent ID',
            'travel_location_code' => 'Travel Location Code',
            'claimant_id' => 'Claimant ID',
            'claim_master_id' => 'Claim Master ID',
            'receipt_file' => 'Receipt File',
            'receipt_date' => 'Receipt Date',
            'detail' => 'Detail',
            'receipt_amount' => 'Receipt Amount',
            'amount_to_be_paid' => 'Amount To Be Paid',
            'claim_status' => 'Claim Status',
            'is_paid' => 'Is Paid',
            'is_deleted' => 'Is Deleted',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
            'payment_proof_file' => 'Payment Proof File',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'scannedFile' => 'Payment Proof'
        ];
    }

    /**
     * Gets query for [[ClaimApprovalWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimApprovalWorklists() {
        return $this->hasMany(ClaimApprovalWorklist::className(), ['claim_detail_id' => 'id']);
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
     * Gets query for [[ClaimMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimMaster() {
        return $this->hasOne(ClaimMaster::className(), ['id' => 'claim_master_id']);
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
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent() {
        return $this->hasOne(ClaimDetail::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[ClaimDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimDetails() {
        return $this->hasMany(ClaimDetail::className(), ['parent_id' => 'id']);
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

    // Main method to process all claim details
    public function processClaimDetails($claimMaster, $claimMasterData, $receiptsData, $uploadedFiles, $isUpdate) {
        try {
            if ($isUpdate) {
                return $this->updateClaimDetails($claimMaster, $claimMasterData, $receiptsData, $uploadedFiles);
            } else {
                return $this->createClaimDetails($claimMaster, $claimMasterData, $receiptsData, $uploadedFiles);
            }
        } catch (\Exception $exc) {
            return $exc->getMessage();
        }
    }

    // Create new claim details
    private function createClaimDetails($claimMaster, $claimMasterData, $receiptsData, $uploadedFiles) {
        $travelAllowanceId = null;

        // Handle travel allowance if it's a travel claim
        if ($this->isTravelClaim($claimMaster, $claimMasterData)) {
            $travelAllowanceId = $this->createTravelAllowance($claimMaster, $claimMasterData);
            if (!is_numeric($travelAllowanceId)) {
                return $travelAllowanceId; // Error message
            }
        }

        // Create receipt details
        return $this->createReceiptDetails($claimMaster, $receiptsData, $uploadedFiles, $travelAllowanceId);
    }

    // Update existing claim details
    private function updateClaimDetails($claimMaster, $claimMasterData, $receiptsData, $uploadedFiles) {
        $travelAllowanceId = null;

        // Handle travel allowance if it's a travel claim
        if ($this->isTravelClaim($claimMaster, $claimMasterData)) {
            $travelAllowanceId = $this->updateOrCreateTravelAllowance($claimMaster, $claimMasterData);
            if (!is_numeric($travelAllowanceId)) {
                return $travelAllowanceId; // Error message
            }
        }

        // Update receipt details
        return $this->updateReceiptDetails($claimMaster, $receiptsData, $uploadedFiles, $travelAllowanceId);
    }

    // Check if it's a travel claim
    private function isTravelClaim($claimMaster, $claimMasterData) {
        return $claimMaster->claim_type === RefClaimType::codeTravel &&
                !empty($claimMasterData["travel_location_code"]) &&
                !empty($claimMasterData["total_allowance_to_be_paid"]);
    }

    // Create travel allowance record
    private function createTravelAllowance($claimMaster, $claimMasterData) {
        try {
            $travelAllowance = new ClaimDetail();
            $travelAllowance->travel_location_code = $claimMasterData["travel_location_code"];
            $travelAllowance->claimant_id = Yii::$app->user->identity->id;
            $travelAllowance->claim_master_id = $claimMaster->id;
            $travelAllowance->detail = 'Travel Allowance';
            $travelAllowance->receipt_amount = round((float) $claimMasterData["total_allowance_to_be_paid"], 2);
            $travelAllowance->amount_to_be_paid = round((float) $claimMasterData["total_allowance_to_be_paid"], 2);
            $travelAllowance->receipt_date = date('Y-m-d');

            if (!$travelAllowance->save()) {
                return 'Failed to save travel allowance: ' . implode(', ', $travelAllowance->getFirstErrors());
            }

            return $travelAllowance->id;
        } catch (\Exception $exc) {
            return 'Error saving travel allowance: ' . $exc->getMessage();
        }
    }

    // Update or create travel allowance
    private function updateOrCreateTravelAllowance($claimMaster, $claimMasterData) {
        $travelAllowance = ClaimDetail::findOne([
            'claim_master_id' => $claimMaster->id,
            'detail' => 'Travel Allowance'
        ]);

        if ($travelAllowance) {
            return $this->updateTravelAllowance($travelAllowance, $claimMasterData);
        } else {
            return $this->createTravelAllowance($claimMaster, $claimMasterData);
        }
    }

    // Update existing travel allowance
    private function updateTravelAllowance($travelAllowance, $claimMasterData) {
        try {
            $travelAllowance->travel_location_code = $claimMasterData["travel_location_code"];
            $travelAllowance->receipt_amount = round((float) $claimMasterData["total_allowance_to_be_paid"], 2);
            $travelAllowance->amount_to_be_paid = round((float) $claimMasterData["total_allowance_to_be_paid"], 2);
            $travelAllowance->receipt_date = date('Y-m-d');

            if (!$travelAllowance->save()) {
                return 'Failed to update travel allowance: ' . implode(', ', $travelAllowance->getFirstErrors());
            }

            return $travelAllowance->id;
        } catch (\Exception $exc) {
            return 'Error updating travel allowance: ' . $exc->getMessage();
        }
    }

    // Create receipt details
    private function createReceiptDetails($claimMaster, $receiptsData, $uploadedFiles, $parentId = null) {
        foreach ($receiptsData as $index => $receiptData) {
            $result = $this->createSingleReceiptDetail($claimMaster, $receiptData, $uploadedFiles[$index] ?? null, $index, $parentId);
            if ($result !== true) {
                return $result;
            }
        }
        return true;
    }

    // Create single receipt detail
    private function createSingleReceiptDetail($claimMaster, $receiptData, $fileData, $index, $parentId = null) {
        try {
            $claimDetail = new ClaimDetail();
            $claimDetail->claimant_id = Yii::$app->user->identity->id;
            $claimDetail->claim_master_id = $claimMaster->id;
            $claimDetail->receipt_date = $receiptData['receipt_date'];
            $claimDetail->detail = $receiptData['detail'];
            $claimDetail->receipt_amount = round((float) $receiptData['receipt_amount'], 2);
            $claimDetail->amount_to_be_paid = round((float) $receiptData['amount_to_be_paid'], 2);

            if ($parentId !== null) {
                $claimDetail->parent_id = $parentId;
            }

            // Handle file upload
            $fileHandler = new ClaimFileHandler();
            $fileName = $fileHandler->handleFileUpload($fileData, $claimMaster->claim_code, $index, true);
            if ($fileName) {
                $claimDetail->receipt_file = $fileName;
            } elseif (!$fileName && $fileHandler->getLastError()) {
                return $fileHandler->getLastError();
            }

            if (!$claimDetail->save()) {
                 \common\models\myTools\Mydebug::dumpFileW($claimDetail->getError());
                return 'Failed to save claim detail: ' . implode(', ', $claimDetail->getFirstErrors());
            }

            return true;
        } catch (\Exception $exc) {
            return $exc->getMessage();
        }
    }

    // Update receipt details
    private function updateReceiptDetails($claimMaster, $receiptsData, $uploadedFiles, $travelAllowanceId = null) {
        try {
            $existingDetails = $this->getExistingReceiptDetails($claimMaster->id);
            $processedIds = [];

            foreach ($receiptsData as $index => $receiptData) {
                $result = $this->updateSingleReceiptDetail($claimMaster, $receiptData, $uploadedFiles[$index] ?? null, $index, $existingDetails, $processedIds, $travelAllowanceId);

                if ($result !== true) {
                    return $result;
                }
            }

            // Handle deletions
            return $this->handleDeletedDetails($existingDetails, $processedIds);
        } catch (\Exception $exc) {
            return $exc->getMessage();
        }
    }

    // Get existing receipt details (excluding travel allowance)
    private function getExistingReceiptDetails($claimMasterId) {
        return ClaimDetail::find()->where(['claim_master_id' => $claimMasterId])->andWhere(['!=', 'detail', 'Travel Allowance'])->indexBy('id')->all();
    }

    // Update single receipt detail
    private function updateSingleReceiptDetail($claimMaster, $receiptData, $fileData, $index, $existingDetails, &$processedIds, $travelAllowanceId) {
        try {
            $isExisting = isset($receiptData['id']) && !empty($receiptData['id']) && isset($existingDetails[$receiptData['id']]);

            if ($isExisting) {
                $claimDetail = $existingDetails[$receiptData['id']];
                $processedIds[] = (int) $receiptData['id'];
            } else {
                $claimDetail = new ClaimDetail();
                $claimDetail->claimant_id = Yii::$app->user->identity->id;
                $claimDetail->claim_master_id = $claimMaster->id;
            }

            // Update basic properties
            $claimDetail->receipt_date = $receiptData['receipt_date'];
            $claimDetail->detail = $receiptData['detail'];
            $claimDetail->receipt_amount = round((float) $receiptData['receipt_amount'], 2);
            $claimDetail->amount_to_be_paid = round((float) $receiptData['amount_to_be_paid'], 2);

            if ($travelAllowanceId !== null) {
                $claimDetail->parent_id = $travelAllowanceId;
            }

            // Handle file operations
            $fileHandler = new ClaimFileHandler();
            $result = $fileHandler->handleFileUpdate($claimDetail, $fileData, $claimMaster->claim_code, $index, $isExisting);
            if ($result !== true) {
                return $result;
            }

            if (!$claimDetail->save()) {
                return 'Failed to save claim detail: ' . implode(', ', $claimDetail->getFirstErrors());
            }

            if (!in_array($claimDetail->id, $processedIds)) {
                $processedIds[] = $claimDetail->id;
            }

            return true;
        } catch (\Exception $exc) {
            return $exc->getMessage();
        }
    }

    // Handle deleted details
    private function handleDeletedDetails($existingDetails, $processedIds) {
        $detailsToDelete = array_diff(array_keys($existingDetails), $processedIds);

        foreach ($detailsToDelete as $detailId) {
            try {
                $detailToDelete = $existingDetails[$detailId];

                if ($detailToDelete->receipt_file && $detailToDelete->is_deleted != 1) {
                    $fileHandler = new ClaimFileHandler();
                    $fileHandler->deleteFile($detailToDelete->receipt_file);
                }

                $detailToDelete->is_deleted = 1;
                $detailToDelete->deleted_by = Yii::$app->user->identity->id;
                $detailToDelete->deleted_at = new \yii\db\Expression('NOW()');

                if (!$detailToDelete->update()) {
                    return "Failed to delete detail ID: {$detailId}";
                }

                Yii::info("Soft deleted claim detail ID: {$detailId}", __METHOD__);
            } catch (\Exception $e) {
                Yii::error("Error deleting claim detail ID {$detailId}: " . $e->getMessage(), __METHOD__);
                return $e->getMessage();
            }
        }

        return true;
    }
}
