<?php

namespace frontend\models\working\leavemgmt;

use Yii;
use frontend\models\office\leave\RefLeaveType;

/**
 * This is the model class for table "leave_entitlement_details".
 *
 * @property int $id
 * @property int $leave_entitle_id
 * @property int $month_start
 * @property int $month_end
 * @property string|null $leave_type_code
 * @property float $days
 * @property string|null $created_at
 * @property int|null $created_by
 *
 * @property LeaveEntitlement $leaveEntitle
 * @property RefLeaveType $leaveTypeCode
 */
class LeaveEntitlementDetails extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'leave_entitlement_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['leave_entitle_id'], 'required'],
            [['leave_entitle_id', 'month_start', 'month_end', 'created_by'], 'integer'],
            [['days'], 'number'],
            [['created_at'], 'safe'],
            [['leave_type_code'], 'string', 'max' => 10],
            [['leave_entitle_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeaveEntitlement::className(), 'targetAttribute' => ['leave_entitle_id' => 'id']],
            [['leave_type_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefLeaveType::className(), 'targetAttribute' => ['leave_type_code' => 'leave_type_code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'leave_entitle_id' => 'Leave Entitle ID',
            'month_start' => 'Month Start',
            'month_end' => 'Month End',
            'leave_type_code' => 'Leave Type Code',
            'days' => 'Days',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[LeaveEntitle]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveEntitle() {
        return $this->hasOne(LeaveEntitlement::className(), ['id' => 'leave_entitle_id']);
    }

    /**
     * Gets query for [[LeaveTypeCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveTypeCode() {
        return $this->hasOne(RefLeaveType::className(), ['leave_type_code' => 'leave_type_code']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

}
