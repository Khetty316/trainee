<?php

namespace frontend\models\projectproduction;

use Yii;

/**
 * This is the model class for table "project_production_documents".
 *
 * @property int $id
 * @property int $project_production_master_id
 * @property string $filename
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionMaster $projectProductionMaster
 * @property ProjectProductionMaster $projectProductionMaster0
 */
class ProjectProductionDocuments extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['project_production_master_id', 'filename'], 'required'],
            [['project_production_master_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['filename'], 'string', 'max' => 255],
            [['project_production_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionMaster::class, 'targetAttribute' => ['project_production_master_id' => 'id']],
            [['project_production_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionMaster::class, 'targetAttribute' => ['project_production_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'project_production_master_id' => 'Project Production Master ID',
            'filename' => 'Filename',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjectProductionMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionMaster() {
        return $this->hasOne(ProjectProductionMaster::class, ['id' => 'project_production_master_id']);
    }

    /**
     * Gets query for [[ProjectProductionMaster0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionMaster0() {
        return $this->hasOne(ProjectProductionMaster::class, ['id' => 'project_production_master_id']);
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_by = Yii::$app->user->id;
            $this->updated_at = new \yii\db\Expression('NOW()');
        }
        return parent::beforeSave($insert);
    }

}
