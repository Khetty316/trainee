<?php

namespace frontend\models\office\pettyCash;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;

/**
 * This is the model class for table "petty_cash_request_master".
 *
 * @property int $id
 * @property string|null $ref_code
 * @property string|null $voucher_no
 * @property int|null $status
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @property int|null $finance_id
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $deletedBy
 * @property RefGeneralStatus $status0
 * @property User $finance
 * @property PettyCashRequestPost[] $pettyCashRequestPosts
 * @property PettyCashRequestPre[] $pettyCashRequestPres
 */
class PettyCashRequestMaster extends \yii\db\ActiveRecord {

    CONST Prefix_RefCode = "PCRF";
    CONST runningNoLength = 2;
    CONST STATUS_APPROVED = 0;
    CONST STATUS_REJECTED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'petty_cash_request_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['status', 'created_by', 'updated_by', 'deleted_by', 'finance_id'], 'integer'],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['ref_code', 'voucher_no'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['deleted_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['finance_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['finance_id' => 'id']],
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
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_at' => 'Submitted At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'deleted_by' => 'Deleted By',
            'deleted_at' => 'Deleted At',
            'finance_id' => 'Finance ID',
        ];
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
     * Gets query for [[PettyCashRequestPosts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPettyCashRequestPosts() {
        return $this->hasMany(PettyCashRequestPost::className(), ['pc_request_master_id' => 'id']);
    }

    /**
     * Gets query for [[PettyCashRequestPres]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPettyCashRequestPres() {
        return $this->hasMany(PettyCashRequestPre::className(), ['pc_request_master_id' => 'id']);
    }

    /**
     * Gets query for [[Finance]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinance() {
        return $this->hasOne(User::className(), ['id' => 'finance_id']);
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
