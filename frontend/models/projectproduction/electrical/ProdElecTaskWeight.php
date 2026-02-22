<?php

namespace frontend\models\projectproduction\electrical;

use Yii;
use frontend\models\common\RefProjectQTypes;
use frontend\models\ProjectProduction\ProjectProductionPanels;
use frontend\models\ProjectProduction\electrical\RefProjProdTaskElec;
use frontend\models\ProjectProduction\electrical\ProductionElecTasks;

/**
 * This is the model class for table "prod_elec_task_weight".
 *
 * @property int $id
 * @property int|null $proj_prod_panel_id
 * @property string|null $panel_type
 * @property float|null $panel_type_weight
 * @property float|null $busbar
 * @property float|null $mount
 * @property float|null $test
 * @property float|null $wire
 * @property string|null $created_at
 * @property int|null $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 *
 * @property ProjectProductionPanels $projProdPanel
 * @property RefProjectQTypes $panelType
 */
class ProdElecTaskWeight extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'prod_elec_task_weight';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['proj_prod_panel_id', 'created_by', 'updated_by'], 'integer'],
            [['panel_type_weight', 'busbar', 'mount', 'test', 'wire'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['panel_type'], 'string', 'max' => 10],
            [['proj_prod_panel_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectProductionPanels::className(), 'targetAttribute' => ['proj_prod_panel_id' => 'id']],
            [['panel_type'], 'exist', 'skipOnError' => true, 'targetClass' => RefProjectQTypes::className(), 'targetAttribute' => ['panel_type' => 'code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'proj_prod_panel_id' => 'Proj Prod Panel ID',
            'panel_type' => 'Panel Type',
            'panel_type_weight' => 'Panel Type Weight',
            'busbar' => 'Busbar',
            'mount' => 'Mount',
            'test' => 'Test',
            'wire' => 'Wire',
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
     * Gets query for [[PanelType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPanelType() {
        return $this->hasOne(RefProjectQTypes::className(), ['code' => 'panel_type']);
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

    /**
     * Save default value for both panel and task weights
     */
    public function saveDefaultElecPanelTaskWeight($panelId, $taskCode) {
        $panel = ProjectProductionPanels::findOne($panelId);
        if (!$panel) {
            return false;
        }

        $panelType = $panel->panelType;
        $taskWeight = RefProjProdTaskElec::findOne($taskCode);

        $model = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
        if (!$model) {
            $model = new ProdElecTaskWeight();
        }

        if ($model->getAttribute($taskCode) === null) {
            $model->setAttribute($taskCode, $taskWeight->weight);
        }
        $model->proj_prod_panel_id = $panelId;
        $model->panel_type = $panelType->code;
        $model->panel_type_weight = $panelType->elec_dept_percentage;
        $model->save();

        return true;
    }

    /**
     * Updates panel weight
     * @param array $postData containing panel weights.
     * @return bool True if all weights are updated successfully, false otherwise.
     */
    public function updateElecPanelWeight($postData) {
        foreach ($postData['ProdElecPanelWeight'] as $panelId => $data) {
            $elecTaskWeight = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
            if (!$elecTaskWeight) {
                $elecTaskWeight = new ProdElecTaskWeight();
                $elecTaskWeight->proj_prod_panel_id = $panelId;
            }
            foreach ($data as $attribute => $value) {
                $elecTaskWeight->panel_type_weight = $value['weight'];
            }
            if (!$elecTaskWeight->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Updates task weight 
     * @param type $postData containing task weights.
     */
    public function updateElecTaskWeight($postData) {
        if (isset($postData['ProdElecTaskWeight'])) {
            foreach ($postData['ProdElecTaskWeight'] as $panelId => $data) {
                $elecTask = ProductionElecTasks::find()->where(['proj_prod_panel_id' => $panelId])->all();
                $elecTaskWeight = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
                foreach ($data as $taskCode => $value) {
                    foreach ($elecTask as $elec) {
                        if ($taskCode === $elec['elec_task_code']) {
                            $elecTaskWeight->{$taskCode} = $value['weight'];
                        }
                    }
                    if (!$elecTaskWeight->save()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Updates the task weight to null after the task has been deleted
     */
    public function updateElecTaskWeightAfterDeleteTask($panelId, $elecTaskCode) {
        $elecTaskWeights = ProdElecTaskWeight::find()->where(['proj_prod_panel_id' => $panelId])->one();
        if (!$elecTaskWeights) {
            return false;
        }

        $elecTaskWeights->{$elecTaskCode} = null;
        $elecTaskWeights->save();
        return true;
    }

    public function getTaskCompletionPercentage($panelDetail) {
        $taskCodes = RefProjProdTaskElec::find()->select(['code'])->asArray()->all();
        $totalAllTaskWeight = 0;
        $totalCompletedTaskWeight = 0;

        $amount = $panelDetail['amount'];
        $getWeights = self::find()->where(['proj_prod_panel_id' => $panelDetail['project_production_panel_id']])->one();

        if ($getWeights) {
            foreach ($taskCodes as $key => $detail) {
                foreach ($detail as $attribute => $taskCode) {
                    if (isset($getWeights->{$taskCode})) {
                        $totalAllTaskWeight += $getWeights->{$taskCode};
                    }
                }
            }

            if (isset($getWeights->{$panelDetail['task_code']})) {
                $completedTaskWeight = $getWeights->{$panelDetail['task_code']};
                $totalCompletedTaskWeight = $amount * $completedTaskWeight;
            }
        }

        $percentage = ($totalAllTaskWeight > 0) ? ($totalCompletedTaskWeight / $totalAllTaskWeight) * 100 : 0;

        return $percentage;
    }
}
