<?php

namespace frontend\models\working\leavemgmt;

use Yii;

/**
 * This is the model class for table "v_leave_entitlement_summary".
 *
 * @property string|null $staff_id
 * @property int|null $entitle_id
 * @property int $user_id
 * @property string|null $fullname
 * @property float|null $annual_bring_forward_days
 * @property string $leave_type_code
 * @property string|null $leave_type_name
 * @property int|null $is_pro_rata
 * @property int|null $YEAR
 * @property float|null $cur_entitle
 * @property float|null $year_end_entitle
 */
class VLeaveEntitlementSummary extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_leave_entitlement_summary';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['entitle_id', 'user_id', 'is_pro_rata', 'YEAR'], 'integer'],
            [['annual_bring_forward_days', 'cur_entitle', 'year_end_entitle'], 'number'],
            [['leave_type_code'], 'required'],
            [['staff_id', 'leave_type_code'], 'string', 'max' => 10],
            [['fullname', 'leave_type_name'], 'string', 'max' => 255],
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
            'leave_type_code' => 'Leave Type Code',
            'leave_type_name' => 'Leave Type Name',
            'is_pro_rata' => 'Is Pro Rata',
            'YEAR' => 'Year',
            'cur_entitle' => 'Cur Entitle',
            'year_end_entitle' => 'Year End Entitle',
        ];
    }

}
