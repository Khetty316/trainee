<?php

namespace frontend\models\office\preReqForm;

use Yii;

/**
 * This is the model class for table "prereq_form_status_trail".
 *
 * @property int $id
 * @property int $prereq_form_master_id
 * @property int $status
 * @property int $responded_by
 * @property string|null $remark
 * @property string|null $created_at
 *
 * @property PrereqFormMaster $prereqFormMaster
 * @property RefGeneralStatus $status0
 * @property User $respondedBy
 */
class PrereqFormStatusTrail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prereq_form_status_trail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prereq_form_master_id', 'status', 'responded_by'], 'required'],
            [['prereq_form_master_id', 'status', 'responded_by'], 'integer'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['prereq_form_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormMaster::className(), 'targetAttribute' => ['prereq_form_master_id' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['responded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responded_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prereq_form_master_id' => 'Prereq Form Master ID',
            'status' => 'Status',
            'responded_by' => 'Responded By',
            'remark' => 'Remark',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[PrereqFormMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormMaster()
    {
        return $this->hasOne(PrereqFormMaster::className(), ['id' => 'prereq_form_master_id']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0()
    {
        return $this->hasOne(RefGeneralStatus::className(), ['id' => 'status']);
    }

    /**
     * Gets query for [[RespondedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRespondedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'responded_by']);
    }
}
