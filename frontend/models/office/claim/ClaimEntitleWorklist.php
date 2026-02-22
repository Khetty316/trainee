<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;

/**
 * This is the model class for table "claim_entitle_worklist".
 *
 * @property int $id
 * @property int|null $claim_entitle_id
 * @property int|null $claim_entitle_status
 * @property int|null $responsed_by
 * @property string|null $remark
 * @property string|null $created_at
 *
 * @property ClaimEntitlement $claimEntitle
 * @property User $responsedBy
 * @property RefGeneralStatus $claimEntitleStatus
 */
class ClaimEntitleWorklist extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_entitle_worklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claim_entitle_id', 'claim_entitle_status', 'responsed_by'], 'integer'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['claim_entitle_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimEntitlement::className(), 'targetAttribute' => ['claim_entitle_id' => 'id']],
            [['responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responsed_by' => 'id']],
            [['claim_entitle_status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['claim_entitle_status' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'claim_entitle_id' => 'Claim Entitle ID',
            'claim_entitle_status' => 'Claim Entitle Status',
            'responsed_by' => 'Responsed By',
            'remark' => 'Remark',
            'created_at' => 'Created At',
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
     * Gets query for [[ResponsedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsedBy() {
        return $this->hasOne(User::className(), ['id' => 'responsed_by']);
    }

    /**
     * Gets query for [[ClaimEntitleStatus]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimEntitleStatus() {
        return $this->hasOne(RefGeneralStatus::className(), ['id' => 'claim_entitle_status']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');

        return parent::beforeSave($insert);
    }
}
