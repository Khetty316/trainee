<?php

namespace frontend\models\office\leave;

use Yii;

/**
 * This is the model class for table "ref_leave_status".
 *
 * @property int $leave_sts_id
 * @property string|null $leave_status_name
 * @property string|null $remark
 * @property string $created_at
 * @property string|null $created_by_char
 *
 * @property LeaveMaster[] $leaveMasters
 */
class RefLeaveStatus extends \yii\db\ActiveRecord {

    CONST STS_ActiveList = [1, 2, 3, 4, 5];
    CONST STS_InactiveList = [6, 7, 8];
    CONST STS_Pending = [1, 2, 3, 5];
    CONST STS_APPROVED = 4;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_leave_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['remark'], 'string'],
            [['created_at'], 'safe'],
            [['leave_status_name'], 'string', 'max' => 100],
            [['created_by_char'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'leave_sts_id' => 'Leave Sts ID',
            'leave_status_name' => 'Leave Status Name',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'created_by_char' => 'Created By Char',
        ];
    }

    /**
     * Gets query for [[LeaveMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters() {
        return $this->hasMany(LeaveMaster::className(), ['leave_status' => 'leave_sts_id']);
    }

    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefLeaveStatus::find()->orderBy(['remark' => SORT_ASC])->all(), "leave_sts_id", "remark");
    }

    public static function getDropDownListFiltered() {
        $filteredData = RefLeaveStatus::find()
                ->where(['not', ['leave_sts_id' => [1, 2, 3, 5, 6, 8, 9]]])
                ->orderBy(['remark' => SORT_ASC])
                ->all();

        return \yii\helpers\ArrayHelper::map($filteredData, "leave_sts_id", "remark");
    }

}
