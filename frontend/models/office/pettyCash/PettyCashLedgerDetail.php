<?php

namespace frontend\models\office\pettyCash;

use Yii;
use common\models\User;

/**
 * This is the model class for table "petty_cash_ledger_detail".
 *
 * @property int $id
 * @property int|null $pc_ledger_master_id
 * @property string|null $date
 * @property string|null $voucher_no
 * @property string|null $ref_1
 * @property string|null $ref_2
 * @property string|null $description
 * @property float|null $debit
 * @property float|null $credit
 * @property float|null $balance
 * @property int|null $created_by
 * @property string|null $created_at
 *
 * @property PettyCashLedgerMaster $pcLedgerMaster
 * @property User $createdBy
 */
class PettyCashLedgerDetail extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'petty_cash_ledger_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['pc_ledger_master_id', 'created_by'], 'integer'],
            [['date', 'created_at'], 'safe'],
            [['debit', 'credit', 'balance'], 'number'],
            [['voucher_no', 'ref_1', 'ref_2', 'description'], 'string', 'max' => 255],
            [['pc_ledger_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => PettyCashLedgerMaster::className(), 'targetAttribute' => ['pc_ledger_master_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'pc_ledger_master_id' => 'Pc Ledger Master ID',
            'date' => 'Date',
            'voucher_no' => 'Voucher No',
            'ref_1' => 'Reference 1',
            'ref_2' => 'Reference 2',
            'description' => 'Description',
            'debit' => 'Debit Amount (RM)',
            'credit' => 'Credit Amount(RM)',
            'balance' => 'Balance (RM)',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[PcLedgerMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPcLedgerMaster() {
        return $this->hasOne(PettyCashLedgerMaster::className(), ['id' => 'pc_ledger_master_id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->created_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }
}
