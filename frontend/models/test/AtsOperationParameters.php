<?php

namespace frontend\models\test;

use Yii;

/**
 * This is the model class for table "ats_operation_parameters".
 *
 * @property int $id
 * @property int|null $form_ats_id
 * @property string|null $parameter_code
 * @property string|null $parameter_name
 * @property string|null $parameter_category
 * @property int|null $tier_level
 * @property string|null $parent_group
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property AtsOperationMatrix[] $atsOperationMatrices
 * @property TestFormAts $formAts
 * @property User $createdBy
 * @property User $updatedBy
 */
class AtsOperationParameters extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ats_operation_parameters';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'form_ats_id', 'tier_level', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['parameter_code', 'parameter_name', 'parameter_category', 'parent_group'], 'string', 'max' => 255],
            [['id'], 'unique'],
            [['form_ats_id'], 'exist', 'skipOnError' => true, 'targetClass' => TestFormAts::className(), 'targetAttribute' => ['form_ats_id' => 'id']],
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
            'parameter_code' => 'Parameter Code',
            'parameter_name' => 'Parameter Name',
            'parameter_category' => 'Parameter Category',
            'tier_level' => 'Tier Level',
            'parent_group' => 'Parent Group',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AtsOperationMatrices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAtsOperationMatrices()
    {
        return $this->hasMany(AtsOperationMatrix::className(), ['parameter_id' => 'id']);
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
