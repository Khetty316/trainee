<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ats_operation_matrix".
 *
 * @property int $id
 * @property int|null $form_ats_id
 * @property int|null $scenario_id
 * @property int|null $parameter_id
 * @property int|null $parameter_value 0 = close, 1 = open, 2 = none
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property TestFormAts $formAts
 * @property AtsOperationScenario $scenario0
 * @property AtsOperationParameters $parameter
 * @property User $createdBy
 * @property User $updatedBy
 */
class AtsOperationMatrix extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ats_operation_matrix';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'form_ats_id', 'scenario_id', 'parameter_id', 'parameter_value', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['id'], 'unique'],
            [['form_ats_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormAts::className(), 'targetAttribute' => ['form_ats_id' => 'id']],
            [['scenario_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtsOperationScenario::className(), 'targetAttribute' => ['scenario_id' => 'id']],
            [['parameter_id'], 'exist', 'skipOnError' => true, 'targetClass' => AtsOperationParameters::className(), 'targetAttribute' => ['parameter_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'form_ats_id' => 'Form Ats ID',
            'scenario_id' => 'Scenario ID',
            'parameter_id' => 'Parameter ID',
            'parameter_value' => 'Parameter Value',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[FormAts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFormAts()
    {
        return $this->hasOne(TestFormAts::className(), ['id' => 'form_ats_id']);
    }

    /**
     * Gets query for [[Scenario0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getScenario0()
    {
        return $this->hasOne(AtsOperationScenario::className(), ['id' => 'scenario_id']);
    }

    /**
     * Gets query for [[Parameter]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParameter()
    {
        return $this->hasOne(AtsOperationParameters::className(), ['id' => 'parameter_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
