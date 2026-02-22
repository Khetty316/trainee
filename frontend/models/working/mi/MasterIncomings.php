<?php

namespace frontend\models\working\mi;

use Yii;
use common\models\User;
use frontend\models\working\project\MasterProjects;
use frontend\models\working\po\PurchaseOrderMaster;
use common\models\myTools\email;
use yii\db\Expression;
use frontend\models\common\RefCurrencies;
use Exception;
use frontend\models\working\claim\ClaimsMaster;

/**
 * This is the model class for table "master_incomings".
 *
 * @property int $id
 * @property string|null $index_no
 * @property int $uploader_id
 * @property int $doc_type_id
 * @property int|null $sub_doc_type_id
 * @property string|null $doc_due_date
 * @property string|null $reference_no
 * @property string|null $particular
 * @property float|null $amount
 * @property int|null $currency
 * @property int|null $po_id
 * @property int $isUrgent
 * @property int $isPerforma
 * @property int $file_type_id
 * @property string $received_from
 * @property string|null $remarks
 * @property string|null $filename
 * @property string|null $project_code
 * @property int $requestor_id
 * @property int|null $current_step
 * @property int|null $current_step_task_id
 * @property string|null $grn_no
 * @property int $mi_status
 * @property int $acknowledge_sts 0=no need review, 1=to be reviewed, 2=reviewed by director 
 * @property int $acknowledge_req_sts 0=no need review, 1=to be reviewed, 2=reviewed by requestor
 * @property int|null $final_invoice
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property ClaimsMaster[] $claimsMasters
 * @property RefMiDoctypes $docType
 * @property RefMiFiletypes $fileType
 * @property RefCurrencies $currency0
 * @property MasterIncomings $finalInvoice
 * @property MasterIncomings[] $masterIncomings
 * @property PurchaseOrderMaster $po
 * @property RefMiStatus $miStatus
 * @property MasterProjects $projectCode
 * @property User $requestor
 * @property RefMiTasks $currentStepTask
 * @property RefMiSubdoctypes $subDocType
 * @property User $uploader
 * @property MiProjects[] $miProjects
 * @property MiWorklist[] $miWorklists
 */
class MasterIncomings extends \yii\db\ActiveRecord {

    public $scannedFile;
    public $adminKeepDocTaskId = 11;
    public $doneTaskId = 7;
    public $forceCloseTaskId = 12;
    public $miCloseStatusId = 3;
    public $claimsId;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'master_incomings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['uploader_id', 'doc_type_id', 'file_type_id', 'received_from', 'requestor_id', 'project_code'], 'required'],
            [['uploader_id', 'doc_type_id', 'sub_doc_type_id', 'currency', 'isUrgent', 'isPerforma', 'file_type_id', 'requestor_id', 'current_step', 'current_step_task_id', 'mi_status', 'acknowledge_sts', 'acknowledge_req_sts', 'claimsId'], 'integer'],
            [['doc_due_date', 'created_at', 'updated_at', 'filename'], 'safe'],
            [['amount'], 'number'],
            [['remarks', 'filename', 'po_id'], 'string'],
            [['index_no', 'project_code'], 'string', 'max' => 20],
            [['reference_no', 'particular', 'received_from', 'grn_no'], 'string', 'max' => 255],
            [['doc_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMiDoctypes::className(), 'targetAttribute' => ['doc_type_id' => 'doc_type_id']],
            [['file_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMiFiletypes::className(), 'targetAttribute' => ['file_type_id' => 'file_type_id']],
            [['currency'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency' => 'currency_id']],
            [['final_invoice'], 'exist', 'skipOnError' => true, 'targetClass' => MasterIncomings::className(), 'targetAttribute' => ['final_invoice' => 'id']],
            [['po_id'], 'exist', 'skipOnError' => true, 'targetClass' => PurchaseOrderMaster::className(), 'targetAttribute' => ['po_id' => 'po_id']],
            [['mi_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefMiStatus::className(), 'targetAttribute' => ['mi_status' => 'mi_status_code']],
            [['project_code'], 'exist', 'skipOnError' => true, 'targetClass' => MasterProjects::className(), 'targetAttribute' => ['project_code' => 'project_code']],
            [['requestor_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor_id' => 'id']],
            [['current_step_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMiTasks::className(), 'targetAttribute' => ['current_step_task_id' => 'task_id']],
            [['sub_doc_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefMiSubdoctypes::className(), 'targetAttribute' => ['sub_doc_type_id' => 'sub_doc_type_id']],
            [['uploader_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uploader_id' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, pdf, jpeg'],
            ['scannedFile', 'file', 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg']],
//            [['updated_at'], 'default', 'value' => new CDbExpression('NOW()'), 'setOnEmpty' => false, 'on' => 'update']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'index_no' => 'Index No',
            'uploader_id' => 'Uploader',
            'doc_type_id' => 'Doc Type',
            'sub_doc_type_id' => 'Sub Doc Type',
            'doc_due_date' => 'Doc. Date',
            'reference_no' => 'Invoice / Proforma Inv. No / Rererence No.',
            'particular' => 'Particular',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'po_id' => 'Po ID',
            'isUrgent' => 'Is Urgent',
            'isPerforma' => 'Is Pro Forma',
            'file_type_id' => 'File Type',
            'received_from' => 'Received From',
            'remarks' => 'Remarks',
            'filename' => 'Filename',
            'project_code' => 'Project Code',
            'requestor_id' => 'Requestor ID',
            'current_step' => 'Current Step',
            'current_step_task_id' => 'Current Step Task ID',
            'grn_no' => 'Grn No',
            'mi_status' => 'Mi Status',
            'acknowledge_sts' => 'Director Acknowledge Sts',
            'acknowledge_req_sts' => 'Requestor Acknowledge Sts',
            'final_invoice' => 'Final Invoice',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[ClaimsMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsMasters() {
        return $this->hasMany(ClaimsMaster::className(), ['claims_mi_id' => 'id']);
    }

    /**
     * Gets query for [[DocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocType() {
        return $this->hasOne(RefMiDoctypes::className(), ['doc_type_id' => 'doc_type_id']);
    }

    /**
     * Gets query for [[FileType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFileType() {
        return $this->hasOne(RefMiFiletypes::className(), ['file_type_id' => 'file_type_id']);
    }

    /**
     * Gets query for [[Currency0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency0() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency']);
    }

    /**
     * Gets query for [[FinalInvoice]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinalInvoice() {
        return $this->hasOne(MasterIncomings::className(), ['id' => 'final_invoice']);
    }

    /**
     * Gets query for [[MasterIncomings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMasterIncomings() {
        return $this->hasMany(MasterIncomings::className(), ['final_invoice' => 'id']);
    }

    /**
     * Gets query for [[Po]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPo() {
        return $this->hasOne(PurchaseOrderMaster::className(), ['po_id' => 'po_id']);
    }

    /**
     * Gets query for [[MiStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiStatus() {
        return $this->hasOne(RefMiStatus::className(), ['mi_status_code' => 'mi_status']);
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
     * Gets query for [[CurrentStepTask]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrentStepTask() {
        return $this->hasOne(RefMiTasks::className(), ['task_id' => 'current_step_task_id']);
    }

    /**
     * Gets query for [[SubDocType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubDocType() {
        return $this->hasOne(RefMiSubdoctypes::className(), ['sub_doc_type_id' => 'sub_doc_type_id']);
    }

    /**
     * Gets query for [[Uploader]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUploader() {
        return $this->hasOne(User::className(), ['id' => 'uploader_id']);
    }

    /**
     * Gets query for [[MiProjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiProjects() {
        return $this->hasMany(MiProjects::className(), ['mi_id' => 'id']);
    }

    /**
     * Gets query for [[MiWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMiWorklists() {
        return $this->hasMany(MiWorklist::className(), ['mi_id' => 'id']);
    }

    public function beforeSave($insert) {
//        if ($this->isNewRecord)
//            $this->created_at = new CDbExpression('NOW()');
        if (!$this->isNewRecord) {
            $this->updated_at = new Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

    // ****************************************************************************************
    // ****************************************************************************************
    // ****************************************************************************************
    // ****************************************************************************************
    // ****************************************************************************************
    // Process New Request and Save into DB
    public function processAndSave() {

        // get po_id from name:po_id instead of masterincomings-po_id;

        $this->po_id = Yii::$app->request->post('po_id');


        if ($this->save()) {

            $this->current_step = "1";
            $count = MasterIncomings::find()->where("DATE(created_at) = DATE(NOW())")->andWhere("id <= " . $this->id)->count();
            $this->index_no = date("Ymd") . "-" . ($count < 10 ? "0" . $count : $count);
            $filename = "";
            if ($this->validate()) {
                $filename = $this->index_no . '.' . $this->scannedFile->extension;
                \common\models\myTools\MyCommonFunction::mkDirIfNull(Yii::$app->params['MI_file_path']);
                $this->scannedFile->saveAs(Yii::$app->params['MI_file_path'] . $filename);
            }
            $this->current_step_task_id = (RefMiMatrices::find()->where(["doc_type_id" => $this->doc_type_id, "step" => $this->current_step])->one())->task_id;
            $this->filename = $filename;
            $this->update(false);
            $this->actionOnCurrentTask();

            // Insert a record into if is invoice or Proforma
            $project = new MiProjects();
            $project->currency_id = $this->currency;
            $project->mi_id = $this->id;
            $project->project_code = $this->project_code;
            $project->requestor = $this->requestor_id;
            $project->amount = $this->amount;
            $project->created_by = Yii::$app->user->identity->id;
            $project->active = 1;

            $project->save();


            if (Yii::$app->request->post("hasMultipleProj") == 'on') {
                $projectList = Yii::$app->request->post("extraProj");
                $PICList = Yii::$app->request->post("extraPIC");
                $amtList = Yii::$app->request->post("myamount");
                foreach ($projectList as $key => $projects) {
                    if ($projects != "" && $PICList[$key] != "") {
                        $project = new MiProjects();
                        $project->currency_id = $this->currency;
                        $project->mi_id = $this->id;
                        $project->project_code = $projects;
                        $project->requestor = $PICList[$key];
                        $project->amount = $amtList[$key];
                        $project->created_by = Yii::$app->user->identity->id;
                        $project->save();
                    }
                }
            }


            // IF Document is related to Claims, link the claims id
            if ($this->doc_type_id == 1 && $this->claimsId != "") {
                $claim = \frontend\models\working\claim\ClaimsMaster::findOne($this->claimsId);
                $claim->claims_mi_id = $this->id;
                $claim->claims_status = 3;
                $claim->update(false);
            }


            return true;
        } else {
            return false;
        }
    }

    public function processAndUpdate() {
        $this->po_id = Yii::$app->request->post('po_id');
        if ($this->update()) {

            return true;
        } else {
            return false;
        }
    }

    // Main Function *****************************************************
    // Insert GRN into M.I. table
    public function miUpdateGrn($grn, $poId, $approval, $remarks) {
        $this->insertWorklist($approval, $remarks); // insert work list
        $this->updateStep($approval);        // Update Step & Task
        $this->actionOnCurrentTask(); // Work based on the task
        $this->grn_no = $grn;
        $this->po_id = $poId;
        return $this->update(false);
    }

    public function miUpdateSubInvoice($miIds, $miIdsUncheck) {
        if ($miIds) {
            foreach ($miIds as $miId) {
                $mi = MasterIncomings::findOne($miId);
                $mi->final_invoice = $this->id;
                $mi->update(false);
            }
        }
        if ($miIdsUncheck) {
            foreach ($miIdsUncheck as $miId) {
                $mi = MasterIncomings::findOne($miId);
                $mi->final_invoice = NULL;
                $mi->update(false);
            }
        }
        return true;
    }

    public function miUpdateFinalInvoice($mainInvMiId) {
        if ($mainInvMiId) {
            $this->final_invoice = $mainInvMiId[0];
            $this->update(false);
        } else {
            $this->final_invoice = NULL;
            $this->update(false);
        }
        return true;
    }

    // Main Function *****************************************************
    public function miResponse($approval, $remarks) {

        $this->insertWorklist($approval, $remarks);

        // **** If it is requestor's approval, do more checking.        
        if ($this->current_step_task_id == '2') {
            // 1. Update this requestor's approval on mi_project table
            $this->updateRequestorStatus($approval);

            // If ALL requestors approved OR REJECTED, then proceed like normal;
            // If NOT ALL requestors approved, then just return here;
            if (!($this->checkAllRequestorApproved() || $approval == 0)) {
                return true;
            }
        }

        $this->updateStep($approval); // Update Step & Task
        $this->actionOnCurrentTask(); // Work based on the task
        $this->update(false);
        return true; //$this->update(false);
    }

    public function miForceClose($remarks) {
        $approval = 1;
        $this->current_step_task_id = $this->forceCloseTaskId;
        $this->insertWorklist($approval, $remarks);

        $taskList = RefMiMatrices::find()->where("doc_type_id=" . $this->doc_type_id)->orderBy(["step" => SORT_DESC])->one();
        $this->current_step = $taskList->step; // Go to the last step, "DONE"
        $this->current_step_task_id = $taskList->task_id;
        $this->mi_status = $this->miCloseStatusId;  // set to Closed
        // Set claim to reject if force close
        $claim = new \frontend\models\working\claim\ClaimsMaster();
        $claim->rejectClaimDocument($this->id);

        return $this->update(false);
    }

    // Main Function *****************************************************
    // Insert Create a record in worklist, inserting response and remarks
    public function insertWorklist($approval, $remarks) {
        $work = new MiWorklist();
        if (!$work->createNewWorklist($this, $approval, $remarks)) {
//            \yii::$app->session->setFlash("error", "ERROR @ CREATE WORKLIST. CONTACT IT DEPARTMENT IMMEDIATELY");
        }
    }

    public function updateStep($approval) {
        // Check next step based on approval status

        if ($approval == 0) {
            // If reject, then admin keep/Settle the doc
            $taskList = RefMiMatrices::find()->where("doc_type_id=" . $this->doc_type_id)->orderBy(["step" => SORT_DESC])->one();
            $this->current_step = $taskList->step - 1; // Go to the last second step before "DONE"
            $this->current_step_task_id = $this->adminKeepDocTaskId; // $taskList->task_id;

            $claim = new \frontend\models\working\claim\ClaimsMaster();
            $claim->rejectClaimDocument($this->id);
        } else {
            // else get next step task id & update this step task id
            $nextStep = $this->current_step + 1;
            $taskList = RefMiMatrices::find()
                    ->where("doc_type_id=" . $this->doc_type_id)
                    ->andWhere("step=" . $nextStep)
                    ->one();

            if ($taskList) {
                $this->current_step_task_id = $taskList->task_id;
                $this->current_step = $nextStep;
            } else {

                $this->current_step_task_id = $this->doneTaskId;
            }
        }
    }

    public function actionOnCurrentTask() {
        $successFlag = true;

        switch ($this->current_step_task_id) {
            case 1: //"director_approval": to-do: send email to director
//                $successFlag = $this->generate_Mi_Directors_Email();
                break;
            case 2: //"requestor_approval": to-do: send email to requestor
                $this->generate_MI_Requestor_Email();
                break;
            case 3: //"account_payment":  inform account to do payment, how? Email?
                break;
            case 4: //"procurement_approval": 

                break;
            case 5: //"admin_senddoc":

                break;
            case 6: //"account_receivedoc":

                break;
            case 7: //"done":
                if ($this->mi_status != 3) {
                    $this->mi_status = 2;
                }
                break;
            case 8: //"admin_approval":

                break;
            case 13: //acknowledge_director         
                $this->acknowledge_sts = 1;
                $this->update(false);
                $this->updateStep("1");
                $this->actionOnCurrentTask(); // Work based on the task
                $successFlag = $this->update(false);
                break;
            case 14: //acknowledge_requestor        
                $this->acknowledge_req_sts = 1;
                $this->update(false);
                $this->updateStep("1");
                $this->actionOnCurrentTask(); // Work based on the task
                $successFlag = $this->update(false);
                break;
            case 15: //Waiting for claim authorization     
                $claim = \frontend\models\working\claim\ClaimsMaster::find()->where("claims_mi_id=" . $this->id)->one();
                if ($claim->claims_status > 1) {
                    $successFlag = $this->miResponse(1, "Authorized");
                }
                break;
        }
        if (!$successFlag) {
            \yii::$app->session->setFlash("error", "ERROR @ ACTION ON CURRENT TASK. CONTACT IT DEPARTMENT IMMEDIATELY");
        }
    }

    // **
    public function setAcknowledged($task_id) {
        $work = new MiWorklist();
        $work->mi_id = $this->id;
        $work->step = 0;
        $work->task_id = $task_id;
        $work->responsed_by = Yii::$app->user->identity->id;
        $work->approved_flag = 1;
        $work->remarks = 'Acknowledged';
        $work->save();
        if (!$work->save()) {
            \yii::$app->session->setFlash("error", "ERROR @ CREATE WORKLIST. CONTACT IT DEPARTMENT IMMEDIATELY");
        }

        if ($task_id == 13) {
            $this->acknowledge_sts = 2;
            if ($this->update(false)) {
                \common\models\myTools\FlashHandler::suc_stsUpdate();
            }
        } else if ($task_id == 14) {
            $this->updateRequestorStatus(1);
            $this->acknowledge_req_sts = 2;
        }
    }

    public function generate_Mi_Directors_Email() {
        try {

            $actionBtnUrl = \Yii::$app->request->getBaseUrl() . "/?function=emailApprove&idxNo=";
            $subject = "Request for Directors' review: " . $this->index_no;
            $textBody = "<table style='border: 1px solid black' ><tr><td style='border: 1px solid black'>File Number</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                    . $this->index_no
                    . "</td></tr><tr><td style='border: 1px solid black'>File Type</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                    . $this->docType->doc_type_name
                    . "</td></tr><tr><td style='border: 1px solid black'>Project</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                    . $this->projectCode->project_code . " - " . $this->projectCode->project_name
                    . "</td></tr><tr><td style='border: 1px solid black'>View File</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                    . \yii\helpers\Url::base(true) . "/working/mi/get-file?filename=" . urlencode($this->filename)
                    . "</td></tr><tr><td colspan='2'></td><td>"
                    . "<a href='" . $actionBtnUrl . "'><button type='button'>TAKE ACTION</button></a>"
                    . "</td></tr></table>";

            $email = new email();
            return $email->sendEmail_MI_toDirector($subject, $textBody);
        } catch (Exception $e) {
            $err = 'Caught exception: ' . $e->getMessage() . "\n";
            \common\models\myTools\FlashHandler::err("$err Error, kindly contact the IT Department");
        }

        return false;
    }

    public function generate_MI_Requestor_Email() {

        try {
            $requestorEmail = $this->requestor->email;
            if ($requestorEmail != "") {
                $actionBtnUrl = \yii\helpers\Url::base(true) . "/working/mi/requestorreview";
                $subject = "Document awaiting for your review: " . $this->index_no;
                $textBody = "<table style='border: 1px solid black' ><tr><td style='border: 1px solid black'>File Number</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                        . $this->index_no
                        . "</td></tr><tr><td style='border: 1px solid black'>File Type</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                        . $this->docType->doc_type_name
                        . "</td></tr><tr><td style='border: 1px solid black'>Project</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                        . $this->projectCode->project_code . " - " . $this->projectCode->project_name
                        . "</td></tr><tr><td style='border: 1px solid black'>View File</td><td style='border: 1px solid black'>:</td><td style='border: 1px solid black'>"
                        . \yii\helpers\Html::a("View File", \yii\helpers\Url::base(true) . "/working/mi/get-file?filename=" . urlencode($this->filename))
                        . "</td></tr><tr><td colspan='2'></td><td>"
                        . "<a href='" . $actionBtnUrl . "' target='_blank'><button type='button'>TAKE ACTION</button></a>"
                        . "</td></tr></table>";

//            $email = new email();
//            return $email->sendEmail_MI_requestor($subject, $textBody, $requestorEmail);

                Yii::$app->mailer->compose()
                        ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Robot'])
                        ->setTo($requestorEmail)
                        ->setSubject($subject)
                        ->setHtmlBody($textBody)
                        ->send();
            }
        } catch (Exception $e) {
            $err = 'Caught exception: ' . $e->getMessage() . "\n";
            \common\models\myTools\FlashHandler::err("$err Error, kindly contact the IT Department");
        }


        return true;
    }

    public function checkAllRequestorApproved() {
        $count = MiProjects::find()->where(["mi_id" => $this->id])->andWhere("requestor_approval IS NULL OR requestor_approval=0")->count();
        return ($count > 0 ? false : true);
    }

    public function updateRequestorStatus($approval) {
//        $miProj = MiProjects::findByCondition("mi_id=" . $this->id . " AND requestor=" . Yii::$app->user->identity->id)->all();
        return MiProjects::updateAll(['requestor_approval' => $approval], ["mi_id" => $this->id, 'requestor' => Yii::$app->user->identity->id]);
    }

    public function getTotalAmount() {
        $amt = MiProjects::find()->where('mi_id=' . $this->id)->sum('amount');
        return $amt;
    }

    public function getTotalAmount_byProject($projCode) {
        $amt = MiProjects::find()->where('mi_id=' . $this->id)->andWhere(['project_code' => $projCode])->sum('amount');
        return $amt;
    }

}
