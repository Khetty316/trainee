<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "v_user".
 *
 * @property int $id
 * @property string|null $staff_id
 * @property string $username
 * @property string $email
 * @property int $status
 * @property string|null $fullname
 * @property string|null $contact_no
 * @property string|null $ic_no
 * @property string|null $address
 * @property int|null $postcode
 * @property string|null $address_line_2
 * @property int|null $area_id
 * @property string|null $area_name
 * @property string|null $state_name
 * @property string|null $emergency_contact_no
 * @property string|null $emergency_contact_person
 * @property int|null $ethnic_id
 * @property string|null $ethnic_name
 * @property int|null $religion_id
 * @property string|null $religion_name
 * @property string|null $sex
 * @property string|null $sex_name
 * @property string|null $dob
 * @property int|null $is_leave_superior
 * @property int $skip_claim_authorize
 * @property int $leave_adjusted 0 for new staff. To state that when the annual leave start to count
 * @property int|null $designation
 * @property string|null $design_name
 * @property string|null $staff_type
 * @property string|null $employment_type
 * @property string|null $company_code
 * @property string|null $company_name
 * @property string|null $date_of_join
 * @property float|null $epf_percent
 * @property int|null $superior_id
 * @property string|null $superior_name
 * @property int|null $hr_job_grade
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $created_by_name
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $updated_by_name
 */
class VUser extends \yii\db\ActiveRecord {

    public static function primaryKey() {
        return ["id"];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'v_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['id', 'status', 'postcode', 'area_id', 'ethnic_id', 'religion_id', 'is_leave_superior', 'skip_claim_authorize', 'leave_adjusted', 'designation', 'superior_id', 'hr_job_grade', 'created_by', 'updated_by'], 'integer'],
            [['username', 'email'], 'required'],
            [['dob', 'date_of_join', 'created_at', 'updated_at'], 'safe'],
            [['epf_percent'], 'number'],
            [['staff_id', 'company_code'], 'string', 'max' => 10],
            [['username', 'email', 'fullname', 'address', 'address_line_2', 'area_name', 'emergency_contact_person', 'ethnic_name', 'religion_name', 'sex_name', 'design_name', 'employment_type', 'company_name', 'superior_name', 'created_by_name', 'updated_by_name'], 'string', 'max' => 255],
            [['contact_no', 'emergency_contact_no', 'staff_type'], 'string', 'max' => 50],
            [['ic_no', 'state_name'], 'string', 'max' => 100],
            [['sex'], 'string', 'max' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'staff_id' => 'Staff ID',
            'username' => 'Username',
            'email' => 'Email',
            'status' => 'Status',
            'fullname' => 'Fullname',
            'contact_no' => 'Contact No',
            'ic_no' => 'Ic No',
            'address' => 'Address',
            'postcode' => 'Postcode',
            'address_line_2' => 'Address Line 2',
            'area_id' => 'Area ID',
            'area_name' => 'Area Name',
            'state_name' => 'State Name',
            'emergency_contact_no' => 'Emergency Contact No',
            'emergency_contact_person' => 'Emergency Contact Person',
            'ethnic_id' => 'Ethnic ID',
            'ethnic_name' => 'Ethnic Name',
            'religion_id' => 'Religion ID',
            'religion_name' => 'Religion Name',
            'sex' => 'Sex',
            'sex_name' => 'Sex Name',
            'dob' => 'Dob',
            'is_leave_superior' => 'Is Leave Superior',
            'skip_claim_authorize' => 'Skip Claim Authorize',
            'leave_adjusted' => 'Leave Adjusted',
            'designation' => 'Designation',
            'design_name' => 'Design Name',
            'staff_type' => 'Staff Type',
            'employment_type' => 'Employment Type',
            'company_code' => 'Company Code',
            'company_name' => 'Company Name',
            'date_of_join' => 'Date Of Join',
            'epf_percent' => 'Epf Percent',
            'superior_id' => 'Superior ID',
            'superior_name' => 'Superior Name',
            'hr_job_grade' => 'Hr Job Grade',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'created_by_name' => 'Created By Name',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'updated_by_name' => 'Updated By Name',
        ];
    }

    public static function getActiveStaffList() {
        return VUser::find()
                        ->where(['status' => User::STATUS_ACTIVE])
                        ->andWhere(['or', ['<>', 'staff_id', ''], ['staff_id' => null]])
                        ->andWhere(['<>', 'staff_id', "(NONE)"])
                        ->andWhere(['<>', 'staff_id', '-'])
                        ->orderBy(['fullname' => SORT_ASC])->asArray()->all();
    }

}
