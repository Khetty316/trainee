<?php

namespace frontend\models\office\preReqForm;

use common\models\User;

use Yii;

/**
 * This is the model class for table "prereq_form_item_worklist".
 *
 * @property int $id
 * @property int|null $prereq_form_master_id
 * @property int $prereq_form_item_id
 * @property int|null $status 0 = approved, 1 = rejected
 * @property string|null $remark reason reject
 * @property int|null $responded_by
 * @property string|null $created_at
 *
 * @property PrereqFormMaster $prereqFormMaster
 * @property User $respondedBy
 * @property PrereqFormItem $prereqFormItem
 */
class PrereqFormItemWorklist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prereq_form_item_worklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prereq_form_master_id', 'prereq_form_item_id', 'status', 'responded_by'], 'integer'],
            [['prereq_form_item_id'], 'required'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['prereq_form_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormMaster::className(), 'targetAttribute' => ['prereq_form_master_id' => 'id']],
            [['responded_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responded_by' => 'id']],
            [['prereq_form_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => PrereqFormItem::className(), 'targetAttribute' => ['prereq_form_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prereq_form_master_id' => 'Prereq Form Master ID',
            'prereq_form_item_id' => 'Prereq Form Item ID',
            'status' => 'Status',
            'remark' => 'Remark',
            'responded_by' => 'Responded By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[PrereqFormMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormMaster() {
        return $this->hasOne(PrereqFormMaster::className(), ['id' => 'prereq_form_master_id']);
    }

    /**
     * Gets query for [[RespondedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRespondedBy() {
        return $this->hasOne(User::className(), ['id' => 'responded_by']);
    }

    /**
     * Gets query for [[PrereqFormItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormItem() {
        return $this->hasOne(PrereqFormItem::className(), ['id' => 'prereq_form_item_id']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->responded_by = Yii::$app->user->identity->id;
        return parent::beforeSave($insert);
    }
}
