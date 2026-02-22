<?php

namespace frontend\models\working\claim;

use Yii;
use common\models\User;
use frontend\models\working\mi\MasterIncomings;
use frontend\models\working\claim\RefClaimStatus;
use yii\db\Expression;
use frontend\models\common\AuditTrail;

/**
 * This is the model class for table "claims_master".
 *
 * @property int $claims_master_id
 * @property string $claims_id
 * @property int|null $claimant_id
 * @property string $claim_type
 * @property int $claims_status
 * @property int|null $claims_mi_id
 * @property float $total_amount
 * @property string $created_at
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ClaimsDetail[] $claimsDetails
 * @property RefClaimType $claimType
 * @property User $claimant
 * @property MasterIncomings $claimsMi
 * @property RefClaimStatus $claimsStatus
 */
class ClaimsMaster extends \yii\db\ActiveRecord {/**
 * {@inheritdoc}
 */

    public static function tableName() {
        return 'claims_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claims_id', 'claim_type', 'total_amount'], 'required'],
            [['claimant_id', 'claims_status', 'claims_mi_id', 'updated_by'], 'integer'],
            [['total_amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['claims_id'], 'string', 'max' => 20],
            [['claim_type'], 'string', 'max' => 5],
            [['claims_id'], 'unique'],
            [['claim_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefClaimType::className(), 'targetAttribute' => ['claim_type' => 'code']],
            [['claimant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['claimant_id' => 'id']],
            [['claims_mi_id'], 'exist', 'skipOnError' => true, 'targetClass' => MasterIncomings::className(), 'targetAttribute' => ['claims_mi_id' => 'id']],
            [['claims_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefClaimStatus::className(), 'targetAttribute' => ['claims_status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'claims_master_id' => 'Claims Master ID',
            'claims_id' => 'Claim Form ID',
            'claimant_id' => 'Claimant',
            'claim_type' => 'Claim Type',
            'claims_status' => 'Claims Status',
            'claims_mi_id' => 'Claims Mi ID',
            'total_amount' => 'Total Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ClaimsDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsDetails() {
        return $this->hasMany(ClaimsDetail::className(), ['claim_master_id' => 'claims_master_id']);
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
     * Gets query for [[Claimant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimant() {
        return $this->hasOne(User::className(), ['id' => 'claimant_id']);
    }

    /**
     * Gets query for [[ClaimsMi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsMi() {
        return $this->hasOne(MasterIncomings::className(), ['id' => 'claims_mi_id']);
    }

    /**
     * Gets query for [[ClaimsStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsStatus() {
        return $this->hasOne(RefClaimStatus::className(), ['id' => 'claims_status']);
    }

    public function beforeSave($insert) {

        if (!$this->isNewRecord) {
            $this->updated_at = new Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * *************************** MAIN FUNCTION
     * Process and save claim record
     * @return boolean
     */
    public function submitClaims($claimIds, $claimFamily) {
        $ids = explode(",", $claimIds);
        $this->claim_type = $claimFamily;
        $this->claims_id = $this->generateClaimId();
        $this->claims_status = '1';
        $this->claimant_id = Yii::$app->user->identity->id;
        $this->total_amount = 0;
        if (!$this->save()) {
            return false;
        };
        if ($this->claims_id == "") {
            return false; // error if it's empty
        }

        $totalAmount = 0;
        $toBeAuthorize = 0;
        foreach ($ids as $id) {
            $claimDetail = ClaimsDetail::findOne($id);
            $claimDetail->claim_master_id = $this->claims_master_id;
            $claimDetail->is_submitted = 1;
            $toBeAuthorize += $claimDetail->initiateAuthorization();
            $claimDetail->update(false);
            $totalAmount += $claimDetail->amount;
        }
        $this->total_amount = $totalAmount;
        if ($toBeAuthorize > 0) { // If something else to be authorized, set to 1 (requested)
            $this->claims_status = 1;
        } else if ($this->claim_type == "tra") {
            $this->claims_status = 3; // 3 - Document Received
        } else {
            $this->claims_status = 2; // 2 - Waiting for document
        }
        return($this->update());
    }

    private function generateClaimId() {

        if ($this->claim_type == "") {
            return "";
        }

        $count = ClaimsMaster::find()->where("claim_type = '" . $this->claim_type . "'")->andWhere("YEAR(created_at) = " . date("Y"))->count();
        $count++;
        $less = 5 - strlen($count);
        $claimId = "";
        for ($i = 0; $i < $less; $i++) {
            $claimId .= "0";
        }

        $claimId = $this->claimType->claim_shortform . "-" . $claimId . $count . "-" . date('y');
        return $claimId;
    }

    /**     * ******************************************** MAIN FUNCTION
     * ** Response to the Claims Authorization
     * 
     * @param type $authorize_sts
     * @param type $claims_detail_ids
     */
    public function authorizeResponse($claimsDetailIdsApprove, $claimsDetailIdsReject) {

        $approve = explode(",", $claimsDetailIdsApprove);
        $reject = explode(",", $claimsDetailIdsReject);

//        ClaimsDetail::updateAll(['authorize_status' => 2], ['AND', ['in', 'claims_detail_id', $approve], ['NOT IN', 'claims_detail_id', $reject]]); // ["claims_detail_id IN (" . $claimsDetailIdsApprove . ") AND claims_detail_id NOT IN (" . $claimsDetailIdsReject . ")"]);
        if (sizeof($approve) > 0) {
            ClaimsDetail::updateAll(['authorize_status' => 2], ['IN', 'claims_detail_id', $approve]); // ["claims_detail_id IN (" . $claimsDetailIdsApprove . ") AND claims_detail_id NOT IN (" . $claimsDetailIdsReject . ")"]);
        }
        if (sizeof($reject) > 0) {
            ClaimsDetail::updateAll(['authorize_status' => 3], ["IN", "claims_detail_id", $reject]);
        }
        $this->updateClaimMasterAuthorization();
        // If document already received
        if ($this->claims_status == 3) {
            // Control MI
            $MI = $this->claimsMi;
            if ($MI->current_step_task_id == 15) { // Refer to Mi_Task_ID
                $MI->miResponse(1, "Authorized Claim");
            }
        }
    }

    /**     * ********************************** private FUNCTION
     * Go through all the claim detail and check if there is any record to be authorized. If no more, then change the status of the claim master.
     * if master already has master incoming, then status to 3 - Document Received;
     * else set to 2 - Waiting For Document, refer to RefClaimStatus
     * 
     * If one of the item is rejected, then reject the entire claim master form
     *      1. Submitted, waiting for verification
     *      2. Waiting For Document
     *      3. Document Received
     *      4. Paid By Account
     *      7. Authorization Rejected
     *      8. Closed/Cancelled
     */
    private function updateClaimMasterAuthorization() {
        $status = 2;
        $pendingAuth = $this->getClaimsDetails()->where("authorize_status = 1")->one();

        if ($pendingAuth != "") {
            $status = 1;
        } else {
            // If empty, then check if has rejected record
            $rejectedAuth = $this->getClaimsDetails()->where("authorize_status = 3")->one();
            if ($rejectedAuth != "") {
                $status = 7;
            }
        }
        if ($this->claims_mi_id != "" && $status == 2) { // If waiting for doc and doc already arrive
            $status = 3;
        }

        $this->claims_status = $status;
        $this->update(false);
    }

    /**     * ******************************************** MAIN FUNCTION
     * SET Claims' status to PAID
     * @param type $authorize_sts
     * @param type $claims_detail_ids
     */
    public function payClaim() {
        $this->claims_status = 4;
        return $this->update(false);
    }
    /**     * ******************************************** MAIN FUNCTION
     * SET Claims' status to PAID
     * @param type $authorize_sts
     * @param type $claims_detail_ids
     */
    public function hrApproveClaim() {
        $this->claims_status = 5;
        return $this->update(false);
    }
    
    /**     * ******************************************** MAIN FUNCTION
     * SET Claims' status to REJECTED
     */
    public function rejectClaim() {
        $this->claims_status = 9;
        return $this->update(false);
    }

    /**     * ******************************************* MAIN FUNCTION
     * 
     */
    public function copyItems() {
        $claimDetailList = ClaimsDetail::find()->where("claim_master_id=" . $this->claims_master_id)->all();

        foreach ($claimDetailList as $claimsDetail) {
            $claimsDetail->duplicate();
        }
        $this->claims_status = 8;
        $this->update(false);
        \common\models\myTools\FlashHandler::success("Claim items copied");
    }

    public function transferClaimant($claimantId) {

        AuditTrail::createNew($this->tableName(), strval($this->claims_master_id), 'claim_claimant', "Change claimant from $this->claimant_id to $claimantId");

        $this->claimant_id = $claimantId;
        if (!$this->update(false)) {
            \common\models\myTools\FlashHandler::err('Fail to transfer. Kindly contact IT Department.');
            return false;
        }

        $claimDetail = new ClaimsDetail();
        return($claimDetail->updateAll(['claimant_id' => $claimantId], ['claim_master_id' => $this->claims_master_id]));
    }

    public static function getPendingClaimList() {
        return \yii\helpers\ArrayHelper::map(ClaimsMaster::find()->where('claims_mi_id IS NULL AND claim_type <> "tra" AND claims_status= 2')->orderBy(['claims_id' => SORT_ASC])->all(), "claims_master_id", "claims_id");
    }
    
    public static function getPendingClaimList_obj() {
        return ClaimsMaster::find()->where('claims_mi_id IS NULL AND claim_type <> "tra" AND claims_status= 2')->orderBy(['claims_id' => SORT_ASC])->all();
    }

    public function getPriorGRN() {
        $returnStr = "";
        foreach ($this->claimsDetails as $claimDetail) {
            if ($claimDetail->grn_no) {
                $returnStr .= ($returnStr == "" ? "" : ", ") . $claimDetail->grn_no;
            }
        }
        return $returnStr;
    }

    public function rejectClaimDocument($miId) {
        $claim = $this->find()->where('claims_mi_id=' . $miId)->one();
        if ($claim) {
            $claim->claims_status = 9;
            return($claim->update(false));
        }

        return false;
    }

}
