<?php

namespace frontend\models\working\project;

use Yii;
use common\models\myTools\MyFormatter;
/**
 * This is the model class for table "project_progress_claim_pay".
 *
 * @property int $id
 * @property int $progress_claim_id
 * @property float $amount
 * @property string|null $pay_date
 * @property string $created_at
 * @property int $created_by
 *
 * @property ProjectProgressClaim $progressClaim
 */
class ProjectProgressClaimPay extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_progress_claim_pay';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['progress_claim_id', 'amount', 'created_by'], 'required'],
            [['progress_claim_id', 'created_by'], 'integer'],
            [['amount'], 'number'],
            [['pay_date', 'created_at'], 'safe'],
            [['progress_claim_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProgressClaim::className(), 'targetAttribute' => ['progress_claim_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'progress_claim_id' => 'Progress Claim ID',
            'amount' => 'Amount',
            'pay_date' => 'Pay Date',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[ProgressClaim]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgressClaim() {
        return $this->hasOne(ProjectProgressClaim::className(), ['id' => 'progress_claim_id']);
    }

    public function processAndSave() {
        $this->created_by = Yii::$app->user->id;
        if ($this->pay_date) {
            $this->pay_date = MyFormatter::changeDateFormat_readToDB($this->pay_date);
        }
        
        return $this->save();
    }

}
