<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\client\Clients;

/**
 * This is the model class for table "project_q_clients".
 *
 * @property int $id
 * @property int $project_q_master_id Auto-generated
 * @property int $client_id
 * @property string|null $remark
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property Clients $client
 * @property ProjectQMasters $projectQMaster
 * @property QuotationPdfMasters[] $quotationPdfMasters
 */
class ProjectQClients extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_q_master_id', 'client_id'], 'required'],
            [['project_q_master_id', 'client_id', 'created_by'], 'integer'],
            [['remark'], 'string'],
            [['created_at'], 'safe'],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['project_q_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQMasters::className(), 'targetAttribute' => ['project_q_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_q_master_id' => 'Project Q Master ID',
            'client_id' => 'Client ID',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient() {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[ProjectQMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQMaster() {
        return $this->hasOne(ProjectQMasters::className(), ['id' => 'project_q_master_id']);
    }

    /**
     * Gets query for [[QuotationPdfMasters]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotationPdfMasters() {
        return $this->hasMany(QuotationPdfMasters::className(), ['project_q_client_id' => 'id']);
    }

    public function processNewClients($clientId, $projectQId) {
        $alreadyExists = ProjectQClients::find()->where(['project_q_master_id' => $projectQId, 'client_id' => $clientId])->all();
        if ($alreadyExists) {
            return false;
        }


        return $this->newClients($clientId, $projectQId);
    }

    public function newClients($clientId, $projectQId) {
        $this->client_id = $clientId;
        $this->project_q_master_id = $projectQId;
        $this->created_by = Yii::$app->user->id;
        return $this->save();
    }

}
