<?php

namespace frontend\models\working\claim;

use Yii;
use common\models\myTools\MyFormatter;
use common\models\User;

/**
 * This is the model class for table "claims_detail".
 *
 * @property int $claims_detail_id
 * @property int|null $claimant_id
 * @property int|null $claim_master_id
 * @property string $claim_type
 * @property string $date1
 * @property string|null $date2
 * @property string|null $company_name
 * @property string|null $receipt_no
 * @property string|null $detail
 * @property string|null $project_account
 * @property int|null $authorized_by
 * @property int $authorize_status 0=default, 1=requested, 2=approved, 3=rejected
 * @property float $amount
 * @property int $receipt_lost
 * @property string|null $filename
 * @property string|null $grn_no
 * @property int $special_approved 0=default, 1=requested, 2=approved, 3=rejected
 * @property string|null $special_request_remark
 * @property int $is_submitted
 * @property int $is_deleted
 * @property string $created_at
 * @property string|null $update_at
 * @property int|null $update_by
 *
 * @property User $authorizedBy
 * @property RefClaimType $claimType
 * @property User $claimant
 * @property ClaimsMaster $claimMaster
 * @property ClaimsDetailSub[] $claimsDetailSubs
 */
class ClaimsDetail extends \yii\db\ActiveRecord {

    public $scannedFile;
    public $amtDay;
    public $tempAuthorizeName;
    public $saveAndNext;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claims_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claimant_id', 'claim_master_id', 'authorized_by', 'authorize_status', 'receipt_lost', 'special_approved', 'is_submitted', 'is_deleted', 'update_by'], 'integer'],
            [['claim_type', 'date1', 'amount', 'detail', 'project_account'], 'required'],
            [['date1', 'date2', 'created_at', 'update_at'], 'safe'],
            [['amount', 'amtDay'], 'number'],
            [['special_request_remark', 'tempAuthorizeName'], 'string'],
            [['claim_type'], 'string', 'max' => 5],
            [['company_name', 'receipt_no', 'detail', 'filename', 'grn_no', 'saveAndNext'], 'string', 'max' => 255],
            [['project_account'], 'string', 'max' => 100],
            [['authorized_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['authorized_by' => 'id']],
            [['claim_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefClaimType::className(), 'targetAttribute' => ['claim_type' => 'code']],
            [['claimant_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['claimant_id' => 'id']],
            [['claim_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimsMaster::className(), 'targetAttribute' => ['claim_master_id' => 'claims_master_id']],
            ['date1', 'validateDate2'],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
//            ['scannedFile', 'file',  'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
            ['scannedFile', 'file', 'extensions' => 'png, jpg, jpeg, pdf', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    public function validateDate2() {
        if ($this->claim_type == 'tra') {
            if ($this->date2 == '') {
                $this->addError('date2', 'Second date cannot be empty for Travel Claim');
            } else if (strtotime($this->date2) < strtotime($this->date1)) {
                $this->addError('date2', 'Second date must be later than first date');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'claims_detail_id' => 'Claims Detail ID',
            'claimant_id' => 'Claimant',
            'claim_master_id' => 'Claim Master ID',
            'claim_type' => 'Claim Type',
            'date1' => 'Date1',
            'date2' => 'Date2',
            'company_name' => 'Company Name',
            'receipt_no' => 'Receipt No',
            'detail' => 'Detail',
            'project_account' => 'Project / Account',
            'authorized_by' => 'Authorized By',
            'authorize_status' => 'Authorize Status',
            'amount' => 'Amount',
            'receipt_lost' => 'Receipt Lost',
            'filename' => 'Filename',
            'grn_no' => 'Grn No',
            'special_approved' => 'Special Approved',
            'special_request_remark' => 'Special Request Remark',
            'is_submitted' => 'Is Submitted',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'update_at' => 'Update At',
            'update_by' => 'Update By',
        ];
    }

    /**
     * Gets query for [[AuthorizedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthorizedBy() {
        return $this->hasOne(User::className(), ['id' => 'authorized_by']);
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
     * Gets query for [[ClaimMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimMaster() {
        return $this->hasOne(ClaimsMaster::className(), ['claims_master_id' => 'claim_master_id']);
    }

    /**
     * Gets query for [[ClaimsDetailSubs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsDetailSubs() {
        return $this->hasMany(ClaimsDetailSub::className(), ['claims_detail_id' => 'claims_detail_id']);
    }

    /**
     * *************************** MAIN FUNCTION
     * Process and save claim record
     * @return boolean
     */
    public function processAndSave() {
        $this->date1 = MyFormatter::fromDateRead_toDateSQL($this->date1);

        if ($this->date2 != '' && $this->claim_type == 'tra') {
            $this->date2 = MyFormatter::fromDateRead_toDateSQL($this->date2);
        } else {
            $this->date2 = '';
        }

        $extraProj = Yii::$app->request->post('extraProj');
        $extraDetail = Yii::$app->request->post('extraDetail');
        $extraAmount = Yii::$app->request->post('extraAmount');



        if ($this->save()) {

            ClaimsDetailSub::deleteAll('claims_detail_id = ' . $this->claims_detail_id);

            // Save record into claims Sub database table
            $claimDetailSub = new ClaimsDetailSub();
            $claimDetailSub->claims_detail_id = $this->claims_detail_id;
            $claimDetailSub->project_account = $this->project_account;
            $claimDetailSub->detail = $this->detail;
            $claimDetailSub->amount = $this->amount;
            $claimDetailSub->save();

            // If have multiple
            if ($extraProj) {
                foreach ($extraProj as $key => $proj) {
                    $claimDetailSub2 = new ClaimsDetailSub();
                    $claimDetailSub2->claims_detail_id = $this->claims_detail_id;
                    $claimDetailSub2->project_account = $proj;
                    $claimDetailSub2->detail = $extraDetail[$key];
                    $claimDetailSub2->amount = $extraAmount[$key];
                    $claimDetailSub2->save();
                    $this->amount += $extraAmount[$key];
                }
                $this->update(false);
            }

            if ($this->validate() && $this->scannedFile) {
                $this->filename = $this->claims_detail_id . '.' . $this->scannedFile->extension;
                $filePath = Yii::$app->params['claim_file_path'] . $this->filename;
                $this->scannedFile->saveAs($filePath);
                if ($this->scannedFile->extension != "pdf") {
                    \common\models\myTools\ImageHandler::resize_image_w1200($filePath, $this->scannedFile->extension);
                }
                $this->update(false);
            }
            return $this->saveAndNext;
        } else {
            return "";
        }
    }

    /**
     * ****************************** FUNCTION
     * To check if this record is more than 2 months (Expired)
     * @return type
     */
    public function isExpired() {

        $dateRecord = date_create($this->date1);
        $dateNow = date_create();
        $diff = ($dateNow->format("Y") - $dateRecord->format("Y")) * 12 + $dateNow->format("m") - $dateRecord->format("m");

        return ($diff >= 2 ? true : false);
    }

    /**     * ***************************** FUNCTION - check authorization
     * if Authorized By user has authority to "Skip Authorization, then the status is set as Authorized, id=2
     * status code: 1-Pending for request, 2-Approve, 3-Rejected
     */
    public function initiateAuthorization() {
        $pendingAuthorize = 0;

        if ($this->authorizedBy) {
            $user = $this->authorizedBy;
            if ($user->skip_claim_authorize) {
                $this->authorize_status = 2;
            } else {
                $this->authorize_status = 1;
                $pendingAuthorize = 1;
            }
        }
        return $pendingAuthorize;
    }

    /**     * *************Main Functions
     * 
     * @param type $sts
     */
    public function updateAuthorize($sts) {
        if ($sts == 1) {//Approve
            $this->authorize_status = 2;
        } else { // Rejected
            $this->authorize_status = 3;
        }
        $this->update(false);
    }

    /**     * ***************************** Function
     * Use when copying the items
     */
    public function duplicate() {
        $newRecord = new ClaimsDetail();

        $newRecord->claimant_id = $this->claimant_id;
        $newRecord->claim_type = $this->claim_type;
        $newRecord->date1 = $this->date1;
        $newRecord->date2 = $this->date2;
        $newRecord->company_name = $this->company_name;
        $newRecord->receipt_no = $this->receipt_no;
        $newRecord->detail = $this->detail;
        $newRecord->project_account = $this->project_account;
        $newRecord->authorized_by = $this->authorized_by;
        $newRecord->authorize_status = 0;
        $newRecord->amount = $this->amount;
        $newRecord->receipt_lost = $this->receipt_lost;
        $newRecord->filename = $this->filename;
        $newRecord->save();


        foreach ($this->claimsDetailSubs as $claimsDetailSub) {
            $claimsDetailSub->duplicate($newRecord->claims_detail_id);
        }
    }

    /**     * ********************************** FUNCTION
     * *********************** Invoke when Director approve/decline special approval
     * @param type $claimIds
     * @param type $approval
     */
    public function specialApprove($claimIds, $approval) {
        if ($approval == 1) {
            $approval = 2;
        } else {
            $approval = 3;
        }
        try {
            if (ClaimsDetail::updateAll(['special_approved' => $approval], ['in', 'claims_detail_id', $claimIds])) {
                if ($approval == 2) {
                    \common\models\myTools\FlashHandler::success("Approved");
                } else {
                    \common\models\myTools\FlashHandler::success("Rejected");
                }
            }
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }

    public function changeClaimType($claimTypeNew) {
        $claimTypeOld = $this->claim_type;

        $audit = new \frontend\models\common\AuditTrail();
        $audit->created_by = Yii::$app->user->id;
        $audit->table = $this->tableName();
        $audit->idx_no = strval($this->claims_detail_id);
        $audit->module = 'claim_claimType';
        $audit->detail = "Change claim type from $claimTypeOld to $claimTypeNew";
        $audit->save();

        $this->claim_type = $claimTypeNew;
        return $this->update(false);
    }

    /**
     * to check if the travel claim detail has repeated date (with submitted claim)
     */
    static public function checkTravelRepeated($id) {
        $on1 = "b.claim_type='tra' AND (a.date1 BETWEEN b.date1 AND b.date2 OR a.date2 BETWEEN b.date1 AND b.date2 OR b.date1 BETWEEN a.date1 AND a.date2 OR b.date2 BETWEEN a.date1 AND a.date2) AND a.claims_detail_id!=b.claims_detail_id AND b.claimant_id=a.claimant_id";
        $on2 = "c.claims_master_id=b.claim_master_id AND c.claims_status IN (2,3,4,5)";
        $rows = (new \yii\db\Query())
                ->select(['c.claims_id'])
                ->from('claims_detail AS a')
                ->join("INNER JOIN", 'claims_detail AS b', $on1)
                ->join("INNER JOIN", 'claims_master AS c', $on2)
                ->where(['a.claims_detail_id' => $id])
                ->all();

        $repeated = "";
        foreach ($rows as $claimId) {
            $repeated .= $claimId['claims_id'] . ", ";
        }

        return $repeated;
    }

    /**
     * to check if the travel claim detail has repeated date
     */
    static public function checkTravelRepeatedAll($id) {
        $on1 = "b.claim_type='tra' AND (a.date1 BETWEEN b.date1 AND b.date2 OR a.date2 BETWEEN b.date1 AND b.date2 OR b.date1 BETWEEN a.date1 AND a.date2 OR b.date2 BETWEEN a.date1 AND a.date2) AND a.claims_detail_id!=b.claims_detail_id AND b.claimant_id=a.claimant_id AND b.is_deleted=0";
        $on2 = "c.claims_master_id=b.claim_master_id";
        $rows = (new \yii\db\Query())
                ->select(['b.claims_detail_id', 'c.claims_id', 'c.claims_status'])
                ->from('claims_detail AS a')
                ->join("INNER JOIN", 'claims_detail AS b', $on1)
                ->join("LEFT JOIN", 'claims_master AS c', $on2)
                ->where(['a.claims_detail_id' => $id])
                ->having('c.claims_status IS NULL OR claims_status IN (1,2, 3, 4, 5)')
                ->distinct()
                ->all();

        $repeated = "";
        foreach ($rows as $claimId) {
            $repeated .= ($repeated == '' ? '' : ', ') . ($claimId['claims_id'] ? $claimId['claims_id'] : "(Pending Item)");
        }

        return $repeated;
    }

    public function showDetail() {
        $prefix = "";
        if ($this->claim_type == "med") {
            $prefix = "(Medical) - ";
        } else if ($this->claim_type == "ent") {
            $prefix = "(Entertainment) - ";
        }

        return $prefix . $this->detail;
    }

}
