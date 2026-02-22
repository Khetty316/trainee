<?php

namespace frontend\models\quotation;

use Yii;
use common\models\User;
use frontend\models\working\project\MasterProjects;
use common\models\myTools\MyCommonFunction;
use frontend\models\quotation\QuotationDetails;
use common\models\myTools\MyFormatter;
use frontend\models\working\po\PurchaseOrderMaster;

/**
 * This is the model class for table "quotation_masters".
 *
 * @property int $id
 * @property int $requestor_id
 * @property string $project_code
 * @property string|null $file_reference
 * @property string $description
 * @property int $proc_approval 1=approved,2=rejected
 * @property string|null $proc_remark
 * @property int|null $proc_approve_by
 * @property string|null $proc_approve_at
 * @property int $requestor_approval 1=approved,2=rejected
 * @property string|null $requestor_remark
 * @property int|null $requestor_approve_by
 * @property string|null $requestor_approve_at
 * @property int $manager_approval 1=approved,2=rejected
 * @property string|null $manager_remark
 * @property int|null $manager_approve_by
 * @property string|null $manager_approve_at
 * @property int $request_is_complete
 * @property int|null $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property PurchaseOrderMaster[] $purchaseOrderMasters
 * @property QuotationDetails[] $quotationDetails
 * @property User $managerApproveBy
 * @property User $procApproveBy
 * @property MasterProjects $projectCode
 * @property User $requestor
 * @property User $requestorApproveBy
 */
class QuotationMasters extends \yii\db\ActiveRecord {

    public $scannedFile;
    public $scannedFiles;

    const APPROVE_YES = 1;
    const APPROVE_NO = 2;
    const STS_PROC = "Awaiting for Procurement";
    const STS_PROC_APP = "Approved by Procurement";
    const STS_PROC_REJ = "Rejected by Procurement";
    const STS_REQ = "Awaiting for requestor's approval";
    const STS_REQ_APP = "Approved by requestor";
    const STS_REQ_REJ = "Rejected by requestor";
    const STS_MGR = "Awaiting for manager's approval";
    const STS_MGR_APP = "Approved by manager";
    const STS_MGR_REJ = "Rejected by manager";
    const STS_WAIT_PO = "Waiting for P.O. from Procurement";
    const STS_PO_DONE = "P.O. issued";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'quotation_masters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['requestor_id', 'project_code', 'description'], 'required'],
            [['requestor_id', 'proc_approval', 'proc_approve_by', 'requestor_approval', 'requestor_approve_by', 'manager_approval', 'manager_approve_by', 'request_is_complete', 'created_by', 'updated_by'], 'integer'],
            [['description', 'proc_remark', 'requestor_remark', 'manager_remark'], 'string'],
            [['proc_approve_at', 'requestor_approve_at', 'manager_approve_at', 'created_at', 'updated_at'], 'safe'],
            [['project_code'], 'string', 'max' => 20],
            [['file_reference'], 'string', 'max' => 255],
            [['manager_approve_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['manager_approve_by' => 'id']],
            [['proc_approve_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['proc_approve_by' => 'id']],
            [['project_code'], 'exist', 'skipOnError' => true, 'targetClass' => MasterProjects::className(), 'targetAttribute' => ['project_code' => 'project_code']],
            [['requestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor_id' => 'id']],
            [['requestor_approve_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor_approve_by' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => 'png, jpg, jpeg, pdf', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'P.R. ID',
            'requestor_id' => 'Requestor ID',
            'project_code' => 'Project Code',
            'file_reference' => 'File Reference',
            'description' => 'Description',
            'proc_approval' => 'Proc Approval',
            'proc_remark' => 'Proc Remark',
            'proc_approve_by' => 'Proc Approve By',
            'proc_approve_at' => 'Proc Approve At',
            'requestor_approval' => 'Requestor Approval',
            'requestor_remark' => 'Requestor Remark',
            'requestor_approve_by' => 'Requestor Approve By',
            'requestor_approve_at' => 'Requestor Approve At',
            'manager_approval' => 'Manager Approval',
            'manager_remark' => 'Manager Remark',
            'manager_approve_by' => 'Manager Approve By',
            'manager_approve_at' => 'Manager Approve At',
            'request_is_complete' => 'Request Is Complete',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[PurchaseOrderMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseOrderMasters() {
        return $this->hasMany(PurchaseOrderMaster::className(), ['quotation_master_id' => 'id']);
    }

    /**
     * Gets query for [[QuotationDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationDetails() {
        return $this->hasMany(QuotationDetails::className(), ['quotation_master_id' => 'id']);
    }

    /**
     * Gets query for [[ManagerApproveBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getManagerApproveBy() {
        return $this->hasOne(User::className(), ['id' => 'manager_approve_by']);
    }

    /**
     * Gets query for [[ProcApproveBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProcApproveBy() {
        return $this->hasOne(User::className(), ['id' => 'proc_approve_by']);
    }

    /**
     * Gets query for [[ProjectCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCode() {
        return $this->hasOne(MasterProjects::className(), ['project_code' => 'project_code']);
    }

    /**
     * Gets query for [[Requestor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor() {
        return $this->hasOne(User::className(), ['id' => 'requestor_id']);
    }

    /**
     * Gets query for [[RequestorApproveBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestorApproveBy() {
        return $this->hasOne(User::className(), ['id' => 'requestor_approve_by']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_by = Yii::$app->user->identity->id;
            $this->created_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    public function processAndSave() {
        $this->requestor_id = Yii::$app->user->id;
        $this->save();

        $this->scannedFile = \yii\web\UploadedFile::getInstance($this, 'scannedFile');
        if ($this->validate() && $this->scannedFile) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['quotation_req_file_path'] . $this->id . "/";
            $this->file_reference = MyCommonFunction::nowDateTime() . '-' . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
            MyCommonFunction::mkDirIfNull($filePath);
            $this->scannedFile->saveAs($filePath . $this->file_reference);
            $this->update(false);
        }

        return true;
    }

    public function procAddQuotationFiles() {
        $this->scannedFile = \yii\web\UploadedFile::getInstances($this, 'scannedFiles');

        if ($this->scannedFile) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['quotation_req_file_path'] . $this->id . '/';
            MyCommonFunction::mkDirIfNull($filePath);
            foreach ($this->scannedFile as $file) {
                $quotationDetail = new QuotationDetails();
                $file->saveAs($filePath . MyCommonFunction::nowDateTime() . '-' . $file->baseName . "." . $file->extension);
                $quotationDetail->insertNew($this->id, MyCommonFunction::nowDateTime() . '-' . $file->baseName . "." . $file->extension);
            }
        }
        return true;
    }

    /**
     * Status changes, from Procurement to Requestor
     * @param type $post
     * @return type
     */
    public function forwardToRequestor($post, $isSaveOnly = 0) {
        $selectedId = array_key_exists('selectQuotation', $post) ? $post['selectQuotation'] : '';
        $remarks = $post['remark'];
        $suppliers = $post['supplier'];
        foreach ($remarks as $key => $remark) {
            $quotationDetail = QuotationDetails::findOne($key);
            if ($remark != "") {
                $quotationDetail->remark = $remark;
            }
            $quotationDetail->supplier_name = $suppliers[$key];
            $quotationDetail->is_selected = ($selectedId == $key ? 1 : 0);
            $quotationDetail->update();
        }
        if ($isSaveOnly == 0) {
            $this->proc_approval = self::APPROVE_YES;
            $this->proc_approve_at = new \yii\db\Expression('NOW()');
            $this->proc_approve_by = Yii::$app->user->id;
            $this->generateRequestorEmail();

            return $this->update();
        } else {
            return true;
        }
    }

    /**
     * Status changes, from Requestor to Manager
     * can be REJECT
     * @param type $post
     * @return type
     */
    public function forwardToManager($post) {

        $approval = ($post['approval'] == 0 ? self::APPROVE_NO : self::APPROVE_YES);

        $this->requestor_approval = $approval;
        $this->requestor_remark = $post['requestor_remark'];
        $this->requestor_approve_at = new \yii\db\Expression('NOW()');
        $this->requestor_approve_by = Yii::$app->user->id;

        if ($this->requestor_approval == self::APPROVE_NO) {
            $this->request_is_complete = 1;
        }
        $this->generateManagerEmail();
        return $this->update();
    }

    /**
     * Status changes, from Manager back to procurement
     * can be REJECT
     * @param type $post
     * @return type
     */
    public function managerApprove($post) {
        if ($this->manager_approval != 0) {
            return false;
        }

        $this->manager_approval = ($post['approval'] == 0 ? self::APPROVE_NO : self::APPROVE_YES);
        $this->manager_approve_at = new \yii\db\Expression('NOW()');
        $this->manager_approve_by = Yii::$app->user->id;
        $this->manager_remark = $post['manager_remark'];


        if ($this->manager_approval == self::APPROVE_NO) {
            $this->request_is_complete = 1;
        }
        $this->generateRequestorByManagerEmail($this->manager_approval);

        return $this->update();
    }

    public function getStatus() {
        if ($this->request_is_complete && $this->manager_approval == 1) {
            return self::STS_PO_DONE;
        } else if ($this->proc_approval != self::APPROVE_YES) {
            return $this->getProcApprovalSts();
        } else if ($this->requestor_approval != self::APPROVE_YES) {
            return $this->getReqApprovalSts();
        } else if ($this->manager_approval == self::APPROVE_YES) {
            return self::STS_WAIT_PO;
        } else {
            return $this->getMgrApprovalSts();
        }
    }

    public function getProcApprovalSts() {
        if ($this->proc_approval < self::APPROVE_YES) {
            return self::STS_PROC;
        } else if ($this->proc_approval == self::APPROVE_NO) {
            return self::STS_PROC_REJ;
        } else {
            return self::STS_PROC_APP . ", by " . $this->procApproveBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($this->proc_approve_at);
        }
    }

    public function getReqApprovalSts() {
        if ($this->requestor_approval < self::APPROVE_YES) {
            return self::STS_REQ;
        } else if ($this->requestor_approval == self::APPROVE_NO) {
            return self::STS_REQ_REJ;
        } else {
            return self::STS_REQ_APP . ", by " . $this->requestorApproveBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($this->requestor_approve_at);
        }
    }

    public function getMgrApprovalSts() {
        if ($this->manager_approval < self::APPROVE_YES) {
            return self::STS_MGR;
        } else if ($this->manager_approval == self::APPROVE_NO) {
            return self::STS_MGR_REJ;
        } else {
            return self::STS_MGR_APP . ", by " . $this->managerApproveBy->fullname . " @ " . MyFormatter::asDateTime_ReaddmYHi($this->manager_approve_at);
        }
    }

    public static function getAutoCompleteList() {
        $list = QuotationMasters::find()
                ->select(['quotation_masters.id as value', 'quotation_masters.id as id', 'CONCAT(quotation_masters.id,", by ",user.fullname) as label', 'project_code', 'user.fullname', 'user.id as user_id'])
                ->join('INNER JOIN', 'user', 'user.id=quotation_masters.requestor_id')
                ->asArray()
                ->all();
        return $list;
    }

    public static function getAutoCompleteList_activeOnly() {
        $list = QuotationMasters::find()
                ->select(['quotation_masters.id as value', 'quotation_masters.id as id', 'CONCAT(quotation_masters.id,", by ",user.fullname) as label', 'project_code', 'user.fullname', 'user.id as user_id'])
                ->join('INNER JOIN', 'user', 'user.id=quotation_masters.requestor_id')
                ->where(['manager_approval' => 1, 'request_is_complete' => 0])
                ->asArray()
                ->all();
        return $list;
    }

    public function setComplete() {
        $this->request_is_complete = 1;
        return $this->update();
    }

    private function generateRequestorEmail() {
        $actionBtnUrl = \yii\helpers\Url::base(true) . "/quotation/staff-view-quotation-detail?id=" . $this->id;
        $receiver = $this->requestor->email;
        $subject = "RFQ response by Procurement Department (request id: " . $this->id . ")";
        $textBody = $this->generateEmailTextBody($actionBtnUrl);
        $this->sendEmail($receiver, $subject, $textBody);
        return true;
    }

    private function generateManagerEmail() {
        $actionBtnUrl = \yii\helpers\Url::base(true) . "/quotation/mgr-view-process-quotation-detail?id=" . $this->id;
        $receiver = Yii::$app->params['managerEmails'];
        $subject = "RFQ - Awaiting for manager's approval (request id: " . $this->id . ")";
        $textBody = $this->generateEmailTextBody($actionBtnUrl);
        $this->sendEmail($receiver, $subject, $textBody);
        return true;
    }

    private function generateRequestorByManagerEmail() {

        if ($this->manager_approval == self::APPROVE_NO) {
            $subject = "RFQ REJECTED by Procurement Department (request id: " . $this->id . ")";
        } else {
            $subject = "RFQ approved by Procurement Department (request id: " . $this->id . ")";
        }
        $actionBtnUrl = \yii\helpers\Url::base(true) . "/quotation/staff-view-quotation-detail?id=" . $this->id;
        $receiver = $this->requestor->email;
        $textBody = $this->generateEmailTextBody($actionBtnUrl);
        $this->sendEmail($receiver, $subject, $textBody);
        return true;
    }

    private function generateEmailTextBody($actionBtnUrl) {
        $textBody = "<table style='border: 1px solid black' ><tr><td style='border: 1px solid black'>Request Id:</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                . $this->id
                . "</td></tr><tr><td style='border: 1px solid black'>Project</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                . $this->projectCode->project_code . " - " . $this->projectCode->project_name
                . "</td></tr><tr><td style='border: 1px solid black'>Status</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                . $this->getStatus()
                . "</td></tr><tr><td colspan='2'></td><td>"
                . "<a href='" . $actionBtnUrl . "' target='_blank'><button type='button'>VIEW / TAKE ACTION</button></a>"
                . "</td></tr></table>";
        return $textBody;
    }

    private function sendEmail($receiver, $subject, $textBody) {
        try {
            Yii::$app->mailer->compose()
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Robot'])
                    ->setTo($receiver)
                    ->setSubject($subject)
                    ->setHtmlBody($textBody)
                    ->send();
        } catch (Exception $e) {
            $err = 'Caught exception: ' . $e->getMessage() . "\n";
            \common\models\myTools\FlashHandler::err("$err Error, kindly contact the IT Department");
        }
    }

    static function getStatusList() {

        $list = [];
        $list[self::STS_PROC] = self::STS_PROC;
        $list[self::STS_PROC_REJ] = self::STS_PROC_REJ;
        $list[self::STS_REQ] = self::STS_REQ;
        $list[self::STS_REQ_REJ] = self::STS_REQ_REJ;
        $list[self::STS_MGR] = self::STS_MGR;
        $list[self::STS_MGR_REJ] = self::STS_MGR_REJ;
        $list[self::STS_WAIT_PO] = self::STS_WAIT_PO;
        $list[self::STS_PO_DONE] = self::STS_PO_DONE;

        return $list;
    }

}
