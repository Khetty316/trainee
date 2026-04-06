<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use \frontend\models\common\RefArea;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use frontend\models\common\RefUserDesignation;
use frontend\models\common\RefUserReligion;
use frontend\models\common\RefUserEthnic;
use frontend\models\common\RefUserSex;
use frontend\models\common\RefUserEmploymentType;
use frontend\models\common\RefCompanyGroupList;
use frontend\models\projectproduction\task\TaskAssignment;
use common\modules\auth\models\AuthItem;
use common\modules\auth\models\AuthAssignment;
use frontend\models\appraisal\AppraisalMaster;
use frontend\models\working\hrdoc\HrEmployeeDocuments;
use frontend\models\RefStaffGrade;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $staff_id
 * @property string|null $grade
 * @property string $username
 * @property string $auth_key
 * @property string $password
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property string|null $verification_token
 * @property string|null $fullname
 * @property string|null $contact_no
 * @property string|null $ic_no
 * @property string|null $address
 * @property string|null $address_line_2
 * @property int|null $postcode
 * @property int|null $area_id
 * @property string|null $emergency_contact_no
 * @property string|null $emergency_contact_person
 * @property int|null $ethnic_id
 * @property int|null $religion_id
 * @property string|null $sex
 * @property string|null $dob
 * @property int|null $is_leave_superior
 * @property string|null $profile_pic
 * @property int $skip_claim_authorize
 * @property int $leave_adjusted 0 for new staff. To state that when the annual leave start to count
 * @property int|null $designation
 * @property string|null $employment_type
 * @property string|null $company_name
 * @property string|null $date_of_join
 * @property float|null $epf_percent
 * @property int|null $superior_id
 * @property int|null $hr_job_grade
 * @property string $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $remember_token
 *
 * @property AppraisalMain[] $appraisalMains
 * @property AppraisalMaster[] $appraisalMasters
 * @property AppraisalMaster[] $appraisalMasters0
 * @property AppraisalMaster[] $appraisalMasters1
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property ClaimApprovalWorklist[] $claimApprovalWorklists
 * @property ClaimDetail[] $claimDetails
 * @property ClaimDetail[] $claimDetails0
 * @property ClaimEntitleWorklist[] $claimEntitleWorklists
 * @property ClaimEntitlement[] $claimEntitlements
 * @property ClaimEntitlement[] $claimEntitlements0
 * @property ClaimEntitlement[] $claimEntitlements1
 * @property ClaimEntitlement[] $claimEntitlements2
 * @property ClaimEntitlementDetails[] $claimEntitlementDetails
 * @property ClaimEntitlementDetails[] $claimEntitlementDetails0
 * @property ClaimMaster[] $claimMasters
 * @property ClaimMaster[] $claimMasters0
 * @property ClaimMaster[] $claimMasters1
 * @property ClaimMaster[] $claimMasters2
 * @property Clients[] $clients
 * @property EhOutpatientMedDetail[] $ehOutpatientMedDetails
 * @property EhOutpatientMedDetail[] $ehOutpatientMedDetails0
 * @property EhOutpatientMedMaster[] $ehOutpatientMedMasters
 * @property EhOutpatientMedMaster[] $ehOutpatientMedMasters0
 * @property EhTravelAllowanceDetail[] $ehTravelAllowanceDetails
 * @property EhTravelAllowanceDetail[] $ehTravelAllowanceDetails0
 * @property EhTravelAllowanceMaster[] $ehTravelAllowanceMasters
 * @property EhTravelAllowanceMaster[] $ehTravelAllowanceMasters0
 * @property EmployeeHandbookMaster[] $employeeHandbookMasters
 * @property EmployeeHandbookMaster[] $employeeHandbookMasters0
 * @property HrEmployeeDocuments[] $hrEmployeeDocuments
 * @property HrEmployeeDocuments[] $hrEmployeeDocuments0
 * @property LeaveHolidays[] $leaveHolidays
 * @property LeaveMaster[] $leaveMasters
 * @property LeaveMaster[] $leaveMasters0
 * @property LeaveMaster[] $leaveMasters1
 * @property LeaveMonthlySummary[] $leaveMonthlySummaries
 * @property LeaveWorklist[] $leaveWorklists
 * @property MonthlyAttendance[] $monthlyAttendances
 * @property PrereqFormItem[] $prereqFormItems
 * @property PrereqFormItem[] $prereqFormItems0
 * @property PrereqFormItemWorklist[] $prereqFormItemWorklists
 * @property PrereqFormMaster[] $prereqFormMasters
 * @property PrereqFormMaster[] $prereqFormMasters0
 * @property PrereqFormMaster[] $prereqFormMasters1
 * @property ProjectProductionMaster[] $projectProductionMasters
 * @property ProjectProductionPanels[] $projectProductionPanels
 * @property ProjectProductionPanels[] $projectProductionPanels0
 * @property ProjectProductionPanels[] $projectProductionPanels1
 * @property ProjectProductionPanels[] $projectProductionPanels2
 * @property ProjectQMasters[] $projectQMasters
 * @property ProjectQRevisions[] $projectQRevisions
 * @property RefAppraisalStatus[] $refAppraisalStatuses
 * @property RefCurrencies[] $refCurrencies
 * @property RefCurrencies[] $refCurrencies0
 * @property StockDispatchMaster[] $stockDispatchMasters
 * @property StockDispatchMaster[] $stockDispatchMasters0
 * @property StockDispatchTrial[] $stockDispatchTrials
 * @property TaskAssignElecComplete[] $taskAssignElecCompletes
 * @property TaskAssignElecCompleteDelete[] $taskAssignElecCompleteDeletes
 * @property TaskAssignElecCompleteDelete[] $taskAssignElecCompleteDeletes0
 * @property TaskAssignElecStaff[] $taskAssignElecStaff
 * @property TaskAssignElec[] $taskAssignElecs
 * @property TaskAssignElecStaffComplete[] $taskAssignElecStaffCompletes
 * @property TaskAssignElecStaffCompleteDelete[] $taskAssignElecStaffCompleteDeletes
 * @property TaskAssignElecStaffCompleteDelete[] $taskAssignElecStaffCompleteDeletes0
 * @property TaskAssignFabComplete[] $taskAssignFabCompletes
 * @property TaskAssignFabCompleteDelete[] $taskAssignFabCompleteDeletes
 * @property TaskAssignFabCompleteDelete[] $taskAssignFabCompleteDeletes0
 * @property TaskAssignFabStaff[] $taskAssignFabStaff
 * @property TaskAssignFab[] $taskAssignFabs
 * @property TaskAssignFabStaffComplete[] $taskAssignFabStaffCompletes
 * @property TaskAssignFabStaffCompleteDelete[] $taskAssignFabStaffCompleteDeletes
 * @property TaskAssignFabStaffCompleteDelete[] $taskAssignFabStaffCompleteDeletes0
 * @property TaskAssignOngoingSummary $taskAssignOngoingSummary
 * @property TestCustomContent[] $testCustomContents
 * @property TestMaster[] $testMasters
 * @property TestMaster[] $testMasters0
 * @property RefUserEmploymentType $employmentType
 * @property RefCompanyGroupList $companyName
 * @property RefArea $area
 * @property RefUserDesignation $designation0
 * @property RefUserEthnic $ethnic
 * @property RefUserReligion $religion
 * @property RefUserSex $sex0
 * @property RefStaffGrade $grade0
 * @property User $superior
 * @property User[] $users
 * @property WorkAssignmentMasterDelete[] $workAssignmentMasterDeletes
 * @property WorkerTaskCategories[] $workerTaskCategories
 */
class User extends ActiveRecord implements IdentityInterface {

    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    const HEAD_OF_MAINTENANCE_NAME = 'Tang Hung Lung';
    
//    const authFabAssignee = 'prodFabAssignee', authElecAssignee = 'prodElecAssignee';

    public $scannedFile;
    public $totalTaskOnHand = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
            [['username', 'auth_key', 'password', 'email', 'fullname', 'staff_id', 'company_name', 'employment_type'], 'required'],
            [['status', 'postcode', 'area_id', 'ethnic_id', 'religion_id', 'is_leave_superior', 'skip_claim_authorize', 'leave_adjusted', 'designation', 'superior_id', 'hr_job_grade', 'created_by', 'updated_by'], 'integer'],
            [['dob', 'date_of_join', 'created_at', 'updated_at'], 'safe'],
            [['epf_percent'], 'number'],
            [['staff_id', 'employment_type', 'company_name'], 'string', 'max' => 10],
            [['grade', 'ic_no', 'remember_token'], 'string', 'max' => 100],
            [['username', 'password', 'password_reset_token', 'email', 'verification_token', 'fullname', 'address', 'address_line_2', 'emergency_contact_person', 'profile_pic'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['contact_no', 'emergency_contact_no'], 'string', 'max' => 50],
            [['ic_no', 'remember_token'], 'string', 'max' => 100],
            [['sex'], 'string', 'max' => 1],
            [['sex'], 'default', 'value' => null],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['employment_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefUserEmploymentType::className(), 'targetAttribute' => ['employment_type' => 'code']],
            [['company_name'], 'exist', 'skipOnError' => true, 'targetClass' => RefCompanyGroupList::className(), 'targetAttribute' => ['company_name' => 'code']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefArea::className(), 'targetAttribute' => ['area_id' => 'area_id']],
            [['designation'], 'exist', 'skipOnError' => true, 'targetClass' => RefUserDesignation::className(), 'targetAttribute' => ['designation' => 'id']],
            [['ethnic_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefUserEthnic::className(), 'targetAttribute' => ['ethnic_id' => 'id']],
            [['religion_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefUserReligion::className(), 'targetAttribute' => ['religion_id' => 'id']],
            [['sex'], 'exist', 'skipOnError' => true, 'targetClass' => RefUserSex::className(), 'targetAttribute' => ['sex' => 'code']],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['superior_id' => 'id']],
            [['grade'], 'exist', 'skipOnError' => true, 'targetClass' => RefStaffGrade::className(), 'targetAttribute' => ['grade' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'staff_id' => 'Staff ID',
            'grade' => 'Grade',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'verification_token' => 'Verification Token',
            'fullname' => 'Fullname',
            'contact_no' => 'Contact No',
            'ic_no' => 'Ic No',
            'address' => 'Address',
            'address_line_2' => 'Address Line 2',
            'postcode' => 'Postcode',
            'area_id' => 'Area ID',
            'emergency_contact_no' => 'Emergency Contact No',
            'emergency_contact_person' => 'Emergency Contact Person',
            'ethnic_id' => 'Ethnic ID',
            'religion_id' => 'Religion ID',
            'sex' => 'Gender',
            'dob' => 'Dob',
            'is_leave_superior' => 'Is Leave Superior (System)?',
            'profile_pic' => 'Profile Pic',
            'skip_claim_authorize' => 'Skip Claim Authorize (System)?',
            'leave_adjusted' => 'Leave Adjusted',
            'designation' => 'Position',
            'employment_type' => 'Employment Type',
            'company_name' => 'Company Name',
            'date_of_join' => 'Date Of Join',
            'epf_percent' => 'Epf Percent',
            'superior_id' => 'Superior ID',
            'hr_job_grade' => 'Hr Job Grade',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
            'remember_token' => 'Remember Token',
        ];
    }

    /**
     * Gets query for [[Grade0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGrade0() {
        return $this->hasOne(RefStaffGrade::className(), ['code' => 'grade']);
    }

    /**
     * Gets query for [[AppraisalMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasters() {
        return $this->hasMany(AppraisalMaster::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[AppraisalMasters0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasters0() {
        return $this->hasMany(AppraisalMaster::className(), ['appraise_by' => 'id']);
    }

    /**
     * Gets query for [[AppraisalMasters1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppraisalMasters1() {
        return $this->hasMany(AppraisalMaster::className(), ['review_by' => 'id']);
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments() {
        return $this->hasMany(AuthAssignment::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[ItemNames]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames() {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Clients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClients() {
        return $this->hasMany(Clients::className(), ['created_by' => 'id']);
    }

    /**
     * Gets query for [[ProjectProductionMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionMasters() {
        return $this->hasMany(ProjectProductionMaster::className(), ['created_by' => 'id']);
    }

    /**
     * Gets query for [[ProjectProductionPanels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanels() {
        return $this->hasMany(ProjectProductionPanels::className(), ['design_completed_by' => 'id']);
    }

    /**
     * Gets query for [[ProjectProductionPanels0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanels0() {
        return $this->hasMany(ProjectProductionPanels::className(), ['material_completed_by' => 'id']);
    }

    /**
     * Gets query for [[ProjectProductionPanels1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanels1() {
        return $this->hasMany(ProjectProductionPanels::className(), ['fabrication_completed_by' => 'id']);
    }

    /**
     * Gets query for [[ProjectProductionPanels2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanels2() {
        return $this->hasMany(ProjectProductionPanels::className(), ['wiring_completed_by' => 'id']);
    }

    /**
     * Gets query for [[ProjectQMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQMasters() {
        return $this->hasMany(ProjectQMasters::className(), ['project_coordinator' => 'id']);
    }

    /**
     * Gets query for [[ProjectQRevisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQRevisions() {
        return $this->hasMany(ProjectQRevisions::className(), ['incharged_by' => 'id']);
    }

    /**
     * Gets query for [[RefCurrencies]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefCurrencies() {
        return $this->hasMany(RefCurrencies::className(), ['created_by' => 'id']);
    }

    /**
     * Gets query for [[RefCurrencies0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRefCurrencies0() {
        return $this->hasMany(RefCurrencies::className(), ['updated_by' => 'id']);
    }

    /**
     * Gets query for [[Area]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getArea() {
        return $this->hasOne(RefArea::className(), ['area_id' => 'area_id']);
    }

    /**
     * Gets query for [[Designation0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDesignation0() {
        return $this->hasOne(RefUserDesignation::className(), ['id' => 'designation']);
    }

    /**
     * Gets query for [[Ethnic]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEthnic() {
        return $this->hasOne(RefUserEthnic::className(), ['id' => 'ethnic_id']);
    }

    /**
     * Gets query for [[Religion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReligion() {
        return $this->hasOne(RefUserReligion::className(), ['id' => 'religion_id']);
    }

    /**
     * Gets query for [[Sex0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSex0() {
        return $this->hasOne(RefUserSex::className(), ['code' => 'sex']);
    }

    /**
     * Gets query for [[Superior]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSuperior() {
        return $this->hasOne(User::className(), ['id' => 'superior_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers() {
        return $this->hasMany(User::className(), ['superior_id' => 'id']);
    }

    /**
     * Gets query for [[EmploymentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmploymentType() {
        return $this->hasOne(RefUserEmploymentType::className(), ['code' => 'employment_type']);
    }

    /**
     * Gets query for [[CompanyName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyName() {
        return $this->hasOne(RefCompanyGroupList::className(), ['code' => 'company_name']);
    }

    /**
     * Gets query for [[HrEmployeeDocuments0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHrEmployeeDocuments0
    () {
        return $this->hasMany(HrEmployeeDocuments::className(), ['created_by' => 'id']);
    }

    /**
     * Gets query for [[LeaveCompulsoryDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveCompulsoryDetails() {
        return $this->hasMany(LeaveCompulsoryDetail::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[LeaveCompulsoryMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveCompulsoryMasters() {

        return $this->hasMany(LeaveCompulsoryMaster::className(), ['requestor' => 'id']);
    }

    /**
     * Gets query for [[LeaveCompulsoryMasters0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveCompulsoryMasters0() {

        return $this->hasMany(LeaveCompulsoryMaster::className(), ['approval_by' => 'id']);
    }

    /**
     * Gets query for [[LeaveHolidays]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveHolidays() {
        return

                $this->hasMany(LeaveHolidays::className(), ['created_by' => 'id']);
    }

    /**
     * Gets query for [[LeaveMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters() {
        return $this->
                        hasMany(LeaveMaster::className(), ['relief_user_id' => 'id']);
    }

    /**
     * Gets query for [[LeaveMasters0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters0() {
        return $this->
                        hasMany(LeaveMaster::className(), ['requestor_id' => 'id']);
    }

    /**
     * Gets query for [[LeaveMasters1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMasters1() {
        return $this->hasMany(
                        LeaveMaster::className(), ['superior_id' => 'id']);
    }

    /**
     * Gets query for [[LeaveMonthlySummaries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveMonthlySummaries() {
        return $this->
                        hasMany(LeaveMonthlySummary::className(), ['requestor_id' => 'id']);
    }

    /**
     * Gets query for [[LeaveWorklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLeaveWorklists() {
        return $this->hasMany(
                        LeaveWorklist::className(), ['responsed_by' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignElecStaff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecStaff() {
        return $this->hasMany(TaskAssignElecStaff::className(), [
                    'user_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignElecs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignElecs() {
        return $this->hasMany(TaskAssignElec::className(), ['id' =>
                    'task_assign_elec_id'])->viaTable('task_assign_elec_staff', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignFabStaff]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabStaff() {
        return $this->hasMany(TaskAssignFabStaff::className(), ['user_id' => 'id'
        ]);
    }

    /**
     * Gets query for [[TaskAssignFabs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignFabs() {
        return $this->hasMany(TaskAssignFab::className(), ['id' => 'task_assign_fab_id'])
                        ->viaTable('task_assign_fab_staff', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[TaskAssignOngoingSummary]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskAssignOngoingSummary() {
        return $this->hasOne(TaskAssignOngoingSummary::className(), ['user_id' =>
                    'id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
                    'verification_token' => $token,
                    'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken() {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function activateUser() {
        $this->status = self::STATUS_ACTIVE;
        return $this->update();
    }

    public function deactivateUser() {
        $this->status = self::STATUS_INACTIVE;
        return $this->update();
    }

    public function deleteUser() {
        $this->status = self::STATUS_DELETED;
        return $this->update();
    }

    public static function getActiveDropDownList() {
        return \yii\helpers\ArrayHelper::map(User::find()->where(['status' => self::STATUS_ACTIVE])->orderBy(['fullname' => SORT_ASC])->all(), "id", "fullname");
    }

    public static function getActiveExexGradeDropDownList() {
        return \yii\helpers\ArrayHelper::map(User::find()->where(['status' => self::STATUS_ACTIVE, 'grade' => \frontend\models\RefStaffGrade::EXEC_CODE])->orderBy(['fullname' => SORT_ASC])->all(), "id", "fullname");
    }

    public static function getActiveDropDownListExcludeOne($userIdToBeExclude) {
        if (is_null($userIdToBeExclude)) {
            $userIdToBeExclude = 0;
        }
        $userList = User::find()->where(['status' => self::STATUS_ACTIVE])->andWhere('id!=' . $userIdToBeExclude)->orderBy(['fullname' => SORT_ASC])->all();
        return \yii\helpers\ArrayHelper::map($userList, "id", "fullname");
    }

    public static function getAllDropDownList() {
        return \yii\helpers\ArrayHelper::map(User::find()->all(), "id", "fullname");
    }

    public static function getActiveAutocompleteList() {
        return User::find()->select(['fullname as value', 'id as id', 'fullname as label'])
                        ->orderBy(['fullname' => SORT_ASC])
                        ->asArray()
                        ->all();
    }

    /**
     * 
     * @return type
     */
    public static function getActiveLeaveSuperiorDropDownList() {
        return \yii\helpers\ArrayHelper::map(User::findAll(['status' => self::STATUS_ACTIVE, 'is_leave_superior' => '1']), "id", "fullname");
    }

    /**
     * **************************************** MAIN FUNCTION
     * @return boolean
     */
    public function processAndSave() {
        $this->date_of_join = myTools\MyFormatter::fromDateRead_toDateSQL($this->date_of_join);
        if ($this->save()) {

            if ($this->validate() && $this->scannedFile) {
                $filePath = Yii::$app->params['user_profile_file_path'] . $this->id . "/";
                $this->profile_pic = 'profile_' . $this->id . "_" . $this->scannedFile->baseName . '.' . $this->scannedFile->extension;
//                if (!is_dir($filePath)) {
                myTools\MyCommonFunction::mkDirIfNull($filePath);
//                }
                $this->scannedFile->saveAs($filePath . $this->profile_pic);
                \common\models\myTools\ImageHandler::resize_image_w1200($filePath . $this->profile_pic, $this->scannedFile->extension);
                $this->update(false);
            }
            myTools\FlashHandler::success("User profile updated success!");
            return true;
        } else {
            myTools\FlashHandler::err("User profile updated FAIL!");
            return false;
        }
    }

    static function getNextUserByStaffId($currentStaffId) {
        return $nextUser = User::find()->where("staff_id>'" . $currentStaffId . "'")->andWhere('status in (9,10)')->orderBy(['staff_id' => SORT_ASC])->limit(1)->one();
    }

    public function isLeaveSuperior() {
        return (User::find()->where(['superior_id' => $this->id])->count() > 0) ? true : false;
    }

    public static function getActiveStaffList() {
        return User::find()->where(['status' => User::STATUS_ACTIVE])->andWhere(['or', ['<>', 'staff_id', ''], ['staff_id' => null]])->asArray()->orderBy(['fullname' => SORT_ASC])->all();
    }

    public static function getStaffWithoutLeaveEntitlement($year) {
        return User::find()->select('user.*,leave_entitlement.id')->join('LEFT JOIN', 'leave_entitlement', 'user.id=leave_entitlement.user_id AND year=' . $year)
                        ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id=user.id AND auth_assignment.item_name="STAFF"')->having('leave_entitlement.id IS NULL')
                        ->where("user.status=" . User::STATUS_ACTIVE . " AND (user.staff_id !='' OR user.staff_id IS NOT NULL)")->asArray()->orderBy(['user.staff_id' => 'SORT_ASC'])->all();
    }

    public static function getStaffWithoutLeaveStartMonth() {
        return User::find()->where("user.status=" . User::STATUS_ACTIVE . " AND (user.staff_id !='' OR user.staff_id IS NOT NULL) AND user.leave_adjusted=0")
                        ->asArray()->orderBy(['user.staff_id' => 'SORT_ASC'])->all();
    }

    public static function getAutoCompleteList() {
        $list = User::find()
                ->select(['id as value', 'id as id', 'fullname as label', 'staff_id'])
                ->asArray()
                ->all();
        return $list;
    }

    public static function getAutoCompleteListActiveOnly() {
        $list = User::find()
                ->select(['fullname as value', 'id as id', 'fullname as label', 'staff_id'])
                ->where(['status' => self::STATUS_ACTIVE])
                ->asArray()
                ->all();
        return $list;
    }

    public static function getStaffList_productionAssignee($taskType = "") {
        $list = User::find()->select(['user.*', 'task_assign_ongoing_summary.total_task_onhand as totalTaskOnHand'])
                ->join('INNER JOIN', 'auth_assignment', 'user.id=auth_assignment.user_id')
                ->leftJoin("task_assign_ongoing_summary", "task_assign_ongoing_summary.user_id=user.id")
                ->where("status=" . User::STATUS_ACTIVE);

        if ($taskType == TaskAssignment::taskTypeFabrication) {
            $list = $list->andWhere(['in', 'item_name', [AuthItem::ROLE_PrdnFab_Wkr]]);
        } else if ($taskType == TaskAssignment::taskTypeElectrical) {
            $list = $list->andWhere(['in', 'item_name', [AuthItem::ROLE_PrdnElec_Wkr]]);
        } else {
            $list = $list->andWhere(['in', 'item_name', [AuthItem::ROLE_PrdnFab_Wkr, AuthItem::ROLE_PrdnElec_Wkr]]);
        }

        $list = $list->asArray()
                        ->orderBy(['fullname' => 'SORT_ASC'])->all();
        return $list;
    }

    public static function getStaffTypeList($staffType) {
        return User::find()
                        ->select(['user.*'])
                        ->joinWith('designation0') // Assuming 'designation' is the name of the relation in User model
                        ->where("status=" . User::STATUS_ACTIVE . " AND (staff_id !='' OR staff_id IS NOT NULL)")
                        ->andWhere(['ref_user_designation.staff_type' => $staffType])
                        ->orderBy(['user.fullname' => SORT_ASC])
                        ->all();
    }

    public static function getProjectCoordinatorList() {
        return User::find()
                        ->select(['user.*'])
                        ->joinWith('authAssignments') // Assuming 'authAssignment' is the relation name in the User model
                        ->where(['status' => User::STATUS_ACTIVE])
                        ->andWhere(['or', ['!=', 'staff_id', ''], ['is not', 'staff_id', null]])
                        ->andWhere(['auth_assignment.item_name' => 'projcoor'])
                        ->orderBy(['user.fullname' => SORT_ASC])
                        ->all();
    }
}
