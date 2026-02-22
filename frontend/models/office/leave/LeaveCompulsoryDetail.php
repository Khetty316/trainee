<?php

namespace frontend\models\office\leave;

use Yii;
use common\models\User;
use frontend\models\office\leave\LeaveMaster;
use frontend\models\office\leave\LeaveCompulsoryMaster;

/**
 * This is the model class for table "leave_compulsory_detail".
 *
 * @property int $id
 * @property int|null $compulsory_master_id
 * @property int|null $user_id
 *
 * @property LeaveCompulsoryMaster $compulsoryMaster
 * @property User $user
 * @property LeaveMaster[] $leaveMasters
 */
class LeaveCompulsoryDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_compulsory_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['compulsory_master_id', 'user_id'], 'integer'],
            [['compulsory_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeaveCompulsoryMaster::className(), 'targetAttribute' => ['compulsory_master_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'compulsory_master_id' => 'Compulsory Master ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * Gets query for [[CompulsoryMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompulsoryMaster()
    {
        return $this->hasOne(LeaveCompulsoryMaster::className(), ['id' => 'compulsory_master_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[LeaveMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters()
    {
        return $this->hasMany(LeaveMaster::className(), ['compulsory_leave' => 'id']);
    }
}
