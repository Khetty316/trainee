<?php

namespace frontend\models\office\pettyCash;

use Yii;
use common\models\User;
use frontend\models\office\pettyCash\PettyCashLedgerDetail;

/**
 * This is the model class for table "petty_cash_ledger_master".
 *
 * @property int $id
 * @property float|null $amount
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property PettyCashLedgerDetail[] $pettyCashLedgerDetails
 * @property User $createdBy
 * @property User $updatedBy
 */
class PettyCashLedgerMaster extends \yii\db\ActiveRecord {

    CONST Prefix_VoucherNo_Debit = "RC";
    CONST Prefix_VoucherNo_Credit = "PC";
    CONST runningNoLength = 2;

    public $startDate;
    public $endDate;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'petty_cash_ledger_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['amount'], 'number'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'amount' => 'Current Balance (RM)',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[PettyCashLedgerDetails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPettyCashLedgerDetails() {
        return $this->hasMany(PettyCashLedgerDetail::className(), ['pc_ledger_master_id' => 'id']);
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

    public function createLedger() {
        $masterLedger = PettyCashLedgerMaster::findOne(['created_by' => Yii::$app->user->identity->id]);
        if ($masterLedger === null) {
            $masterLedger = new PettyCashLedgerMaster();
        }

        if (!$masterLedger->save(false)) {
            return false;
        }

        return true;
    }

    public function generateVoucherNoDebit() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");
        $initialRefCode = self::Prefix_VoucherNo_Debit;

        // Query to count debit entries only (where debit is NOT NULL and credit IS NULL)
        $query = PettyCashLedgerDetail::find()
                ->where(['YEAR(created_at)' => $currentYear])
                ->andWhere(['created_by' => Yii::$app->user->identity->id])
                ->andWhere(['IS NOT', 'debit', null])
                ->andWhere(['IS', 'credit', null]);

        $runningNo = $query->count() + 1;

        // Pad running number with leading zeros
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $refCode = $initialRefCode . $currentYearShort . "/" . $currentMonth . "/" . $runningNo;
        return $refCode;
    }

//    public function generateVoucherNoCredit() {
//        $currentYear = date("Y");
//        $currentMonth = date("m");
//        $currentYearShort = date("y");
//        $initialRefCode = self::Prefix_VoucherNo_Credit; 
//        
//        // Query to count debit entries only (where debit is NOT NULL and credit IS NULL)
//        $query = PettyCashLedgerDetail::find()
//                ->where(['YEAR(created_at)' => $currentYear])
//                ->andWhere(['created_by' => Yii::$app->user->identity->id])
//                ->andWhere(['IS NOT', 'credit', null])
//                ->andWhere(['IS', 'debit', null]);
//
//        $runningNo = $query->count() + 1;
//
//        // Pad running number with leading zeros
//        if (strlen($runningNo) < self::runningNoLength) {
//            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
//        }
//
//        $refCode = $initialRefCode . $currentYearShort . "/" . $currentMonth . "/" . $runningNo;
//        return $refCode;
//    }

    public function generateVoucherNoCredit() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");
        $initialRefCode = self::Prefix_VoucherNo_Credit;

        // Get the latest voucher number for the current year/month
        $latestRecord = PettyCashLedgerDetail::find()
                ->where(['YEAR(created_at)' => $currentYear])
                ->andWhere(['MONTH(created_at)' => $currentMonth])
                ->andWhere(['created_by' => Yii::$app->user->identity->id])
                ->andWhere(['IS NOT', 'credit', null])
                ->andWhere(['IS', 'debit', null])
                ->andWhere(['LIKE', 'voucher_no', $initialRefCode . $currentYearShort . "/" . $currentMonth . "/"])
                ->orderBy(['voucher_no' => SORT_DESC])
                ->one();

        if ($latestRecord && !empty($latestRecord->voucher_no)) {
            // Extract the running number from the latest voucher
            $parts = explode('/', $latestRecord->voucher_no);
            $lastRunningNo = intval(end($parts));
            $runningNo = $lastRunningNo + 1;
        } else {
            // No previous record found, start from 1
            $runningNo = 1;
        }

        // Pad running number with leading zeros
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $refCode = $initialRefCode . $currentYearShort . "/" . $currentMonth . "/" . $runningNo;
        return $refCode;
    }

    public function updateLedgerMasterAmount() {
        // Update master ledger balance
        $debit = PettyCashLedgerDetail::find()
                ->where(['pc_ledger_master_id' => $this->id])
                ->sum('debit');

        $credit = PettyCashLedgerDetail::find()
                ->where(['pc_ledger_master_id' => $this->id])
                ->sum('credit');

        $this->amount = ($debit - $credit);

        if (!$this->save(false)) {
            return false;
        } else {
            return true;
        }
    }
}
