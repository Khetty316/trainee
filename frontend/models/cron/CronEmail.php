<?php

namespace frontend\models\cron;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;
use frontend\models\working\todo\VTodoMaster;
use frontend\models\working\todo\TodoMaster;
use frontend\models\office\leave\VMasterLeave;
use frontend\models\office\leave\LeaveMaster;
use common\models\myTools\MyFormatter;

/**
 * This is the model class for table "cron_email".
 *
 * @property int $id
 * @property int $active_sts
 * @property int $emailed
 * @property string|null $emailed_at
 * @property string|null $sender_name
 * @property string $email_to
 * @property string $email_subject
 * @property string $email_content
 * @property string|null $controller
 * @property int|null $start_date_time
 * @property string|null $sdt_timestamp
 * @property string|null $created_at
 * @property int|null $created_by
 */
class CronEmail extends \yii\db\ActiveRecord {

    CONST emailSenderName = "Tenaga Kenari Digital Management System";
    CONST ITTeamEmail = "paul.ling@npl.com.my";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'cron_email';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['active_sts', 'emailed', 'start_date_time', 'created_by'], 'integer'],
            [['emailed_at', 'sdt_timestamp', 'created_at'], 'safe'],
            [['email_to', 'email_subject', 'email_content'], 'required'],
            [['email_content'], 'string'],
            [['sender_name', 'email_to', 'email_subject', 'controller'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'active_sts' => 'Active Sts',
            'emailed' => 'Emailed',
            'emailed_at' => 'Emailed At',
            'sender_name' => 'Sender Name',
            'email_to' => 'Email To',
            'email_subject' => 'Email Subject',
            'email_content' => 'Email Content',
            'controller' => 'Controller',
            'start_date_time' => 'Start Date Time',
            'sdt_timestamp' => 'Sdt Timestamp',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * General functions to create email record
     * @param type $receiverEmail
     * @param type $emailSubject
     * @param type $emailContent
     */
    public static function newEmailRecord($receiverEmail, $emailSubject, $emailContent) {
        $cronEmail = new CronEmail();
        $cronEmail->active_sts = 1;
        $cronEmail->emailed = 0;
        $cronEmail->sender_name = $cronEmail::emailSenderName;
        $cronEmail->email_to = is_array($receiverEmail) ? implode(",", $receiverEmail) : $receiverEmail;
        $cronEmail->email_subject = $emailSubject;
        $cronEmail->email_content = $emailContent;
        if (!$cronEmail->validate()) {
            \common\models\myTools\Mydebug::dumpFileA($cronEmail->errors);
        }
        return $cronEmail->save();
    }

    public static function emailSystemRequestNew($systemRequestMaster) {
        $cronEmail = new CronEmail();
        $cronEmail->active_sts = 1;
        $cronEmail->emailed = 0;
        $cronEmail->sender_name = $cronEmail::emailSenderName;
        $cronEmail->email_to = $cronEmail::ITTeamEmail;
        $cronEmail->email_subject = "New system support request " . $systemRequestMaster->id . " submitted by " . $systemRequestMaster->requestedBy->fullname;
        $actionBtnUrl = \yii\helpers\Url::base(true) . "/systemrequest/view-dev?id=" . $systemRequestMaster->id;
        $cronEmail->email_content = "<table>"
                . "<tr><td>Request Type</td><td>:</td><td>" . $systemRequestMaster->sysReqType->name . "</td></tr>"
                . "<tr><td>Urgent</td><td>:</td><td>" . ($systemRequestMaster->is_urgent ? "Yes" : "No") . "</td></tr>"
                . "<tr style='vertical-align:top'><td>Description</td><td>:</td><td>" . nl2br(Html::encode($systemRequestMaster->description)) . "</td></tr>"
                . "<tr><td colspan='2'></td><td>"
                . "<a href='" . $actionBtnUrl . "' target='_blank'><button type='button'>View</button></a>"
                . "</td></tr>"
                . "</table>";
        $cronEmail->save();
    }

    public function sendEmail() {
        try {
            return Yii::$app->mailer->compose()
                            ->setFrom([Yii::$app->params['cronEmail'] => $this->sender_name])
                            ->setTo(explode(",", $this->email_to))
                            ->setSubject($this->email_subject)
                            ->setHtmlBody($this->email_content)
                            ->send();
        } catch (Exception $e) {
            $err = 'Caught exception: ' . $e->getMessage() . "\n";
            \common\models\myTools\MyLogTxt::dumpFileA($err);
        }
        return false;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////****    _Todo Master_    ****

    public static function todoApproverTextBody($modulename, $remark, $createdby, $datetime, $url) {
        return "<table border='1' style='border-collapse: collapse; border-color: black;'>"
                . "<tr><td><strong>Project Module:</strong></td><td>" . $modulename . "</td></tr>"
                . "<tr><td><strong>Remarks:</strong></td><td>" . Html::encode($remark) . "</td></tr>"
                . "<tr><td><strong>Requested by:</strong></td><td>" . $createdby . "</td></tr>"
                . "<tr><td><strong>Date and Time:</strong></td><td>" . MyFormatter::asDateTime_Read($datetime) . "</td></tr>"
                . "<tr><td><strong>Webpage:</strong></td><td><a href='" . $url . "' target='_blank'>Click here</a> to go to the webpage.</td></tr>"
                . "</table>";
    }

    public static function todoApproverReplyTextBody($modulename, $projectcode, $approver, $datetime, $url) {
        return "<table border='1' style='border-collapse: collapse; border-color: black;'>"
                . "<tr><td><strong>Project Module:</strong></td><td>" . $modulename . " - " . $projectcode . "</td></tr>"
                . "<tr><td><strong>Approved by:</strong></td><td>" . $approver . "</td></tr>"
                . "<tr><td><strong>Date and Time:</strong></td><td>" . MyFormatter::asDateTime_Read($datetime) . "</td></tr>"
                . "<tr><td><strong>Webpage:</strong></td><td><a href='" . $url . "' target='_blank'>Click here</a> to go to the webpage.</td></tr>"
                . "</table>";
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////****    _Todo - Purchase Order_    ****

    public function bodyPoApproverNotice($id, $todoId) {
        $user = User::findOne($id);
        $todoModel = VTodoMaster::findOne($todoId);
        $url = Url::base(true) . "/working/todo/approve-purchase-order?id=" . $todoId;
        $receiver = $user->email;
        $subject = "Purchase Order " . $todoModel->project_code . " - Needs Approval (Code:" . $todoModel->id . ")";
        $textBody = self::todoApproverTextBody($todoModel->todo_module_name, $todoModel->remark, $todoModel->created_by_name, $todoModel->created_at, $url);
        return self::newEmailRecord($receiver, $subject, $textBody);
    }

    public function bodyPoApproverReply($id) {
        $todoModel = TodoMaster::findOne($id);
        $poModel = $todoModel->purchaseOrderMasters;
        $vTodoModel = VTodoMaster::findOne($id);
        $user = User::findOne($todoModel->created_by);
        $url = Url::base(true) . "/working/po/view?id=" . $poModel[0]->po_id;
        $receiver = $user->email;
        $subject = "(APPROVED) " . $vTodoModel->todo_module_name . " " . $poModel[0]->project_code . " (Code:" . $todoModel->id . ")";
        $textBody = self::todoApproverReplyTextBody($vTodoModel->todo_module_name, $poModel[0]->project_code, $poModel[0]->poApprover->fullname, $todoModel->updated_at, $url);
        return self::newEmailRecord($receiver, $subject, $textBody);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////****    _Todo - Project Progress Claim(Main)_    ****

    public function bodyProgressClaimApproverNotice($id, $todoId) {
        $user = User::findOne($id);
        $todoModel = VTodoMaster::findOne($todoId);
        $url = Url::base(true) . "/working/todo/approve-progress-claim-main?id=" . $todoId;
        $receiver = $user->email;
        $subject = "Project Progress Claim (Outgoing) " . $todoModel->project_code . " - Requesting Claim (Code:" . $todoModel->id . ")";
        $textBody = self::todoApproverTextBody($todoModel->todo_module_name, $todoModel->remark, $todoModel->created_by_name, $todoModel->created_at, $url);
        return CronEmail::newEmailRecord($receiver, $subject, $textBody);
    }

    public function bodyProgressClaimApproverReply($id) {
        $todoModel = TodoMaster::findOne($id);
        $project = $todoModel->projectProgressClaims;
        $vTodoModel = VTodoMaster::findOne($id);
        $user = User::findOne($todoModel->created_by);
        $url = Url::base(true) . "/working/project/view-progress-claim-main?id=" . $project[0]->project_id;
        $receiver = $user->email;
        $subject = "(APPROVED) " . $vTodoModel->todo_module_name . " " . $project[0]->project->proj_code . " - " . $project[0]->submit_reference . " - (Code:" . $todoModel->id . ")";
        $textBody = self::todoApproverReplyTextBody($vTodoModel->todo_module_name, $project[0]->project->proj_code, $project[0]->submitFileApprover->fullname, $todoModel->updated_at, $url);
        return CronEmail::newEmailRecord($receiver, $subject, $textBody);
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////****    _Leave Master_    ****

    public static function leaveApproverTextBody($requestor, $reason, $startDate, $endDate, $relief, $url) {
        return "<table border='1' style='border-collapse: collapse; border-color: black;'>"
                . "<tr><td><strong>Requestor:</strong></td><td>" . Html::encode($requestor) . "</td></tr>"
                . "<tr><td><strong>Reason:</strong></td><td>" . Html::encode($reason) . "</td></tr>"
                . "<tr><td><strong>Date:</strong></td><td> From " . MyFormatter::asDate_Read($startDate) . " to " . MyFormatter::asDate_Read($endDate) . "</td></tr>"
                . "<tr><td><strong>Relief:</strong></td><td>" . Html::encode($relief) . "</td></tr>"
//                . "<tr><td><strong>Webpage:</strong></td><td><a href='" . $url . "' target='_blank'>Click here</a> to go to the webpage.</td></tr>"
                . "</table>";
    }

    public static function leaveApproverResponseTextBody($status, $startDate, $endDate, $url, $remark) {
        return "<table border='1' style='border-collapse: collapse; border-color: black;'>"
                . "<tr><td><strong>Application status:</strong></td><td>" . $status . "</td></tr>"
                . "<tr><td><strong>Remarks:</strong></td><td>" . $remark . "</td></tr>"
                . "<tr><td><strong>Date:</strong></td><td> From " . MyFormatter::asDate_Read($startDate) . " to " . MyFormatter::asDate_Read($endDate) . "</td></tr>"
//                . "<tr><td><strong>Webpage:</strong></td><td><a href='" . $url . "' target='_blank'>Click here</a> to go to the webpage.</td></tr>"
                . "</table>";
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////****    _Leave - Send Application for Approval_    ****

    public function bodyLeaveRequestRelief($id, $type) {
        $leaveModel = VMasterLeave::findOne($id);
        $user = User::findOne($leaveModel->relief_user_id);
        $receiver = $user->email;
        $url = Url::base(true) . "/working/leavemgmt/relief-leave-approval";
        $subject = $leaveModel->requestor . " requesting for " . $type . " (" . MyFormatter::asDate_Read($leaveModel->start_date) . "-" . MyFormatter::asDate_Read($leaveModel->end_date) . ")";
        $textBody = self::leaveApproverTextBody($leaveModel->requestor, $leaveModel->reason, $leaveModel->start_date, $leaveModel->end_date, $leaveModel->relief, $url);
        return CronEmail::newEmailRecord($receiver, $subject, $textBody);
    }

    public function bodyLeaveRequestSuperior($id, $type) {
        $leaveModel = VMasterLeave::findOne($id);
        $url = Url::base(true) . "/working/leavemgmt/superior-leave-approval";
        $receiver = $leaveModel->superior_email;
        $subject = $leaveModel->requestor . " requesting for " . $type . " (" . MyFormatter::asDate_Read($leaveModel->start_date) . "-" . MyFormatter::asDate_Read($leaveModel->end_date) . ")";
        $textBody = self::leaveApproverTextBody($leaveModel->requestor, $leaveModel->reason, $leaveModel->start_date, $leaveModel->end_date, $leaveModel->relief, $url);
        return CronEmail::newEmailRecord($receiver, $subject, $textBody);
    }

    public function bodyLeaveRequestHr($id, $type) {
        $leaveModel = VMasterLeave::findOne($id);
        $systemConfig = \frontend\models\common\RefSystemConfig::getValue_defaultHrEmail();
        $receiver = $systemConfig->value;
        $url = Url::base(true) . "/working/leavemgmt/hr-leave-approval";
        $subject = $leaveModel->requestor . " requesting for " . $type . " (" . MyFormatter::asDate_Read($leaveModel->start_date) . "-" . MyFormatter::asDate_Read($leaveModel->end_date) . ")";
        $textBody = self::leaveApproverTextBody($leaveModel->requestor, $leaveModel->reason, $leaveModel->start_date, $leaveModel->end_date, $leaveModel->relief, $url);
        return CronEmail::newEmailRecord($receiver, $subject, $textBody);
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////****    _Leave - Response to Application_    ****

    public function bodyLeaveApplicationResponse($id, $status, $remark) {
        $leaveModel = VMasterLeave::findOne($id);
        $receiver = $leaveModel->requestor_email;
        $subject = $status . " on (" . MyFormatter::asDate_Read($leaveModel->start_date) . "-" . MyFormatter::asDate_Read($leaveModel->end_date) . ")";
        $url = Url::base(true) . "/office/leave/personal-leave";
        $textBody = self::leaveApproverResponseTextBody($status, $leaveModel->start_date, $leaveModel->end_date, $url, $remark);

        return CronEmail::newEmailRecord($receiver, $subject, $textBody);
    }

}
