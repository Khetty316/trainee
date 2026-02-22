<?php

namespace frontend\models\working\project;

use Yii;
use common\models\User;

/**
 * This is the model class for table "project_closing".
 *
 * @property int $id
 * @property int $project_id
 * @property int $cpc_status
 * @property float|null $cpc_amount
 * @property string|null $cpc_date
 * @property int|null $cpc_by
 * @property int $cmgd_status
 * @property float|null $cmgd_amount
 * @property string|null $cmgd_date
 * @property int|null $cmgd_by
 * @property int $final_acc_status
 * @property float|null $final_acc_amount
 * @property string|null $final_acc_date
 * @property int|null $final_acc_by
 * @property int $pay_rec_status
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property User $cmgdBy
 * @property User $cpcBy
 * @property User $finalAccBy
 * @property ProjectMaster $project
 */
class ProjectClosing extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_closing';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_id'], 'required'],
            [['project_id', 'cpc_status', 'cpc_by', 'cmgd_status', 'cmgd_by', 'final_acc_status', 'final_acc_by', 'pay_rec_status', 'created_by'], 'integer'],
            [['cpc_amount', 'cmgd_amount', 'final_acc_amount'], 'number'],
            [['cpc_date', 'cmgd_date', 'final_acc_date', 'created_at'], 'safe'],
            [['cmgd_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cmgd_by' => 'id']],
            [['cpc_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cpc_by' => 'id']],
            [['final_acc_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['final_acc_by' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectMaster::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'cpc_status' => 'Cpc Status',
            'cpc_amount' => 'Cpc Amount',
            'cpc_date' => 'Cpc Date',
            'cpc_by' => 'Cpc By',
            'cmgd_status' => 'Cmgd Status',
            'cmgd_amount' => 'Cmgd Amount',
            'cmgd_date' => 'Cmgd Date',
            'cmgd_by' => 'Cmgd By',
            'final_acc_status' => 'Final Acc Status',
            'final_acc_amount' => 'Final Acc Amount',
            'final_acc_date' => 'Final Acc Date',
            'final_acc_by' => 'Final Acc By',
            'pay_rec_status' => 'Pay Rec Status',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[CmgdBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmgdBy() {
        return $this->hasOne(User::className(), ['id' => 'cmgd_by']);
    }

    /**
     * Gets query for [[CpcBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCpcBy() {
        return $this->hasOne(User::className(), ['id' => 'cpc_by']);
    }

    /**
     * Gets query for [[FinalAccBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinalAccBy() {
        return $this->hasOne(User::className(), ['id' => 'final_acc_by']);
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(ProjectMaster::className(), ['id' => 'project_id']);
    }
    
    
    public function initiate($projId){
        $this->project_id=$projId;
        $this->cpc_status=0;
        $this->cmgd_status=0;
        $this->final_acc_status=0;
        $this->pay_rec_status=0;
        $this->save();
    }
    


}
