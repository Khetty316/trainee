<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "project_production_panel_design_master".
 *
 * @property int $id
 * @property string $sub_folder_name
 * @property string $filename
 * @property string|null $remarks
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanelDesign[] $projectProductionPanelDesigns
 */
class ProjectProductionPanelDesignMaster extends \yii\db\ActiveRecord {

    public $scannedFile;
    public $selectedPanelIds;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_design_master';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['sub_folder_name', 'filename'], 'required'],
            [['remarks'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_by', 'updated_by'], 'integer'],
            [['sub_folder_name', 'filename'], 'string', 'max' => 255],
//            [['scannedFiles'], 'file', 'skipOnEmpty' => false],
            ['scannedFile', 'file', 'maxFiles' => 0, 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sub_folder_name' => 'Sub Folder Name',
            'filename' => 'Filename',
            'remarks' => 'Remarks',
            'scannedFile' => 'Attachments',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjectProductionPanelDesigns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjectProductionPanelDesigns() {
        return $this->hasMany(ProjectProductionPanelDesign::className(), ['design_master_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!$this->isNewRecord) {
            $this->updated_at = new \yii\db\Expression('NOW()');
            $this->updated_by = Yii::$app->user->identity->id;
        } else {
            $this->created_at = new \yii\db\Expression('NOW()');
            $this->created_by = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }

}
