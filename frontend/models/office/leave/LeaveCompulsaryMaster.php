<?php

namespace frontend\models\office\leave;

use Yii;

/**
 * This is the model class for table "leave_compulsary_master".
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
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $approvalBy
 * @property RefLeaveStatus $status0
 * @property User $requestor0
 */
class LeaveCompulsaryMaster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_compulsary_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['requestor', 'days', 'requestordays', 'status', 'approval_by', 'updated_by'], 'integer'],
            [['created_at', 'start_date', 'end_date', 'updated_at'], 'safe'],
            [['requestor_remark', 'approval_remark'], 'string', 'max' => 255],
            [['approval_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['approval_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveStatus::className(), 'targetAttribute' => ['status' => 'leave_sts_id']],
            [['requestor'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'requestor' => 'Requestor',
            'created_at' => 'Created At',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'days' => 'Days',
            'requestordays' => 'Requestordays',
            'requestor_remark' => 'Requestor Remark',
            'status' => 'Status',
            'approval_by' => 'Approval By',
            'approval_remark' => 'Approval Remark',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
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
