<?php

namespace frontend\models\projectquotation;

use Yii;
use frontend\models\common\RefProjectQTypes;
use frontend\models\ProjectProduction\ProjectProductionMaster;
/**
 * This is the model class for table "project_q_types".
 *
 * @property int $id
 * @property int $project_id
 * @property string $type
 * @property string|null $remark
 * @property int $is_finalized
 * @property int|null $active_revision_id
 * @property int|null $active_client_id
 * @property int|null $proj_prod_id
 * @property string $created_at
 * @property int|null $created_by
 *
 * @property ProjectQRevisions[] $projectQRevisions
 * @property ProjectQMasters $project
 * @property ProjectQRevisions $activeRevision
 * @property RefProjectQTypes $type0
 * @property ProjectQClients $activeClient
 * @property ProjectProductionMaster $projProd
 */
class ProjectQTypes extends \yii\db\ActiveRecord {

    public $attachments;

    const TYPE_AUTOMATION = "auto";
    const TYPE_LV = "lv";
    const TYPE_MECHANICAL = "mech";

    public $scannedFile;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_q_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_id', 'type'], 'required'],
            [['project_id', 'is_finalized', 'active_revision_id', 'active_client_id', 'proj_prod_id', 'created_by'], 'integer'],
            [['remark'], 'string'],
            [['created_at'], 'safe'],
            [['type'], 'string', 'max' => 10],
            [['project_id', 'type'], 'unique', 'targetAttribute' => ['project_id', 'type']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQMasters::className(), 'targetAttribute' => ['project_id' => 'id']],
            [['active_revision_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQRevisions::className(), 'targetAttribute' => ['active_revision_id' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::className(), 'targetAttribute' => ['type' => 'code']],
            [['active_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectQClients::className(), 'targetAttribute' => ['active_client_id' => 'id']],
            [['proj_prod_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionMaster::className(), 'targetAttribute' => ['proj_prod_id' => 'id']],
            [['scannedFile'], 'file', 'skipOnEmpty' => true],
            ['scannedFile', 'file', 'extensions' => "png, jpg, jpeg, pdf", 'maxSize' => Yii::$app->params['maxSize'], 'tooBig' => Yii::$app->params['tooBigMsg'], 'checkExtensionByMimeType' => false],
            // additional change here
            [['attachments'], 'file', 'extensions' => 'pdf', 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'type' => 'Type',
            'remark' => 'Remark',
            'is_finalized' => 'Confirmed',
            'active_revision_id' => 'Active Revision ID',
            'active_client_id' => 'Active Client ID',
            'proj_prod_id' => 'Project Production ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
        ];
    }

    public function getProjectQTypesAttachments() {
        return $this->hasMany(ProjectQTypesAttachments::className(), ['proj_q_type_id' => 'id']);
    }

    /**
     * Gets query for [[ProjectQRevisions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectQRevisions() {
        return $this->hasMany(ProjectQRevisions::className(), ['project_q_type_id' => 'id']);
    }

    /**
     * Gets query for [[Project]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(ProjectQMasters::className(), ['id' => 'project_id']);
    }

    /**
     * Gets query for [[ActiveRevision]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveRevision() {
        return $this->hasOne(ProjectQRevisions::className(), ['id' => 'active_revision_id']);
    }

    /**
     * Gets query for [[Type0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getType0() {
        return $this->hasOne(RefProjectQTypes::className(), ['code' => 'type']);
    }

    /**
     * Gets query for [[ActiveClient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveClient() {
        return $this->hasOne(ProjectQClients::className(), ['id' => 'active_client_id']);
    }

    /**
     * Gets query for [[ProjProd]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProd() {
        return $this->hasOne(ProjectProductionMaster::className(), ['id' => 'proj_prod_id']);
    }

    public static function checkCreateAndGet($projectId, $type) {
        $projectQType = ProjectQTypes::find()->where(['project_id' => $projectId, 'type' => $type])->one();
        if (!$projectQType) {
            $projectQType = new ProjectQTypes();
            $projectQType->project_id = $projectId;
            $projectQType->type = $type;
            $projectQType->save();
        }

        return $projectQType;
    }

    public function confirmOrder() {

        if (!$this->activeRevision || !$this->activeClient) {
            return false;
        }
        $this->is_finalized = 1;
        return $this->update();
    }

}
