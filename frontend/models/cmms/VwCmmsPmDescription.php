<?php

namespace frontend\models\cmms;

use Yii;

/**
 * This is the model class for table "vw_cmms_pm_description".
 *
 * @property int $id
 * @property int|null $cmms_preventive_maintenance_details_id
 * @property int|null $maintenance_category_id
 * @property string|null $remarks
 * @property int|null $cmms_pm_category_desc_id
 * @property string|null $instruction
 */
class VwCmmsPmDescription extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vw_cmms_pm_description';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cmms_preventive_maintenance_details_id', 'maintenance_category_id', 'cmms_pm_category_desc_id'], 'integer'],
            [['remarks', 'instruction'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cmms_preventive_maintenance_details_id' => 'Cmms Preventive Maintenance Details ID',
            'maintenance_category_id' => 'Maintenance Category ID',
            'remarks' => 'Remarks',
            'cmms_pm_category_desc_id' => 'Cmms Pm Category Desc ID',
            'instruction' => 'Instruction',
        ];
    }
}
