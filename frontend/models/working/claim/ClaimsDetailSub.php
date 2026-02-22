<?php

namespace frontend\models\working\claim;

use Yii;
use frontend\models\working\project\MasterProjects;
use frontend\models\working\claim\ClaimsDetail;

/**
 * This is the model class for table "claims_detail_sub".
 *
 * @property int $id
 * @property int $claims_detail_id
 * @property string|null $project_account
 * @property string $detail
 * @property float $amount
 *
 * @property ClaimsDetail $claimsDetail
 * @property MasterProjects $projectAccount
 */
class ClaimsDetailSub extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'claims_detail_sub';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['claims_detail_id', 'detail'], 'required'],
            [['claims_detail_id'], 'integer'],
            [['amount'], 'number'],
            [['project_account'], 'string', 'max' => 30],
            [['detail'], 'string', 'max' => 255],
            [['claims_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClaimsDetail::className(), 'targetAttribute' => ['claims_detail_id' => 'claims_detail_id']],
            [['project_account'], 'exist', 'skipOnError' => true, 'targetClass' => MasterProjects::className(), 'targetAttribute' => ['project_account' => 'project_code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'claims_detail_id' => 'Claims Detail ID',
            'project_account' => 'Project Account',
            'detail' => 'Detail',
            'amount' => 'Amount',
        ];
    }

    /**
     * Gets query for [[ClaimsDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClaimsDetail() {
        return $this->hasOne(ClaimsDetail::className(), ['claims_detail_id' => 'claims_detail_id']);
    }

    /**
     * Gets query for [[ProjectAccount]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectAccount() {
        return $this->hasOne(MasterProjects::className(), ['project_code' => 'project_account']);
    }

    /**     * ***************************** Function
     * Use when copying the items
     */
    public function duplicate($claimsDetailId) {
        $newRecord = new ClaimsDetailSub();

        $newRecord->claims_detail_id = $claimsDetailId;
        $newRecord->project_account = $this->project_account;
        $newRecord->detail = $this->detail;
        $newRecord->amount = $this->amount;
        $newRecord->save();

    }

}
