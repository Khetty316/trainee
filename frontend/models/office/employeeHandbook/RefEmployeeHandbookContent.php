<?php

namespace frontend\models\office\employeeHandbook;

use Yii;

/**
 * This is the model class for table "ref_employee_handbook_content".
 *
 * @property string $code
 * @property string|null $name
 * @property int|null $is_active 0 = no, 1 = yes
 * @property int|null $order
 * @property int|null $grade 0 = nonexec, 1 = exec
 */
class RefEmployeeHandbookContent extends \yii\db\ActiveRecord
{
    CONST TRAVEL_ALLOWANCE_CODE = 'travellingAllowance';
    CONST OUTPATIENT_MEDICAL_CODE = 'outpatientMedical';
    CONST EXEC_OT_MEAL_CODE = 'execOtMeal';
     
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ref_employee_handbook_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
            [['is_active', 'order', 'grade'], 'integer'],
            [['code'], 'string', 'max' => 100],
            [['name'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'code' => 'Code',
            'name' => 'Name',
            'is_active' => 'Is Active',
            'order' => 'Order',
            'grade' => 'Grade',
        ];
    }
}
