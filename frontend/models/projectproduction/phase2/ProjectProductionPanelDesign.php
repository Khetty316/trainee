<?php

namespace frontend\models\ProjectProduction;

use Yii;

/**
 * This is the model class for table "project_production_panel_design".
 *
 * @property int $id
 * @property int $proj_prod_panel_id
 * @property int $design_master_id
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanels $projProdPanel
 * @property ProjectProductionPanelDesignMaster $designMaster
 */
class ProjectProductionPanelDesign extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'project_production_panel_design';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'design_master_id'], 'required'],
            [['proj_prod_panel_id', 'design_master_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['design_master_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanelDesignMaster::className(), 'targetAttribute' => ['design_master_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'design_master_id' => 'Design Master ID',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'updated_at' => 'Updated At',
            'updated_by' => 'Updated By',
        ];
    }

    /**
     * Gets query for [[ProjProdPanel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProjProdPanel() {
        return $this->hasOne(ProjectProductionPanels::className(), ['id' => 'proj_prod_panel_id']);
    }

    /**
     * Gets query for [[DesignMaster]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDesignMaster() {
        return $this->hasOne(ProjectProductionPanelDesignMaster::className(), ['id' => 'design_master_id']);
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
