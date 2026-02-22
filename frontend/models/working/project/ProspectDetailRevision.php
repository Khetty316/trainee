<?php

namespace frontend\models\working\project;

use Yii;
use frontend\models\working\project\ProspectDetail;

/**
 * This is the model class for table "prospect_detail_revision".
 *
 * @property int $id
 * @property int|null $prospect_detail_id
 * @property float|null $amount
 * @property int $awarded_sts
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProspectDetail $prospectDetail
 * @property ProspectDetailRevisionScope[] $prospectDetailRevisionScopes
 */
class ProspectDetailRevision extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prospect_detail_revision';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['prospect_detail_id', 'awarded_sts', 'created_by'], 'integer'],
            [['amount'], 'number'],
            [['created_at'], 'safe'],
            [['prospect_detail_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProspectDetail::className(), 'targetAttribute' => ['prospect_detail_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'prospect_detail_id' => 'Prospect Detail ID',
            'amount' => 'Amount',
            'awarded_sts' => 'Awarded Sts',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[ProspectDetail]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetail() {
        return $this->hasOne(ProspectDetail::className(), ['id' => 'prospect_detail_id']);
    }

    /**
     * Gets query for [[ProspectDetailRevisionScopes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProspectDetailRevisionScopes() {
        return $this->hasMany(ProspectDetailRevisionScope::className(), ['prospect_detail_revision_id' => 'id']);
    }

    public function processAndSave($post) {
        $this->prospect_detail_id = $post['prospectDetailId'];
        $this->save();

        foreach ($post['percent'] as $key => $percent) {
            if ($percent > 0) {
                $scope = new ProspectDetailRevisionScope();
                $scope->prospect_detail_revision_id = $this->id;
                $scope->prospect_master_scope_id = $post['scopeId'][$key];
                $scope->scope = $post['scope'][$key];
                $scope->amount = $post['amt'][$key];
                $scope->percentage = $percent;
                $scope->save();
                $this->amount += $scope->amount * $scope->percentage / 100;
            }
        }

        $this->amount = Yii::$app->request->post('totFinAmt');

        return $this->update(false);
    }

    public function processCopyAndSave($post) {

        $prospectDetailId = $post['prospectDetailId'];
        $revisionId = $post['revisionId'];
        $motherRevision = ProspectDetailRevision::findOne($revisionId);

        $this->prospect_detail_id = $prospectDetailId;
        $this->amount = $motherRevision->amount;
        $this->save();

        $scopes = ProspectDetailRevisionScope::find()->where('prospect_detail_revision_id = ' . $revisionId)->all();
        foreach ($scopes as $scope) {

            $newScope = new ProspectDetailRevisionScope();
            $newScope->prospect_detail_revision_id = $this->id;
            $newScope->prospect_master_scope_id = $scope->prospect_master_scope_id;
            $newScope->scope = $scope->scope;
            $newScope->amount = $scope->amount;
            $newScope->percentage = $scope->percentage;
            $newScope->save();
        }

        return true;
    }

    public function setAward() {
        $revisions = ProspectDetailRevision::find()->where("prospect_detail_id=" . $this->prospect_detail_id)->andWhere("awarded_sts=1")->one();
        if ($revisions) {
            return false;
        }
        
        $prospect = $this->prospectDetail;
        $prospect->amount = $this->amount;
        $prospect->is_awarded=1;
        $this->awarded_sts = 1;
        return($this->update() && $prospect->update(false));
    }

}
