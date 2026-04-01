<?php //

namespace frontend\models\office\leave;

use Yii;
use common\models\User;
use frontend\models\office\leave\LeaveCompulsoryDetail;
use frontend\models\office\leave\RefLeaveStatus;

/**
 * This is the model class for table "leave_compulsory_master".
 *
 * @property int $id
 * @property int|null $requestor
 * @property string|null $created_at
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $days
 * @property int|null $requestordays
 * @property string|null $requestor_remark
 * @property int|null $status
 * @property int|null $approval_by
 * @property string|null $approval_remark
 * @property string|null $approved_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property LeaveCompulsoryDetail[] $leaveCompulsoryDetails
 * @property User $approvalBy
 * @property RefLeaveStatus $status0
 * @property User $requestor0
 */
class LeaveCompulsoryMaster extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_compulsory_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['start_date', 'end_date', 'requestor_remark'], 'required'],
            [['requestor', 'status', 'days', 'updated_at', 'updated_by'], 'safe'],
            [['days', 'requestor', 'status', 'approval_by', 'updated_by'], 'integer'],
            [['requestor_remark', 'approval_remark'], 'string'],
            [['requestor'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor' => 'id']],
            [['approval_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approval_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveStatus::className(), 'targetAttribute' => ['status' => 'leave_sts_id']],
            [['requestor'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'requestor' => 'Requestor',
            'created_at' => 'Created At',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'days' => 'Total Day/s',
            'requestordays' => 'Requestordays',
            'requestor_remark' => 'Reason',
            'status' => 'Status',
            'approval_by' => 'Decision By',
            'approval_remark' => "Director's Remark",
            'approved_at' => 'Decision Date',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[LeaveCompulsoryDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveCompulsoryDetails()
    {
        return $this->hasMany(LeaveCompulsoryDetail::className(), ['compulsory_master_id' => 'id']);
    }

    /**
     * Gets query for [[ApprovalBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApprovalBy()
    {
        return $this->hasOne(User::className(), ['id' => 'approval_by']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(RefLeaveStatus::className(), ['leave_sts_id' => 'status']);
    }

    /**
     * Gets query for [[Requestor0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor0()
    {
        return $this->hasOne(User::className(), ['id' => 'requestor']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

}
