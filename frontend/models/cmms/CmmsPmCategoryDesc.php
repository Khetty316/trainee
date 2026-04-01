<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "cmms_pm_category_desc".
 *
 * @property int $id
 * @property int|null $cmms_pm_category_id
 * @property int|null $ref_pm_category_id
 * @property string|null $instruction
 * @property int|null $yes_no
 * @property string|null $check_status
 * @property string|null $observation_reading
 * @property int|null $pass_fail
 *
 * @property CmmsPreventiveMaintenanceDetails $cmmsPmCategory
 * @property RefPmCategory $refPmCategory
 */
class CmmsPmCategoryDesc extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cmms_pm_category_desc';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cmms_pm_category_id', 'ref_pm_category_id', 'yes_no', 'pass_fail'], 'integer'],
            [['instruction', 'check_status', 'observation_reading'], 'string', 'max' => 255],
            [['cmms_pm_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmmsPreventiveMaintenanceDetails::className(), 'targetAttribute' => ['cmms_pm_category_id' => 'id']],
            [['ref_pm_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefPmCategory::className(), 'targetAttribute' => ['ref_pm_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cmms_pm_category_id' => 'Cmms Pm Category ID',
            'ref_pm_category_id' => 'Ref Pm Category ID',
            'instruction' => 'Instruction',
            'yes_no' => 'Yes No',
            'check_status' => 'Check Status',
            'observation_reading' => 'Observation Reading',
            'pass_fail' => 'Pass Fail',
        ];
    }

    /**
     * Gets query for [[CmmsPmCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmmsPmCategory()
    {
        return $this->hasOne(CmmsPreventiveMaintenanceDetails::className(), ['id' => 'cmms_pm_category_id']);
    }

    /**
     * Gets query for [[RefPmCategory]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefPmCategory()
    {
        return $this->hasOne(RefPmCategory::className(), ['id' => 'ref_pm_category_id']);
    }
}
