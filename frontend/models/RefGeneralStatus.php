<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ref_general_status".
 *
 * @property int $id
 * @property string $status_code
 * @property string $status_name
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property PrereqFormMaster[] $prereqFormMasters
 * @property PrereqFormStatusTrail[] $prereqFormStatusTrails
 */
class RefGeneralStatus extends \yii\db\ActiveRecord {

    CONST STATUS_GetSuperiorApproval = 1;
    CONST STATUS_GetDirectorApproval = 2;
    CONST STATUS_GetFinanceApproval = 3;
    CONST STATUS_Approved = 4;
    CONST STATUS_SuperiorRejected = 5;
    CONST STATUS_DirectorRejected = 6;
    CONST STATUS_FinanceRejected = 7;
    CONST STATUS_GetHrAcknowledgement = 8;
    CONST STATUS_HrRejected = 9;
    CONST STATUS_WaitingForPayment = 10;
    CONST STATUS_Paid = 11;
    CONST STATUS_ClaimantCancelClaim = 12;
    CONST STATUS_Completed = 13;
    CONST STATUS_PendingSupportedDocument = 14;
    CONST STATUS_WaitingForReceiptReturn = 15;
    CONST STATUS_WaitingForReceiptVerification = 16;
    CONST STATUS_WaitingForCashRelease = 17;
    
    CONST STATUS_Complete = [
        self::STATUS_Approved,
        self::STATUS_SuperiorRejected,
        self::STATUS_DirectorRejected,
        self::STATUS_FinanceRejected,
        self::STATUS_HrRejected,
        self::STATUS_Paid,
        self::STATUS_Completed
    ];
    
    CONST STATUS_Pending = [
        self::STATUS_GetSuperiorApproval,
        self::STATUS_GetDirectorApproval,
        self::STATUS_GetFinanceApproval,
        self::STATUS_GetHrAcknowledgement,
        self::STATUS_WaitingForPayment,
        self::STATUS_PendingSupportedDocument,
        self::STATUS_WaitingForReceiptReturn,
        self::STATUS_WaitingForReceiptVerification,
        self::STATUS_WaitingForCashRelease,
    ];
    
    CONST STATUS_Pending_Finance = [
        self::STATUS_GetFinanceApproval,
        self::STATUS_WaitingForPayment,
        self::STATUS_WaitingForReceiptVerification,
        self::STATUS_WaitingForCashRelease,
        self::STATUS_PendingSupportedDocument,
    ];
    
    //for petty cash
    

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'ref_general_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['status_code', 'status_name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['status_code', 'status_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'status_code' => 'Status Code',
            'status_name' => 'Status Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[PrereqFormMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormMasters() {
        return $this->hasMany(PrereqFormMaster::className(), ['status' => 'id']);
    }

    /**
     * Gets query for [[PrereqFormStatusTrails]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrereqFormStatusTrails() {
        return $this->hasMany(PrereqFormStatusTrail::className(), ['status' => 'id']);
    }
}
