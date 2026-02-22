<?php

namespace frontend\models\office\claim;

use Yii;
use common\models\User;
use frontend\models\RefGeneralStatus;

/**
 * This is the model class for table "claim_approval_worklist".
 *
 * @property int $id
 * @property int|null $module 1 = superior, 2 = finance
 * @property int|null $claim_master_id
 * @property int|null $claim_detail_id
 * @property int|null $claim_status 0 = approved, 1 = rejected
 * @property int|null $status
 * @property int|null $responsed_by
 * @property string|null $remark
 * @property string|null $created_at
 *
 * @property ClaimMaster $claimMaster
 * @property ClaimDetail $claimDetail
 * @property User $responsedBy
 * @property RefGeneralStatus $status0
 */
class ClaimApprovalWorklist extends \yii\db\ActiveRecord {

    public $scannedFile;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claim_approval_worklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['module', 'claim_master_id', 'claim_detail_id', 'claim_status', 'status', 'responsed_by'], 'integer'],
            [['created_at'], 'safe'],
            [['remark'], 'string', 'max' => 255],
            [['claim_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimMaster::className(), 'targetAttribute' => ['claim_master_id' => 'id']],
            [['claim_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimDetail::className(), 'targetAttribute' => ['claim_detail_id' => 'id']],
            [['responsed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['responsed_by' => 'id']],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => RefGeneralStatus::className(), 'targetAttribute' => ['status' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => "png, jpg, jpeg, pdf", 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'checkExtensionByMimeType' => false],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'module' => 'Module',
            'claim_master_id' => 'Claim Master ID',
            'claim_detail_id' => 'Claim Detail ID',
            'claim_status' => 'Claim Status',
            'status' => 'Status',
            'responsed_by' => 'Responsed By',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'scannedFile' => 'Payment Proof'
        ];
    }

    /**
     * Gets query for [[ClaimMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimMaster() {
        return $this->hasOne(ClaimMaster::className(), ['id' => 'claim_master_id']);
    }

    /**
     * Gets query for [[ClaimDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimDetail() {
        return $this->hasOne(ClaimDetail::className(), ['id' => 'claim_detail_id']);
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
     * Gets query for [[Status0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus0() {
        return $this->hasOne(RefGeneralStatus::className(), ['id' => 'status']);
    }

    public function beforeSave($insert) {
        $this->created_at = new \yii\db\Expression('NOW()');
        $this->responsed_by = Yii::$app->user->identity->id;

        return parent::beforeSave($insert);
    }

    public function savePaymentProof($uploadedFile, $claimCode) {
            $uploadPath = Yii::getAlias('@frontend/uploads/claim/');

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            // Create unique filename
            $fileName = 'Payment_' . $claimCode . '_' . uniqid() . '.' . $uploadedFile->extension;
            $filePath = $uploadPath . $fileName;

            if ($uploadedFile->saveAs($filePath)) {
                return $fileName;
            }
    }
}
