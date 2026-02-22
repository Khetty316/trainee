<?php

namespace frontend\models\office\leave;

use Yii;

/**
 * This is the model class for table "ref_leave_section".
 *
 * @property int $leave_section_id
 * @property string|null $leave_section_name
 * @property string $created_at
 * @property string|null $created_by_char
 *
 * @property LeaveMaster[] $leaveMasters
 * @property LeaveMaster[] $leaveMasters0
 */
class RefLeaveSection extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_leave_section';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['created_at'], 'safe'],
            [['leave_section_name'], 'string', 'max' => 100],
            [['created_by_char'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'leave_section_id' => 'Leave Section ID',
            'leave_section_name' => 'Leave Section Name',
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
        return $this->hasMany(LeaveMaster::className(), ['end_section' => 'leave_section_id']);
    }

    /**
     * Gets query for [[LeaveMasters0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters0() {
        return $this->hasMany(LeaveMaster::className(), ['start_section' => 'leave_section_id']);
    }
    
    
    public static function getDropDownList() {
        return \yii\helpers\ArrayHelper::map(RefLeaveSection::find()->orderBy(['leave_section_name'=>SORT_ASC])->all(), "leave_section_id", "leave_section_name");
    }

}
