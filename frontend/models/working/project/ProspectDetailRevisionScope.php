<?php

namespace frontend\models\working\project;

use Yii;
use frontend\models\working\project\ProspectDetailRevision;
use frontend\models\working\project\ProspectMasterScope;

/**
 * This is the model class for table "prospect_detail_revision_scope".
 *
 * @property int $id
 * @property int $prospect_detail_revision_id
 * @property int|null $prospect_master_scope_id
 * @property string|null $scope
 * @property float|null $amount
 * @property float|null $percentage
 * @property string $created_at
 *
 * @property ProspectDetailRevision $prospectDetailRevision
 * @property ProspectMasterScope $prospectMasterScope
 */
class ProspectDetailRevisionScope extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prospect_detail_revision_scope';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prospect_detail_revision_id'], 'required'],
            [['prospect_detail_revision_id', 'prospect_master_scope_id'], 'integer'],
            [['amount', 'percentage'], 'number'],
            [['created_at'], 'safe'],
            [['scope'], 'string', 'max' => 255],
            [['prospect_detail_revision_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProspectDetailRevision::className(), 'targetAttribute' => ['prospect_detail_revision_id' => 'id']],
            [['prospect_master_scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProspectMasterScope::className(), 'targetAttribute' => ['prospect_master_scope_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prospect_detail_revision_id' => 'Prospect Detail Revision ID',
            'prospect_master_scope_id' => 'Prospect Master Scope ID',
            'scope' => 'Scope',
            'amount' => 'Amount',
            'percentage' => 'Percentage',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[ProspectDetailRevision]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetailRevision() {
        return $this->hasOne(ProspectDetailRevision::className(), ['id' => 'prospect_detail_revision_id']);
    }

    /**
     * Gets query for [[ProspectMasterScope]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectMasterScope() {
        return $this->hasOne(ProspectMasterScope::className(), ['id' => 'prospect_master_scope_id']);
    }

}
