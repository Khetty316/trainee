<?php

namespace frontend\models\office\pettyCash;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;

/**
 * This is the model class for table "petty_cash_replenishment".
 *
 * @property int $id
 * @property string|null $ref_code
 * @property string|null $voucher_no
 * @property float|null $amount_requested
 * @property string|null $purpose
 * @property float|null $amount_approved
 * @property int|null $status
 * @property int|null $superior_id
 * @property int|null $director_responsed_by
 * @property string|null $director_responsed_at
 * @property string|null $director_responsed_remark
 * @property int|null $director_responsed_status
 * @property int|null $finance_responsed_by
 * @property string|null $finance_responsed_at
 * @property int|null $finance_responsed_status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 *
 * @property User $superior
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $deletedBy
 * @property RefGeneralStatus $status0
 * @property User $directorResponsedBy
 * @property User $financeResponsedBy
 */
class PettyCashReplenishment extends \yii\db\ActiveRecord {

    CONST Prefix_RefCode = "PCR";
    CONST runningNoLength = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'petty_cash_replenishment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['amount_requested', 'amount_approved'], 'number'],
            [['status', 'superior_id', 'director_responsed_by', 'director_responsed_status', 'finance_responsed_by', 'finance_responsed_status', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['director_responsed_at', 'finance_responsed_at', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['ref_code', 'voucher_no', 'purpose', 'director_responsed_remark'], 'string', 'max' => 255],
            [['superior_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['superior_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['director_responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['director_responsed_by' => 'id']],
            [['finance_responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['finance_responsed_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'ref_code' => 'Reference Code',
            'voucher_no' => 'Voucher No.',
            'amount_requested' => 'Amount Requested',
            'purpose' => 'Purpose',
            'amount_approved' => 'Amount Approved',
            'status' => 'Status',
            'superior_id' => 'Superior ID',
            'director_responsed_by' => 'Director Responsed By',
            'director_responsed_at' => 'Director Responsed At',
            'director_responsed_remark' => 'Director Responsed Remark',
            'director_responsed_status' => 'Director Responsed Status',
            'finance_responsed_by' => 'Finance Responsed By',
            'finance_responsed_at' => 'Finance Responsed At',
            'finance_responsed_status' => 'Finance Responsed Status',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
        ];
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
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy() {
        return $this->hasOne(User::className(), ['id' => 'deleted_by']);
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
     * Gets query for [[DirectorResponsedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDirectorResponsedBy() {
        return $this->hasOne(User::className(), ['id' => 'director_responsed_by']);
    }

    /**
     * Gets query for [[FinanceResponsedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinanceResponsedBy() {
        return $this->hasOne(User::className(), ['id' => 'finance_responsed_by']);
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

    public function generateRefCode() {
        $currentYear = date("Y");
        $currentMonth = date("m");
        $currentYearShort = date("y");

        $initialRefCode = self::Prefix_RefCode;
        $query = self::find()->where(['YEAR(created_at)' => $currentYear]);

        $runningNo = $query->count() + 1;
        if (strlen($runningNo) < self::runningNoLength) {
            $runningNo = str_repeat("0", self::runningNoLength - strlen($runningNo)) . $runningNo;
        }

        $refCode = $initialRefCode . $currentYearShort . "/" . $currentMonth . "/" . $runningNo;

        return $refCode;
    }
}
