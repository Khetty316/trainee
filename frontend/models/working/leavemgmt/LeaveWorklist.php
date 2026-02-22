<?php

namespace frontend\models\working\leavemgmt;

use Yii;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\leave\RefLeaveStatus;
use common\models\User;
/**
 * This is the model class for table "leave_worklist".
 *
 * @property int $leave_worklist_id
 * @property int $leave_id
 * @property int|null $leave_status
 * @property int|null $responsed_by
 * @property int $approved_flag
 * @property string|null $remarks
 * @property string $created_at
 *
 * @property LeaveMaster $leave
 * @property RefLeaveStatus $leaveStatus
 * @property User $responsedBy
 */
class LeaveWorklist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_worklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['leave_id'], 'required'],
            [['leave_id', 'leave_status', 'responsed_by', 'approved_flag'], 'integer'],
            [['remarks'], 'string'],
            [['created_at'], 'safe'],
            [['leave_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeaveMaster::className(), 'targetAttribute' => ['leave_id' => 'id']],
            [['leave_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveStatus::className(), 'targetAttribute' => ['leave_status' => 'leave_sts_id']],
            [['responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responsed_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'leave_worklist_id' => 'Leave Worklist ID',
            'leave_id' => 'Leave ID',
            'leave_status' => 'Leave Status',
            'responsed_by' => 'Responsed By',
            'approved_flag' => 'Approved Flag',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Leave]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeave() {
        return $this->hasOne(LeaveMaster::className(), ['id' => 'leave_id']);
    }

    /**
     * Gets query for [[LeaveStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveStatus() {
        return $this->hasOne(RefLeaveStatus::className(), ['leave_sts_id' => 'leave_status']);
    }

    /**
     * Gets query for [[ResponsedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsedBy() {
        return $this->hasOne(User::className(), ['id' => 'responsed_by']);
    }

}
