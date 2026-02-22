<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;

/**
 * This is the model class for table "claim_entitlement".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $year
 * @property int|null $superior_id
 * @property int|null $status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $user
 * @property User $superior
 * @property User $createdBy
 * @property User $updatedBy
 * @property RefGeneralStatus $status0
 * @property ClaimEntitlementDetails[] $claimEntitlementDetails
 */
class ClaimEntitlement extends \yii\db\ActiveRecord {

    const SUPERIOR_USER_MANUAL_FILENAME = "T6B2b-Claim Entitlement Superior Module-01.pdf";
    const HR_USER_MANUAL_FILENAME = "T6B2b-Claim Entitlement HR Module-01.pdf";
    const USER_MANUAL_FILENAME = "T6B2b-Claim Entitlement Module-02.pdf";

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_entitlement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'year', 'superior_id', 'status', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['superior_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'user_id' => 'Staff Name',
            'year' => 'Year',
            'superior_id' => 'Superior ID',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefGeneralStatus::className(), ['id' => 'status']);
    }

    /**
     * Gets query for [[ClaimEntitlementDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimEntitlementDetails() {
        return $this->hasMany(ClaimEntitlementDetails::className(), ['claim_entitle_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }

        return parent::beforeSave($insert);
    }

    public function getClaimSummary($year = '', $month = '', $claim_type = '', $staff = '') {
        // --- 1. Initialize & Defaults ---
        $year = $year ?: date('Y');
        $currentYear = date('Y');

        // --- 2. Generate Dropdown & Lookup Data ---
        $yearList = array_combine(
                range($currentYear - 5, $currentYear + 1),
                range($currentYear - 5, $currentYear + 1)
        );

        $monthList = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        // Determine staff grade
//        $grade = 1;
//        if ($staff !== '') {
//            $staffDetail = User::findOne($staff);
//            $grade = ($staffDetail->grade === \frontend\models\RefStaffGrade::EXEC_CODE ? 1 : 0);
//        }

        $user = User::findOne(Yii::$app->user->identity->id);
        if($user->grade === \frontend\models\RefStaffGrade::EXEC_CODE){
            $claimTypeList = RefClaimType::find()
                ->where(['is_active' => 1])
                ->orderBy(['claim_name' => SORT_ASC])
                ->select(['claim_name'])
                ->indexBy('code')
                ->column();
            
            
        }else{
            $claimTypeList = RefClaimType::find()
                ->where(['is_active' => 1, 'grade' => 0])
                ->orderBy(['claim_name' => SORT_ASC])
                ->select(['claim_name'])
                ->indexBy('code')
                ->column();
        }
//        $claimTypeList = RefClaimType::find()
//                ->where(['is_active' => 1, 'grade' => $grade])
//                ->orderBy(['claim_name' => SORT_ASC])
//                ->select(['claim_name'])
//                ->indexBy('code')
//                ->column();

        $claim_type = $claim_type ?: array_key_first($claimTypeList);

        // Check budget flag
        $claimTypeDetail = RefClaimType::findOne($claim_type);
        $hasEntitlement = ($claimTypeDetail && $claimTypeDetail->budget == 1);

        $staffList = User::find()
                ->where(['status' => 10])
                ->select(['fullname', 'id', 'staff_id'])
                ->indexBy('id')
                ->orderBy(['fullname' => 'ASC'])
                ->column();

        $intMonths = $month ? [$month] : array_keys($monthList);

        // --- 3. Build Dynamic SQL Query ---
        $monthCases = [];
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($intMonths as $monthNum) {
            $monthCases[] = $this->buildUnifiedMonthCases($monthNames[(int) $monthNum - 1], $monthNum);
        }

        $monthCasesString = implode(",\n\t\t", $monthCases);

        // Conditional clauses
        $claimTypeCondition = $claim_type ? " AND ced.claim_type_code = :claim_type" : "";
        $claimMasterClaimTypeCondition = $claim_type ? " AND cm.claim_type = :claim_type" : "";
        $staffCondition = $staff ? " AND u.id = :staff" : "";
        $monthConditions = $month ? " AND MONTH(cd.receipt_date) = :month" : "";

        $sql = "
    SELECT
        u.id as claimant_id,
        u.staff_id as staff_id,
        u.fullname,
        {$monthCasesString}
    FROM
        user u
    LEFT JOIN
        claim_entitlement ce ON u.id = ce.user_id AND ce.year = :year
    LEFT JOIN
        claim_entitlement_details ced ON ce.id = ced.claim_entitle_id
        {$claimTypeCondition}
    LEFT JOIN
        claim_master cm ON u.id = cm.claimant_id
        AND cm.is_deleted = 0
        {$claimMasterClaimTypeCondition}
    LEFT JOIN
        claim_detail cd ON cm.id = cd.claim_master_id
        AND YEAR(cd.receipt_date) = :year
        {$monthConditions}
        AND cd.is_deleted = 0
    WHERE
        u.status = 10
        {$staffCondition}
    GROUP BY
        u.id, u.fullname, u.staff_id
    ORDER BY
        u.fullname
    ";

        // --- 4. Execute Query ---
        $params = [':year' => $year];
        if ($claim_type)
            $params[':claim_type'] = $claim_type;
        if ($staff)
            $params[':staff'] = $staff;
        if ($month)
            $params[':month'] = $month;

        $results = Yii::$app->db->createCommand($sql, $params)->queryAll();

        // --- 5. Transform Data for View ---
        $finalArray = [];
        foreach ($results as $row) {
            $staffId = $row['staff_id'];

            $finalArray[$staffId] = [
                'staffid' => $staffId,
                'fullname' => $row['fullname'],
            ];

            foreach ($intMonths as $monthNum) {
                $monthName = $monthNames[(int) $monthNum - 1];

                $claimSubmit = $row[$monthName . '_Claim_Submit'] ?? 0;
                $claimApprove = $row[$monthName . '_Claim_Approve'] ?? 0;
                $claimReject = $row[$monthName . '_Claim_Reject'] ?? 0;
                $pending = $row[$monthName . '_Pending'] ?? 0;
                $paid = $row[$monthName . '_Paid'] ?? 0;

                $monthData = [
                    'ClaimSubmit' => $claimSubmit,
                    'ClaimApprove' => $claimApprove,
                    'ClaimReject' => $claimReject,
                    'Pending' => $pending,
                    'Paid' => $paid,
                ];

                if ($hasEntitlement) {
                    $noLimit = $row[$monthName . '_NoLimit'] ?? 0;
                    $entitlementAmount = $row[$monthName . '_Entitle'] ?? 0;
                    $entitlement = $noLimit ? 'No limit' : $entitlementAmount;

                    // Logic: Reserve funds immediately upon submission. Release funds back if rejected.
                    $balance = $noLimit ? 'No limit' : ($entitlementAmount - $claimSubmit + $claimReject);

                    $monthData['Entitlement'] = $entitlement;
                    $monthData['Balance'] = $balance;
                } else {
                    $monthData['Entitlement'] = '-';
                    $monthData['Balance'] = '-';
                }

                $finalArray[$staffId][$monthNum] = $monthData;
            }
        }

        // --- 6. Return Data ---
        return [
            'claimSummarys' => $finalArray,
            'hasEntitlement' => $hasEntitlement,
            'yearList' => $yearList,
            'monthList' => $monthList,
            'claimTypes' => $claimTypeList,
            'staffList' => $staffList,
            'intMonth' => $intMonths,
            'year' => $year,
            'month' => $month,
            'claimType' => $claim_type,
            'staff' => $staff
        ];
    }

    /**
     * Helper: Generate SQL with updated Status Logic
     */
    private function buildUnifiedMonthCases(string $monthName, string $monthNum) {
        $monthInt = (int) $monthNum;

        // Status Constants
        $statusWaitingPayment = RefGeneralStatus::STATUS_WaitingForPayment;
        $statusPaid = RefGeneralStatus::STATUS_Paid;

        // Fully Approved List (Must include Paid status)
        $approvedStatuses = [
            $statusWaitingPayment,
            $statusPaid
        ];
        $approvedSqlString = implode(', ', $approvedStatuses);

        return "
    -- 1. Entitlement & No Limit
    MAX(CASE WHEN ce.year = :year AND {$monthInt} BETWEEN ced.month_start AND ced.month_end THEN ced.amount ELSE 0 END) as '{$monthName}_Entitle',
    MAX(CASE WHEN ce.year = :year AND {$monthInt} BETWEEN ced.month_start AND ced.month_end THEN ced.no_limit ELSE 0 END) as '{$monthName}_NoLimit',
    
    -- 2. Submitted (All claims, including Rejected)
    SUM(CASE 
        WHEN MONTH(cd.receipt_date) = {$monthInt} 
        AND YEAR(cd.receipt_date) = :year 
        THEN cd.amount_to_be_paid 
        ELSE 0 
    END) as '{$monthName}_Claim_Submit',

    -- 3. Approved (Finance Verified + Paid)
    SUM(CASE 
        WHEN MONTH(cd.receipt_date) = {$monthInt} 
        AND YEAR(cd.receipt_date) = :year 
        AND cd.claim_status = 0 
        AND cm.claim_status IN ({$approvedSqlString}) 
        THEN cd.amount_to_be_paid 
        ELSE 0 
    END) as '{$monthName}_Claim_Approve',

    -- 4. Rejected (Specifically status = 1)
    SUM(CASE 
        WHEN MONTH(cd.receipt_date) = {$monthInt} 
        AND YEAR(cd.receipt_date) = :year 
        AND cd.claim_status = 1 
        THEN cd.amount_to_be_paid 
        ELSE 0 
    END) as '{$monthName}_Claim_Reject',

    -- 5. Pending Payment
    SUM(CASE 
        WHEN MONTH(cd.receipt_date) = {$monthInt} 
        AND YEAR(cd.receipt_date) = :year 
        AND cm.claim_status = {$statusWaitingPayment} 
        THEN cd.amount_to_be_paid 
        ELSE 0 
    END) as '{$monthName}_Pending',

    -- 6. Paid
    SUM(CASE 
        WHEN MONTH(cd.receipt_date) = {$monthInt} 
        AND YEAR(cd.receipt_date) = :year 
        AND cm.claim_status = {$statusPaid} 
        AND cd.is_paid = 1 
        THEN cd.amount_to_be_paid 
        ELSE 0 
    END) as '{$monthName}_Paid'
    ";
    }
}
