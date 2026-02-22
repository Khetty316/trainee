<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "v_leave_entitlement_dashboard".
 *
 * @property string|null $staff_id
 * @property int|null $entitle_id
 * @property int $user_id
 * @property string|null $fullname
 * @property float|null $annual_bring_forward_days
 * @property float|null $annual_bring_next_year_days
 * @property int|null $year
 * @property float|null $annual_year
 * @property float|null $annual_current
 * @property float|null $sick_year
 * @property float|null $other_year
 */
class VLeaveEntitlementDashboard extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_leave_entitlement_dashboard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['entitle_id', 'user_id', 'year'], 'integer'],
            [['annual_bring_forward_days', 'annual_bring_next_year_days', 'annual_year', 'annual_current', 'sick_year', 'other_year'], 'number'],
            [['staff_id'], 'string', 'max' => 10],
            [['fullname'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'staff_id' => 'Staff ID',
            'entitle_id' => 'Entitle ID',
            'user_id' => 'User ID',
            'fullname' => 'Fullname',
            'annual_bring_forward_days' => 'Annual Bring Forward Days',
            'annual_bring_next_year_days' => 'Annual Bring Next Year Days',
            'year' => 'Year',
            'annual_year' => 'Annual Year',
            'annual_current' => 'Annual Current',
            'sick_year' => 'Sick Year',
            'other_year' => 'Other Year',
        ];
    }

}
