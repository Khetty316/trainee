<?php

namespace frontend\models\working\mi;

use Yii;
use common\models\User;
use frontend\models\working\project\MasterProjects;
use frontend\models\common\RefCurrencies;
/**
 * This is the model class for table "mi_projects".
 *
 * @property int $id
 * @property int $mi_id
 * @property string|null $project_code
 * @property int|null $requestor
 * @property int|null $requestor_approval NULL = NOT YET REPLY, 0 = DECLINE, 1 = Approved
 * @property float|null $amount
 * @property int $currency_id
 * @property int $active
 * @property string $created_at
 * @property int $created_by
 * @property string $updated_at
 * @property int|null $updated_by
 *
 * @property RefCurrencies $currency
 * @property User $createdBy
 * @property MasterIncomings $mi
 * @property MasterProjects $projectCode
 * @property User $requestor0
 * @property User $updatedBy
 */
class MiProjects extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'mi_projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['mi_id', 'created_by'], 'required'],
            [['mi_id', 'requestor', 'requestor_approval', 'currency_id', 'active', 'created_by', 'updated_by'], 'integer'],
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['project_code'], 'string', 'max' => 20],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => RefCurrencies::className(), 'targetAttribute' => ['currency_id' => 'currency_id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['mi_id'], 'exist', 'skipOnError' => true, 'targetClass' => MasterIncomings::className(), 'targetAttribute' => ['mi_id' => 'id']],
            [['project_code'], 'exist', 'skipOnError' => true, 'targetClass' => MasterProjects::className(), 'targetAttribute' => ['project_code' => 'project_code']],
            [['requestor'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['requestor' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'mi_id' => 'Mi ID',
            'project_code' => 'Project Code',
            'requestor' => 'Requestor',
            'requestor_approval' => 'Requestor Approval',
            'amount' => 'Amount',
            'currency_id' => 'Currency ID',
            'active' => 'Active',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    public function beforeSave($insert) {
//        if ($this->isNewRecord)
//            $this->created_at = new CDbExpression('NOW()');
        if (!$this->isNewRecord) {
            $this->updated_at = new Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }
    
    
    /**
     * Gets query for [[Currency]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCurrency() {
        return $this->hasOne(RefCurrencies::className(), ['currency_id' => 'currency_id']);
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
     * Gets query for [[Mi]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMi() {
        return $this->hasOne(MasterIncomings::className(), ['id' => 'mi_id']);
    }

    /**
     * Gets query for [[ProjectCode]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectCode() {
        return $this->hasOne(MasterProjects::className(), ['project_code' => 'project_code']);
    }

    /**
     * Gets query for [[Requestor0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRequestor0() {
        return $this->hasOne(User::className(), ['id' => 'requestor']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy() {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }

}
