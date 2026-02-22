<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;

/**
 * This is the model class for table "claim_entitlement_details".
 *
 * @property int $id
 * @property int|null $claim_entitle_id
 * @property string|null $claim_type_code
 * @property int|null $month_start
 * @property int|null $month_end
 * @property float|null $amount
 * @property string|null $remark
 * @property int|null $created_by
 * @property string|null $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $no_limit 0 = no, 1 = yes
 *
 * @property ClaimEntitlement $claimEntitle
 * @property RefClaimType $claimTypeCode
 * @property User $createdBy
 * @property User $updatedBy
 */
class ClaimEntitlementDetails extends \yii\db\ActiveRecord {

    CONST noLimitAmountSts = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_entitlement_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claim_entitle_id', 'month_start', 'month_end', 'created_by', 'updated_by', 'no_limit'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at', 'no_limit'], 'safe'],
            [['claim_type_code'], 'string', 'max' => 10],
            [['remark'], 'string', 'max' => 255],
            [['claim_entitle_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimEntitlement::className(), 'targetAttribute' => ['claim_entitle_id' => 'id']],
            [['claim_type_code'], 'exist', 'skipOnError' => true, 'targetClass' => RefClaimType::className(), 'targetAttribute' => ['claim_type_code' => 'code']],
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
            'claim_entitle_id' => 'Claim Entitle ID',
            'claim_type_code' => 'Claim Type Code',
            'month_start' => 'Month Start',
            'month_end' => 'Month End',
            'amount' => 'Amount',
            'remark' => 'Remark',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_by' => 'Updated By',
            'updated_at' => 'Updated At',
            'no_limit' => 'No Limit',
        ];
    }

    /**
     * Gets query for [[ClaimEntitle]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimEntitle() {
        return $this->hasOne(ClaimEntitlement::className(), ['id' => 'claim_entitle_id']);
    }

    /**
     * Gets query for [[ClaimTypeCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimTypeCode() {
        return $this->hasOne(RefClaimType::className(), ['code' => 'claim_type_code']);
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
}
