<?php

namespace frontend\models\working\leavemgmt;

use Yii;
use common\models\User;
/**
 * This is the model class for table "leave_delegate_list".
 *
 * @property int $id
 * @property int $leave_id
 * @property int $delegate_from_user
 * @property int $delegate_to_user
 * @property string|null $remark
 * @property string $create_at
 *
 * @property User $delegateFromUser
 * @property User $delegateToUser
 */
class LeaveDelegateList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'leave_delegate_list';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['leave_id', 'delegate_from_user', 'delegate_to_user'], 'required'],
            [['leave_id', 'delegate_from_user', 'delegate_to_user'], 'integer'],
            [['remark'], 'string'],
            [['create_at'], 'safe'],
            [['delegate_from_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['delegate_from_user' => 'id']],
            [['delegate_to_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['delegate_to_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'leave_id' => 'Leave ID',
            'delegate_from_user' => 'Delegate From User',
            'delegate_to_user' => 'Delegate To User',
            'remark' => 'Remark',
            'create_at' => 'Create At',
        ];
    }

    /**
     * Gets query for [[DelegateFromUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDelegateFromUser()
    {
        return $this->hasOne(User::className(), ['id' => 'delegate_from_user']);
    }

    /**
     * Gets query for [[DelegateToUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDelegateToUser()
    {
        return $this->hasOne(User::className(), ['id' => 'delegate_to_user']);
    }
}
